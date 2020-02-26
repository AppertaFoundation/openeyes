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
 * This is the model class for table "et_ophciexamination_lasermanagement".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $left_laser_status_id
 * @property int $left_laser_deferralreason_id
 * @property string $left_laser_deferralreason_other
 * @property int $left_lasertype_id
 * @property string $left_lasertype_other
 * @property string $left_comments
 * @property int $right_laser_status_id
 * @property int $right_laser_deferralreason_id
 * @property string $right_laser_deferralreason_other
 * @property int $right_lasertype_id
 * @property string $right_lasertype_other
 * @property string $right_comments
 *
 * The followings are the available model relations:
 * @property OphCiExamination_ManagementStatus $left_laser_status
 * @property OphCiExamination_Management_DeferralReason $left_laser_deferralreason
 * @property OphCiExamination_LaserManagement_LaserType $left_lasertype
 * @property OphCiExamination_ManagementStatus $right_laser_status
 * @property OphCiExamination_Management_DeferralReason $right_laser_deferralreason
 * @property OphCiExamination_LaserManagement_LaserType $right_lasertype
 */
class Element_OphCiExamination_LaserManagement extends \SplitEventTypeElement
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
        return 'et_ophciexamination_lasermanagement';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('eye_id, left_laser_status_id, left_laser_deferralreason_id, left_laser_deferralreason_other, '.
                'left_lasertype_id, left_lasertype_other, left_comments, right_laser_status_id, right_laser_deferralreason_id, '.
                'right_laser_deferralreason_other, right_lasertype_id, right_lasertype_other, right_comments', 'safe', ),
            array('left_laser_status_id', 'requiredIfSide', 'side' => 'left'),
            array('right_laser_status_id', 'requiredIfSide', 'side' => 'right'),
            array('left_laser_deferralreason_id', 'laserDependencyDeferralValidation', 'status' => 'left_laser_status_id'),
            array('right_laser_deferralreason_id', 'laserDependencyDeferralValidation', 'status' => 'right_laser_status_id'),
            array('left_laser_deferralreason_other', 'laserDeferralReasonDependencyValidation', 'deferral' => 'left_laser_deferralreason_id'),
            array('right_laser_deferralreason_other', 'laserDeferralReasonDependencyValidation', 'deferral' => 'right_laser_deferralreason_id'),
            array('left_lasertype_id', 'requiredIfTreatmentNeeded', 'side' => 'left', 'status' => 'left_laser_status_id'),
            array('left_lasertype_other', 'requiredIfLaserTypeOther', 'side' => 'left', 'lasertype' => 'left_lasertype'),
            array('right_lasertype_id', 'requiredIfTreatmentNeeded', 'side' => 'right', 'status' => 'right_laser_status_id'),
            array('right_lasertype_other', 'requiredIfLaserTypeOther', 'side' => 'right', 'lasertype' => 'right_lasertype'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id, left_laser_status_id, left_laser_deferralreason_id, left_laser_deferralreason_other, '.
                'left_lasertype_id, left_lasertype_other, left_comments, right_laser_status_id, right_laser_deferralreason_id, '.
                'right_laser_deferralreason_other, right_lasertype_id, right_lasertype_other, right_comments', 'safe', 'on' => 'search', ),
        );
    }

    public function sidedFields()
    {
        return array('laser_status_id', 'laser_deferralreason_id', 'laser_deferralreason_other', 'lasertype_id', 'lasertype_other', 'comments');
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
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'left_laser_status' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Management_Status', 'left_laser_status_id'),
            'right_laser_status' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Management_Status', 'right_laser_status_id'),
            'left_laser_deferralreason' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Management_DeferralReason', 'left_laser_deferralreason_id'),
            'right_laser_deferralreason' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Management_DeferralReason', 'right_laser_deferralreason_id'),
            'left_lasertype' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_LaserManagement_LaserType', 'left_lasertype_id'),
            'right_lasertype' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_LaserManagement_LaserType', 'right_lasertype_id'),
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
            'left_laser_status_id' => 'Laser',
            'left_laser_deferralreason_id' => 'Laser deferral reason',
            'left_laser_deferralreason_other' => 'Laser deferral reason',
            'left_lasertype_id' => 'Laser type',
            'left_lasertype_other' => 'Please provide other laser type name',
            'left_comments' => 'Comments',
            'right_laser_status_id' => 'Laser',
            'right_laser_deferralreason_id' => 'Laser deferral reason',
            'right_laser_deferralreason_other' => 'Laser deferral reason',
            'right_lasertype_id' => 'Laser type',
            'right_lasertype_other' => 'Please provide other laser type name',
            'right_comments' => 'Comments',

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
        $criteria->compare('eye_id', $this->eye_id, true);
        $criteria->compare('left_laser_status_id', $this->left_laser_status_id);
        $criteria->compare('left_laser_deferralreason_id', $this->left_laser_deferral_reason_id);
        $criteria->compare('left_laser_deferralreason_other', $this->left_laser_deferralreason_other);
        $criteria->compare('left_lasertype_id', $this->left_lasertype_id);
        $criteria->compare('left_lasertype_other', $this->left_lasertype_other);
        $criteria->compare('left_comments', $this->left_comments);
        $criteria->compare('right_laser_status_id', $this->right_laser_status_id);
        $criteria->compare('right_laser_deferralreason_id', $this->right_laser_deferral_reason_id);
        $criteria->compare('right_laser_deferralreason_other', $this->right_laser_deferralreason_other);
        $criteria->compare('right_lasertype_id', $this->right_lasertype_id);
        $criteria->compare('right_lasertype_other', $this->right_lasertype_other);
        $criteria->compare('right_comments', $this->right_comments);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * deferral reason is only required for laser status that are flagged deferred.
     *
     * @param string $attribute attribute to validate
     * @param {key => value}[] params - status required
     */
    public function laserDependencyDeferralValidation($attribute, $params)
    {
        $status_attribute = $params['status'];

        if ($status_id = $this->$status_attribute) {
            $status = OphCiExamination_Management_Status::model()->findByPk($status_id);
            if ($status->deferred) {
                $v = \CValidator::createValidator('required', $this, array($attribute));
                $v->validate($this);
            }
        }
    }

    /**
     * only need a text "other" reason for reasons that are flagged "other".
     *
     * @param string $attribute attribute to validate
     * @param {key => value}[] params - deferral required
     */
    public function laserDeferralReasonDependencyValidation($attribute, $params)
    {
        $deferral_attribute = $params['deferral'];
        if ($deferral_id = $this->$deferral_attribute) {
            $deferral = OphCiExamination_Management_DeferralReason::model()->findByPk($deferral_id);
            if ($deferral->other) {
                $v = \CValidator::createValidator('required', $this, array($attribute), array('message' => '{attribute} required when deferral reason is '.$deferral->name));
                $v->validate($this);
            }
        }
    }

    /**
     * validate for status that indicate treatment required.
     *
     * @param $attribute
     * @param {key => value}[] $params - status and side required
     */
    public function requiredIfTreatmentNeeded($attribute, $params)
    {
        $side = $params['side'];
        $status_attribute = $params['status'];
        if ($status_id = $this->$status_attribute) {
            $status = OphCiExamination_Management_Status::model()->findByPk($status_id);
            if ($status->book || $status->event) {
                $this->requiredifSide($attribute, array('side' => $side));
            }
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function requiredIfLaserTypeOther($attribute, $params)
    {
        $lasertype_attr = $params['lasertype'];
        $lt = $this->$lasertype_attr;
        if ($lt && $lt->other) {
            $v = \CValidator::createValidator('required', $this, array($attribute), array('message' => ucfirst($params['side']).' {attribute} required for laser type '.$lt->name));
            $v->validate($this);
        }
    }

    /**
     * returns the reason the injection has been deferred (switches between text value of fk, or the entered 'other' reason).
     *
     * @param string $side
     *
     * @return mixed|string
     *
     * @throws Exception
     */
    public function getLaserDeferralReasonForSide($side)
    {
        if (!in_array($side, array('left', 'right'))) {
            throw new Exception('unrecognised side attribute '.$side);
        }

        if (($side == 'left' && $this->hasLeft()) || $this->hasRight()) {
            if ($this->{$side.'_laser_deferralreason'}) {
                if ($this->{$side.'_laser_deferralreason'}->other) {
                    return $this->{$side.'_laser_deferralreason_other'};
                } else {
                    return $this->{$side.'_laser_deferralreason'}->name;
                }
            } else {
                // shouldn't get to this point really
                return 'N/A';
            }
        }
    }

    /**
     * returns the laser type for the given side.
     *
     * @param string $side
     *
     * @throws Exception
     *
     * @return string
     */
    public function getLaserTypeStringForSide($side)
    {
        if (!in_array($side, array('left', 'right'))) {
            throw new Exception('unrecognised side attribute '.$side);
        }

        if ($lt = $this->{$side.'_lasertype'}) {
            if ($lt->other) {
                return $this->{$side.'_lasertype_other'};
            } else {
                return $lt->name;
            }
        }

        return 'N/A';
    }

    /**
     * Returns the laser management plan section  for use in correspondence.
     *
     * @return string
     *
     * @deprecated since 1.4.10 - use getLetter_string()
     */
    public function getLetter_lmp()
    {
        return $this->getLetter_string();
    }

    /**
     * gets a string of the information contained in this element for the given side.
     *
     * @param $side
     *
     * @return string
     */
    protected function getLetterStringForSide($side)
    {
        $res = ucfirst($side)." Eye:\n";

        $status = $this->{$side.'_laser_status'};
        $res .= $status->name;
        if ($status->deferred) {
            $res .= ' due to '.$this->getLaserDeferralReasonForSide($side);
        }

        if ($status->book || $status->event) {
            $res .= "\n".$this->getAttributeLabel($side.'_lasertype_id').': '.$this->getLaserTypeStringForSide($side);
        }
        if ($comments = $this->{$side.'_comments'}) {
            $res .= "\n".$this->getAttributeLabel($side.'_comments').': '.$comments;
        }

        return $res;
    }

    /**
     * get the string of this element for use in correspondence.
     *
     * @return string
     */
    public function getLetter_string()
    {
        $res = "Laser Management:\n";
        if ($this->hasRight()) {
            $res .= $this->getLetterStringForSide('right');
        }
        if ($this->hasLeft()) {
            if ($this->hasRight()) {
                $res .= "\n";
            }
            $res .= $this->getLetterStringForSide('left');
        }

        return $res;
    }

    public function canCopy()
    {
        return true;
    }

    public function __toString()
    {
        $result = array();
        if ($this->hasRight()) {
            $result[] = 'R: ' . $this->right_laser_status;
        }
        if ($this->hasRight()) {
            $result[] = 'L: ' . $this->left_laser_status;
        }
        return implode(', ', $result);
    }

    /**
     * @return string
     */
    public function getComments()
    {
        $result = array();
        if ($this->hasRight() && $this->right_comments) {
            $result[] = 'R: ' . $this->right_comments;
        }
        if ($this->hasLeft() && $this->left_comments) {
            $result[] = 'L: ' . $this->left_comments;
        }
        return implode(', ', $result);
    }

}
