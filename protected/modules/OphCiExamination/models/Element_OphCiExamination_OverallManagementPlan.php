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

/**
 * This is the model class for table "et_ophciexamination_overallmanagementplan".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $target_iop
 * @property int $clinic_interval_id
 * @property int $photo_id
 * @property int $oct_id
 * @property int $hfa_id
 * @property int $hrt_id
 * @property int $gonio_id
 * @property string $comments
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property OphCiExamination_OverallPeriod $clinic_interval
 * @property Gender $photo
 * @property Gender $oct
 * @property Gender $hfa
 * @property Gender $gonio
 */
class Element_OphCiExamination_OverallManagementPlan  extends  \SplitEventTypeElement
{
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
        return 'et_ophciexamination_overallmanagementplan';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id, left_target_iop_id , right_target_iop_id  , gonio_id, clinic_interval_id ,
				 photo_id , oct_id ,hfa_id , hrt_id, comments , eye_id', 'safe'),
            array('clinic_interval_id, photo_id , oct_id , hfa_id, hrt_id', 'required'),
            array('left_target_iop_id ',
                'requiredIfSide', 'side' => 'left', ),
            array('right_target_iop_id  ',
                'requiredIfSide', 'side' => 'right', ),
            array('id, event_id, left_target_iop_id , right_target_iop_id  , gonio_id, clinic_interval_id ,
				photo_id , oct_id , hfa_id , hrt_id, comments , eye_id, ', 'safe', 'on' => 'search'),

        );
    }

    /**
     * @return array
     *
     * @see parent::sidedFields()
     */
    public function sidedFields()
    {
        return array('target_iop_id');
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
            'gonio' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_VisitInterval', 'gonio_id'),
            'clinic_interval' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'clinic_interval_id'),
            'photo' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'photo_id'),
            'oct' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'oct_id'),
            'hfa' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'hfa_id'),
            'hrt' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'hrt_id'),
            'right_target_iop' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_TargetIop', 'right_target_iop_id'),
            'left_target_iop' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_TargetIop', 'left_target_iop_id'),
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
            'right_target_iop_id' => 'Target IOP',
            'left_target_iop_id' => 'Target IOP',
            'gonio_id' => 'Gonio',
            'comments' => 'Comments',
            'clinic_interval_id' => 'Clinic interval',
            'photo_id' => 'Photo',
            'oct_id' => 'OCT',
            'hfa_id' => 'Visual Fields',
            'hrt_id' => 'HRT',
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
        $criteria->compare('right_target_iop_id', $this->right_target_iop_id);
        $criteria->compare('left_target_iop_id', $this->left_target_iop_id);
        $criteria->compare('gonio_id', $this->gonio_id);
        $criteria->compare('clinic_interval_id', $this->clinic_interval_id);
        $criteria->compare('photo_id', $this->photo_id);
        $criteria->compare('oct_id', $this->oct_id);
        $criteria->compare('hfa_id', $this->hfa_id);
        $criteria->compare('hrt_id', $this->hrt_id);
        $criteria->compare('comments', $this->comments);
        $criteria->compare('eye', $this->eye);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function canCopy()
    {
        return true;
    }

    protected function afterSave()
    {
        return parent::afterSave();
    }

    public function setDefaultOptions(\Patient $patient = null)
    {
        $element_type = \ElementType::model()->find('class_name=?', array(get_class($this)));
        $defaults = \SettingMetadata::model()->findAll('element_type_id=? ', array($element_type->id));
        foreach ($defaults as $default) {
            $this->{$default->key} = $default->default_value;
        }
    }

    public function __toString()
    {
        return 'Clinic: ' . $this->clinic_interval;
    }
    
    public function getPrint_view()
    {
        return 'print_'.$this->getDefaultView();
    }

}
