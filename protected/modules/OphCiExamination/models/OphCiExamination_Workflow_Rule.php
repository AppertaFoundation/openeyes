<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for rules that apply workflows,
 * where the appropriate firm/subspecialty/episode status applies.
 *
 * @property integer $id
 * @property integer $workflow_id
 * @property integer $firm_id
 * @property integer $subspecialty_id
 * @property integer $episode_status_id
 * @property OphCiExamination_Workflow $workflow
 * @property Firm $firm
 * @property Subspecialty $subspecialty
 * @property EpisodeStatus $episode_status
 */
class OphCiExamination_Workflow_Rule extends \BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_workflow_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['subspecialty_id, firm_id, episode_status_id, workflow_id', 'safe'],
            ['id', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'workflow' => [self::BELONGS_TO, OphCiExamination_Workflow::class, 'workflow_id'],
            'subspecialty' => [self::BELONGS_TO, \Subspecialty::class, 'subspecialty_id'],
            'firm' => [self::BELONGS_TO, \Firm::class, 'firm_id'],
            'episode_status' => [self::BELONGS_TO, \EpisodeStatus::class, 'episode_status_id'],
        ];
    }

    /**
     * Finds the best matching workflow.
     *
     * @param int $firm_id
     * @param int $status_id
     *
     * @return OphCiExamination_Workflow
     */
    public function findWorkflow($firm_id, $status_id)
    {
        $subspecialty_id = null;
        $firm = \Firm::model()->findByPk($firm_id);

        if ($firm) {
            $subspecialty_id = ($firm->serviceSubspecialtyAssignment) ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;
        }

        $rule = self::model()->find('subspecialty_id=? and firm_id=? and episode_status_id=?', array($subspecialty_id, $firm_id, $status_id));
        if ($rule) {
            return $rule->workflow;
        }

        $rule = self::model()->find('subspecialty_id=? and episode_status_id=?', array($subspecialty_id, $status_id));
        if ($rule) {
            return $rule->workflow;
        }

        $rule = self::model()->find('subspecialty_id=?', array($subspecialty_id));
        if ($rule) {
            return $rule->workflow;
        }

        $rule = self::model()->find('subspecialty_id is null and episode_status_id is null');
        if ($rule) {
            return $rule->workflow;
        }

        throw new \CException('Cannot find default workflow rule');
    }

    /**
     * @param $institution_id
     * @param $firm_id
     * @param $status_id
     *
     * @return mixed|null
     *
     * @throws \CException
     */
    public function findWorkflowCascading($firm_id, $status_id)
    {
        $firm = $firm_id instanceof \Firm ? $firm_id : \Firm::model()->findByPk($firm_id);
        $subspecialty_id = ($firm->serviceSubspecialtyAssignment) ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

        $criteria = new \CDbCriteria();
        $criteria->addCondition('(firm_id = :firm_id OR firm_id IS NULL) AND (workflow.institution_id = :institution_id OR workflow.institution_id IS NULL)');
        $criteria->order = 'firm_id DESC, episode_status_id DESC, subspecialty_id DESC';
        $criteria->params = [':firm_id' => $firm->id, ':institution_id' => $firm->institution_id];
        $criteria->with = ['workflow'];

        $workflows = self::model()->with('workflow.active_steps')->findAll($criteria);

        if (!$workflows) {
            throw new \CException('Cannot find any workflow rules');
        }

        $workflow = null;

        foreach ($workflows as $possibleWorkflow) {
            //episode and speciality must match what we have or be null, if that's not the case continue
            if (
                !($possibleWorkflow->episode_status_id == $status_id || !$possibleWorkflow->episode_status_id) ||
                !($possibleWorkflow->subspecialty_id == $subspecialty_id || !$possibleWorkflow->subspecialty_id)
            ) {
                continue;
            }

            if ($possibleWorkflow->episode_status_id == $status_id) { //If the episode status matches return it
                $workflow = $possibleWorkflow->workflow;
                break;
            } elseif ($possibleWorkflow->subspecialty_id === $subspecialty_id) { //Otherwise the subspeciality should match
                $workflow = $possibleWorkflow->workflow;
                break;
            } elseif (!$possibleWorkflow->episode_status_id && !$possibleWorkflow->subspecialty_id) { //else we take the one where everything is null
                $workflow = $possibleWorkflow->workflow;
                break;
            }
        }

        if (!$workflow) {
            throw new \CException('Cannot find default workflow rule');
        }

        return $workflow;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'subspecialty_id' => 'Subspecialty',
            'firm_id' => \Firm::contextLabel(),
            'episode_status_id' => 'Episode status',
            'workflow_id' => 'Workflow',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function beforeValidate()
    {
        $whereParams = array();

        if ($this->id) {
            $where = 'id != :id and ';
            $whereParams[':id'] = $this->id;
        } else {
            $where = '';
        }

        if (!$this->subspecialty_id) {
            $where .= ' subspecialty_id is null and ';
        } else {
            $where .= ' subspecialty_id = :subspecialty_id and ';
            $whereParams[':subspecialty_id'] = $this->subspecialty_id;
        }

        if (!$this->episode_status_id) {
            $where .= ' episode_status_id is null';
        } else {
            $where .= ' episode_status_id = :episode_status_id';
            $whereParams[':episode_status_id'] = $this->episode_status_id;
        }

        if (self::model()->find($where, $whereParams)) {
            //$this->addError('id','There is already a rule for this subspecialty and episode status combination');
        }

        return parent::beforeValidate();
    }

    public function findWorkflowSteps($institution_id, $episode_status_id)
    {
        $firms = \Firm::model()->with('serviceSubspecialtyAssignment')->findAll('institution_id = :institution_id', array(':institution_id' => $institution_id));
        $workflowSteps = [];

        foreach ($firms as $firm) {
            $workflow = self::model()->findWorkflowCascading($firm, $episode_status_id);
            $workflowSteps[$firm->id] = $workflow ? $workflow->active_steps : null;
        }

        return $workflowSteps;
    }
}
