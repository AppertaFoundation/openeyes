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

/*
 * This is the model class for table "et_ophciexamination_currentmanagementplan".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
 * @property integer $glaucoma_status_id
 * @property integer $drop-related_prob_id
 * @property integer $drops_id
 * @property integer $surgery_id
 * @property integer $other_service
 * @property integer $refraction
 * @property integer $lva
 * @property integer $orthoptics
 * @property integer $cl_clinic
 * @property integer $vf
 * @property integer $us
 * @property integer $biometry
 * @property integer $oct
 * @property integer $hrt
 * @property integer $disc_photos
 * @property integer $edt
 *
 * The followings are the available model relations:
 *
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property OphCiExamination_GlaucomaStatus $right_glaucoma_status
 * @property OphCiExamination_DropRelProb $right_drop-related_prob
 * @property OphCiExamination_Drops $right_drops
 * @property OphCiExamination_ManagementSurgery $right_surgery
 * @property OphCiExamination_GlaucomaStatus $left_glaucoma_status
 * @property OphCiExamination_DropRelProb $left_drop-related_prob
 * @property OphCiExamination_Drops $left_drops
 * @property OphCiExamination_ManagementSurgery $left_surgery
 */

use OEModule\OphCiExamination\components\OphCiExamination_API;

class Element_OphCiExamination_CurrentManagementPlan  extends  \SplitEventTypeElement
{
    use traits\CustomOrdering;
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
        return 'et_ophciexamination_currentmanagementplan';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id, left_glaucoma_status_id, left_drop-related_prob_id, left_drops_id, left_surgery_id,
			right_glaucoma_status_id, right_drop-related_prob_id, right_drops_id, right_surgery_id, eye_id', 'safe'),
            array('left_glaucoma_status_id, left_drop-related_prob_id, left_drops_id',
                'requiredIfSide', 'side' => 'left', ),
            array('right_glaucoma_status_id, right_drop-related_prob_id, right_drops_id',
                'requiredIfSide', 'side' => 'right', ),
            array('eye_id ', 'required'),
            array('id, event_id, left_glaucoma_status_id, left_drop-related_prob_id, left_drops_id, left_surgery_id,
			right_glaucoma_status_id, right_drop-related_prob_id, right_drops_id, right_surgery_id, eye_id ',
                'safe', 'on' => 'search', ),
        );
    }

    /**
     * @return array
     *
     * @see parent::sidedFields()
     */
    public function sidedFields()
    {
        return array('glaucoma_status_id', 'drop-related_prob_id', 'drops_id', 'surgery_id');
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'right_glaucoma_status' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_GlaucomaStatus', 'right_glaucoma_status_id'),
            'left_glaucoma_status' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_GlaucomaStatus', 'left_glaucoma_status_id'),
            'right_drop-related_prob' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_DropRelProb', 'right_drop-related_prob_id'),
            'left_drop-related_prob' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_DropRelProb', 'left_drop-related_prob_id'),
            'right_drops' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Drops', 'right_drops_id'),
            'left_drops' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Drops', 'left_drops_id'),
            'right_surgery' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_ManagementSurgery', 'right_surgery_id'),
            'left_surgery' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_ManagementSurgery', 'left_surgery_id'),
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
            'right_glaucoma_status_id' => 'Glaucoma status',
            'left_glaucoma_status_id' => 'Glaucoma status',
            'right_drop-related_prob_id' => 'Drop-related problems',
            'left_drop-related_prob_id' => 'Drop-related problems',
            'right_drops_id' => 'Drops',
            'left_drops_id' => 'Drops',
            'right_surgery_id' => 'Surgery',
            'left_surgery_id' => 'Surgery',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('right_glaucoma_status_id', $this->right_glaucoma_status_id);
        $criteria->compare('left_glaucoma_status_id', $this->left_glaucoma_status_id);
        $criteria->compare('right_drop-related_prob_id', $this->drop - right_related_prob_id);
        $criteria->compare('left_drop-related_prob_id', $this->left_drop - related_prob_id);
        $criteria->compare('right_drops_id', $this->right_drops_id);
        $criteria->compare('left_drops_id', $this->left_drops_id);
        $criteria->compare('right_surgery_id', $this->right_surgery_id);
        $criteria->compare('left_surgery_id', $this->left_surgery_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function canCopy()
    {
        return true;
    }

    public function getLatestIOP($patient, $api = null)
    {
        $result = array();

        $api = $api ? $api : $this->getApp()->moduleAPI->get('OphCiExamination');
        $result['leftIOP'] = $api->getIOPReadingLeft($patient);
        $result['rightIOP'] = $api->getIOPReadingRight($patient);
        if ($result['leftIOP'] || $result['rightIOP']) {
            return $result;
        }

        return;
    }

    public function __toString()
    {
        $result = array();
        if ($this->hasRight()) {
            $result[] = 'R: ' . $this->right_glaucoma_status;
        }
        if ($this->hasLeft()) {
            $result[] = 'L: ' . $this->left_glaucoma_status;
        }
        return implode(', ', $result);
    }
}
