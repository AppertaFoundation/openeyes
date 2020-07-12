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
 * This is the model class for table "et_ophciexamination_bleb_assessment".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property int $left_central_area_id
 * @property int $left_max_area_id
 * @property int $left_height_id
 * @property int $left_vasc_id
 * @property string $left_notes
 * @property int $right_central_area_id
 * @property int $right_max_area_id
 * @property int $right_height_id
 * @property int $right_vasc_id
 * @property string $right_notes
 */
class Element_OphCiExamination_BlebAssessment extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    public $service;

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
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
        return 'et_ophciexamination_bleb_assessment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('event_id, eye_id, left_central_area_id, left_max_area_id, left_height_id, left_vasc_id, left_notes,
					right_central_area_id, right_max_area_id, right_height_id, right_vasc_id, right_notes', 'safe'),
                array('left_central_area_id, left_max_area_id, left_height_id, left_vasc_id', 'requiredIfSide', 'side' => 'left'),
                array('right_central_area_id, right_max_area_id, right_height_id, right_vasc_id', 'requiredIfSide', 'side' => 'right'),
                // The following rule is used by search().
                array('event_id, eye_id, left_central_area_id, left_max_area_id, left_height_id, left_vasc_id, left_notes,
					right_central_area_id, right_max_area_id, right_height_id, right_vasc_id, right_notes', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array
     *               (non-phpdoc)
     *
     * @see parent::sidedFields()
     */
    public function sidedFields()
    {
        return array('central_area_id', 'max_area_id', 'height_id', 'vasc_id');
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
                'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'left_central_area' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_CentralArea', 'left_central_area_id'),
                'left_max_area' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_MaxArea', 'left_max_area_id'),
                'left_height' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Height', 'left_height_id'),
                'left_vasc' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Vascularity', 'left_vasc_id'),
                'right_central_area' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_CentralArea', 'right_central_area_id'),
                'right_max_area' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_MaxArea', 'right_max_area_id'),
                'right_height' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Height', 'right_height_id'),
                'right_vasc' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Vascularity', 'right_vasc_id'),
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
                'left_central_area_id' => 'Central Area',
                'left_max_area_id' => 'Max Area',
                'left_notes' => 'Comments',
                'right_central_area_id' => 'Central Area',
                'right_max_area_id' => 'Max Area',
                'left_height_id' => 'Height',
                'left_vasc_id' => 'Vascularity',
                'right_height_id' => 'Height',
                'right_vasc_id' => 'Vascularity',
                'right_notes' => 'Comments'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('left_central_area_id', $this->left_central_area_id);
        $criteria->compare('left_max_area_id', $this->left_max_area_id);
        $criteria->compare('left_height_id', $this->left_height_id);
        $criteria->compare('left_vasc_id', $this->left_vasc_id);
        $criteria->compare('left_notes', $this->left_notes);
        $criteria->compare('right_central_area_id', $this->right_central_area_id);
        $criteria->compare('right_max_area_id', $this->right_max_area_id);
        $criteria->compare('right_height_id', $this->right_height_id);
        $criteria->compare('right_vasc_id', $this->right_vasc_id);
        $criteria->compare('right_notes', $this->right_notes);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function canCopy()
    {
        return true;
    }
}
