<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use \RRule\RRule;
namespace OEModule\OphCiExamination\models;
use Period;

/**
 * This is the model class for table "ophciexamination_clinicoutcome_entry".
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $element_id
 * @property int $status_id
 * @property int $risk_status_id
 * @property int $followup_quantity
 * @property int $followup_period_id
 * @property string $followup_comments
 * @property int $role_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * @property Element_OphCiExamination_ClinicOutcome $element
 * @property OphCiExamination_ClinicOutcome_Status $status
 * @property Period $followupPeriod
 * @property OphCiExamination_ClinicOutcome_Role $role
 * @property OphCiExamination_ClinicOutcome_Risk_Status $risk_status
 */
class ClinicOutcomeEntry extends \BaseElement
{
    /**
     * Returns the static model of the specified AR class.
     * @return ClinicOutcomeEntry static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_clinicoutcome_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, element_id, status_id, followup_quantity, followup_period_id, role_id, followup_comments', 'safe'),
            array('status_id', 'required'),
            array('status_id', 'statusDependencyValidation'),
            array('role_id', 'roleDependencyValidation'),
            array('followup_quantity, risk_status_id', 'default', 'setOnEmpty' => true, 'value' => null),
            array('followup_quantity', 'numerical', 'integerOnly' => true, 'min' => Element_OphCiExamination_ClinicOutcome::FOLLOWUP_Q_MIN, 'max' => Element_OphCiExamination_ClinicOutcome::FOLLOWUP_Q_MAX),
            array('element_id, status_id, followup_quantity, followup_period_id, role_id, followup_comments', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'element' => [self::BELONGS_TO, 'OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome', 'element_id'],
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'status' => [self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status', 'status_id'],
            'followupPeriod' => [self::BELONGS_TO, 'Period', 'followup_period_id'],
            'role' => [self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Role', 'role_id'],
            'risk_status' => [self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Risk_Status', 'risk_status_id'],
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'element_id' => 'Element',
            'status_id' => 'Status',
            'followup_quantity' => 'Follow-up',
            'followup_period_id' => 'Follow-up period',
            'followup_comments' => 'Comments',
            'role_id' => 'Role',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('element_id', $this->element_id, true);
        $criteria->compare('status_id', $this->status_id);
        $criteria->compare('followup_quantity', $this->followup_quantity);
        $criteria->compare('followup_period_id', $this->followup_period_id);
        $criteria->compare('followup_comments', $this->followup_comments);
        $criteria->compare('role_id', $this->role_id);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Follow up data is only required for status that are flagged for follow up.
     *
     * @property string $attribute
     */
    public function statusDependencyValidation($attribute)
    {
        if ($this->status_id && $this->status->followup) {
            $v = \CValidator::createValidator('required', $this, array('followup_quantity', 'followup_period_id', 'role_id'));
            $v->validate($this);
        }
    }

    /**
     * Role comments are only required if role flags it.
     *
     * @property string $attribute
     */
    public function roleDependencyValidation($attribute)
    {
        if ($this->role && $this->role->requires_comment
            && !trim($this->followup_comments)) {
            $this->addError($attribute, '"' .$this->role->name . '" role requires a comment');
        }
    }

    public function afterDelete()
    {
        $ticket = $this->element->getPatientTicket();
        $patient_ticket_ids = OphCiExamination_ClinicOutcome_Status::model()->getPatientTicketIds();

        if (array_search($this->status_id, $patient_ticket_ids) !== false && $ticket) {
            $this->element->deleteRelatedTicket($ticket);
        }

        parent::afterDelete();
    }

    public function getStatusLabel()
    {
        return $this->status->name;
    }

    public function getRoleLabel()
    {
        if ($this->status->followup && $this->role) {
            return ' with ' . $this->role->name;
        }
        return '';
    }

    public function getPeriodLabel()
    {
        if ($this->status->followup && $this->followupPeriod) {
            return $this->followupPeriod->name;
        }
        return '';
    }

    public function getRiskStatusLabel($for_worklist = false)
    {
        $ret = array(
            'class' => '',
            'content' => '',
            'icon' => '',
        );

        if ($this->status->followup && $this->risk_status) {
            $size_css = "";
            $position_css = "";
            $content = "";
            if (!$for_worklist) {
                $size_css = "small";
                $position_css = "pad-right";
                $content = "{$this->risk_status->name}. {$this->risk_status->alias}";
            } else {
                $subspecialty = $this->element->event->episode->getSubspecialtyText();
                $due_date = "$subspecialty due: {$this->getDueDate()}";
                $risk_status_details = "{$this->risk_status->alias} ({$this->risk_status->name}):";
                $riskt_status_desc = $this->risk_status->description;
                $content = "{$due_date}<br/><br/>${risk_status_details}<br/>{$riskt_status_desc}";
            }
            $risk_status_icon_color = $this->risk_status->getIndicatorColor();
            $ret['class'] = "oe-i triangle-{$risk_status_icon_color} $size_css $position_css js-has-tooltip";
            $ret['content'] = $content;
            $ret['icon'] = "<i class='{$ret['class']}' data-tooltip-content='{$ret['content']}'></i>";
        }
        return $ret;
    }

    public function getDueDate()
    {
        $event_date = $this->element->event->event_date;
        $due_date = null;
        if ($this->status->followup) {
            $period = strtolower($this->followupPeriod);
            $quantity = intval($this->followup_quantity);
            $due_date = date('Y-m-d', strtotime("+$quantity $period", strtotime($event_date)));
            $due_date = \Helper::convertDate2NHS($due_date, ' ');
        }

        return $due_date;
    }

    public function getDisplayComments()
    {
        return $this->followup_comments ? ' (' . $this->followup_comments . ')' : '';
    }

    public function getInfos()
    {
        if ($this->isFollowUp()) {
            $risk_status_info = $this->getRiskStatusLabel();
            return $this->getStatusLabel() . ' ' . $this->followup_quantity . ' ' . $this->getPeriodLabel() . $this->getRoleLabel() . ' ' . $this->getDisplayComments() . $risk_status_info['icon'];
        } else {
            return $this->getStatusLabel();
        }
    }

    public function isPatientTicket()
    {
        if ($this->status->patientticket) {
            return true;
        }
        return false;
    }

    public function isFollowUp()
    {
        if ($this->status->followup) {
            return true;
        }
        return false;
    }
}
