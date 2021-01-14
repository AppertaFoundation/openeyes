<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\models\traits\CustomOrdering;
use OEModule\OphCiExamination\models\traits\HasRelationOptions;
use OEModule\OphCiExamination\widgets\SocialHistory as SocialHistoryWidget;

/**
 * @property string $id
 * @property int $event_id
 * @property int $occupation_id
 * @property int $driving_status_id
 * @property int $smoking_status_id
 * @property int $accommodation_id
 * @property string $comments
 * @property string $type_of_job
 * @property int $carer_id
 * @property int $alcohol_intake
 * @property int $substance_misuse_id
 *
 * relations:
 * @property \User $user
 * @property \User $usermodified
 * @property SocialHistoryOccupation $occupation
 * @property SocialHistoryDrivingStatus[] $driving_statuses
 * @property SocialHistorySmokingStatus $smoking_status
 * @property SocialHistoryAccommodation $accommodation
 * @property SocialHistoryCarer $carer
 * @property SocialHistorySubstanceMisuse $substance_misuse
 */
class SocialHistory extends \BaseEventTypeElement
{
    use CustomOrdering;
    use HasRelationOptions;

    protected $auto_update_relations = true;
    protected $widgetClass = SocialHistoryWidget::class;
    protected $default_from_previous = true;

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
        return 'et_ophciexamination_socialhistory';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array(
            'PatientLevelElementBehaviour' => 'PatientLevelElementBehaviour',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, occupation_id, smoking_status_id, accommodation_id, carer_id, substance_misuse_id, ' .
                'alcohol_intake, comments, type_of_job, driving_statuses', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, occupation_id, smoking_status_id, accommodation_id, carer_id, substance_misuse_id, ' .
                'alcohol_intake, comments, type_of_job',  'safe', 'on' => 'search')
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'occupation' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\SocialHistoryOccupation', 'occupation_id'),
            'driving_status_assignments' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\SocialHistoryDrivingStatusAssignment', 'element_id', 'order' => 'display_order asc'),
            'driving_statuses' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\SocialHistoryDrivingStatus', 'driving_status_id', 'through' => 'driving_status_assignments'),
            'smoking_status' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\SocialHistorySmokingStatus', 'smoking_status_id'),
            'accommodation' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\SocialHistoryAccommodation', 'accommodation_id'),
            'carer' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\SocialHistoryCarer', 'carer_id'),
            'substance_misuse' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\SocialHistorySubstanceMisuse', 'substance_misuse_id'),
        );
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array(
            'occupation_id' => 'Employment',
            'driving_statuses' => 'Driving Status',
            'smoking_status_id' => 'Smoking Status',
            'accommodation_id' => 'Accommodation',
            'comments' => 'Comments',
            'type_of_job' => 'Type of job',
            'carer_id' => 'Carer',
            'alcohol_intake' => 'Alcohol Intake',
            'substance_misuse_id' => 'Substance Misuse',
        );
    }

    /**
     * @return \CActiveDataProvider
     */
    public function search()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('occupation_id', $this->occupation_id);
        $criteria->compare('smoking_status_id', $this->smoking_status_id);
        $criteria->compare('accommodation_id', $this->accommodation_id);
        $criteria->compare('comments', $this->comments);
        $criteria->compare('type_of_job', $this->type_of_job);
        $criteria->compare('carer_id', $this->carer_id);
        $criteria->compare('alcohol_intake', $this->alcohol_intake);
        $criteria->compare('substance_misuse_id', $this->substance_misuse_id);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return string
     */
    public function getDisplayOccupation()
    {
        if ($this->type_of_job) {
            return $this->type_of_job;
        } else {
            return $this->occupation ? $this->occupation->name : '';
        }
    }

    /**
     * @return string
     */
    public function getDisplayDrivingStatuses($separator = ', ')
    {
        if ($this->driving_statuses) {
            return implode($separator, $this->driving_statuses);
        }
        return '';
    }

    /**
     * @return string
     */
    public function getDisplayAlcoholIntake()
    {
        if (isset($this->alcohol_intake)) {
            return $this->alcohol_intake . ' units/week';
        }
        return '';
    }

    public function getDisplayAllEntries()
    {
        $res = array();
        foreach (array(
                     'occupation_id' => 'displayoccupation',
                     'driving_statuses' => 'displaydrivingstatuses',
                     'smoking_status_id' => 'smoking_status',
                     'accommodation_id' => 'accommodation',
                     'comments' => 'comments',
                     'carer_id' => 'carer',
                     'alcohol_intake' => 'displayalcoholintake',
                     'substance_misuse_id' => 'substance_misuse',
                 ) as $id => $source) {
            if ($this->$source) {
                $res[] = $this->getAttributeLabel($id) . ': ' . $this->$source;
            }
        }

        return $res;
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        if ($this->alcohol_intake == "")
        $this->alcohol_intake = null;
        return true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' <br /> ', $this->getEntries());
    }

    /**
     * @param SocialHistory $element
     */
    public function loadFromExisting($element)
    {
        foreach (['occupation_id', 'occupation_id', 'smoking_status_id', 'accommodation_id', 'carer_id',
                     'substance_misuse_id', 'alcohol_intake', 'comments', 'type_of_job', 'driving_statuses'] as $field) {
            // add only the entries from DB that were not in the previous session
            if (!$this->$field) {
                $this->$field = $element->$field;
            }
        }
    }
}
