<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_injectionmanagement".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property string $injection_status_id
 * @property string $injection_deferralreason_id
 * @property string $injection_deferralreason_other
 *
 * The followings are the available model relations:
 * @property OphCiExamination_Management_Status $injection_status
 */
class Element_OphCiExamination_InjectionManagement extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    public $service;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'et_ophciexamination_injectionmanagement';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('injection_status_id, injection_deferralreason_id, injection_deferralreason_other', 'safe'),
                array('injection_status_id', 'required'),
                array('injection_status_id', 'injectionDependencyValidation'),
                array('injection_deferralreason_id', 'injectionDeferralReasonDependencyValidation'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, injection_status_id, injection_deferralreason_id, injection_deferralreason_other', 'safe', 'on' => 'search'),
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
                'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'injection_status' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Management_Status', 'injection_status_id'),
                'injection_deferralreason' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Management_DeferralReason', 'injection_deferralreason_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'id' => 'ID',
                'event_id' => 'Event',
                'injection_status_id' => 'Injection',
                'injection_deferralreason_id' => 'Injection deferral reason',
                'injection_deferralreason_other' => 'Injection deferral reason',
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
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('injection_status_id', $this->injection_status_id);
        $criteria->compare('injection_deferralreason_id', $this->injection_deferral_reason_id);
        $criteria->compare('injection_deferralreason_other', $this->injection_deferralreason_other);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /*
     * deferral reason is only required for injection status that are flagged deferred
    */
    public function injectionDependencyValidation($attribute)
    {
        if ($this->injection_status && $this->injection_status->deferred) {
            $v = \CValidator::createValidator('required', $this, array('injection_deferralreason_id'));
            $v->validate($this);
        }
    }

    /*
     * only need a text "other" reason for reasons that are flagged "other"
    */
    public function injectionDeferralReasonDependencyValidation($attribute)
    {
        if ($this->injection_deferralreason && $this->injection_deferralreason->other) {
            $v = \CValidator::createValidator('required', $this, array('injection_deferralreason_other'), array('message' => '{attribute} required when deferral reason is '.$this->injection_deferralreason));
            $v->validate($this);
        }
    }

    /*
     * returns the reason the injection has been deferred (switches between text value of fk, or the entered 'other' reason)
     *
     * @returns string
     */
    public function getInjectionDeferralReason()
    {
        if ($this->injection_deferralreason) {
            if ($this->injection_deferralreason->other) {
                return $this->injection_deferralreason_other;
            } else {
                return $this->injection_deferralreason->name;
            }
        } else {
            // shouldn't get to this point really
            return 'N/A';
        }
    }

    public function canCopy()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->injection_status ? ((string) $this->injection_status) : '';
    }
}
