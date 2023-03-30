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
 * This is the model class for table "ophciexamination_workflow".
 *
 * @property int $id
 * @property string $name
 * @property int $institution_id
 * @property \Institution $institution
 * @property OphCiExamination_ElementSet[] $steps
 * @property OphCiExamination_ElementSet $first_step
 */
class OphCiExamination_Workflow extends \BaseActiveRecordVersioned
{
    use \OwnedByReferenceData;
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_workflow';
    }

    public function defaultScope()
    {
        return ['order' => $this->getTableAlias(true, false).'.name'];
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['institution_id', 'anyTenantedOrNullInstitutionValidator'],
            ['institution_id', 'onlyCurrentInstitutionValidator', 'except' => 'installationAdminSave'],
            ['institution_id', 'cannotChangeInstitutionIfRulesExistValidator'],
            ['id, name, institution_id', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
                'steps' => [
                    self::HAS_MANY, OphCiExamination_ElementSet::class, 'workflow_id',
                    'order' => 'position'
                ],
                'first_step' => [
                    self::HAS_ONE, OphCiExamination_ElementSet::class, 'workflow_id',
                    'order' => 'first_step.position, first_step.id',
                    'condition' => 'is_active = 1'
                ],
                'active_steps' => [
                    self::HAS_MANY, OphCiExamination_ElementSet::class, 'workflow_id',
                    'order' => 'position',
                    'condition' => 'is_active = 1'
                ],
                'institution' => [self::BELONGS_TO, \Institution::class, 'institution_id'],
                'workflow_rules' => [self::HAS_MANY, OphCiExamination_Workflow_Rule::class, 'workflow_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'institution_id' => 'Institution',
        ];
    }

    public function anyTenantedOrNullInstitutionValidator($attribute, $params)
    {
        if ($this->institution && $this->institution->getPrimaryKey() != $this->institution_id) {
            // ensure we're validating the correct property
            $this->institution = \Institution::model()->findByPk($this->institution_id);
        }
        if ($this->institution !== null && !$this->institution->isTenanted()) {
            $this->addError('institution_id', 'The selected institution is not tenanted');
        }
    }

    public function onlyCurrentInstitutionValidator($attribute, $params)
    {
        if ($this->institution_id !== \Institution::model()->getCurrent()->id) {
            $this->addError('institution_id', 'The selected institution cannot be chosen for the workflow by the user');
        }
    }

    public function cannotChangeInstitutionIfRulesExistValidator($attribute, $params)
    {
        if ($this->isAttributeDirty('institution_id') && !$this->canChangeInstitution()) {
            $this->addError('institution_id', 'Cannot change the workflow\'s institution while rules are associated with it');
        }
    }

    /**
     * First step (set) in this workflow.
     *
     * @return OphCiExamination_ElementSet
     * @throws \SystemException
     */
    public function getFirstStep()
    {
        if (!$this->first_step) {
            throw new \SystemException("Incomplete workflow '$this->name' has no steps configured");
        }
        return $this->first_step;
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('institution_id', $this->institution_id, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * canChangeInstitution
     *
     * If a workflow has any rules, then some of those rules might have a firm associated with them.
     * Since those firms could be associated with a specific institution, which will be the same institution
     * as the worklow, allowing that workflow to change its institution once it has rules will be problematic
     * as the the firms for those rules will suddenly have a different institution and will not work correctly.
     *
     * @return bool
    */
    public function canChangeInstitution(): bool
    {
        return count($this->workflow_rules) === 0;
    }

    protected function getSupportedLevelMask(): int
    {
        return \ReferenceData::LEVEL_INSTALLATION |
            \ReferenceData::LEVEL_INSTITUTION;
    }
}
