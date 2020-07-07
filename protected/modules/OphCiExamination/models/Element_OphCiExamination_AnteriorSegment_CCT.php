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
 * This is the model class for table "et_ophciexamination_anteriorsegment_cct".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $event_id
 * @property int $eye_id
 * @property string $left_value
 * @property string $right_value
 * @property OphCiExamination_AnteriorSegment_CCT_Method $left_method
 * @property OphCiExamination_AnteriorSegment_CCT_Method $right_method
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_AnteriorSegment_CCT extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_AnteriorSegment_CCT
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
        return 'et_ophciexamination_anteriorsegment_cct';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('eye_id, left_method_id, left_value, right_method_id, right_value, left_notes, right_notes', 'safe'),
                array('left_method_id ,left_value', 'requiredIfSide', 'side' => 'left'),
                array('right_method_id, right_value', 'requiredIfSide', 'side' => 'right'),
                array('left_value', 'numerical', 'integerOnly' => true, 'max' => 1500, 'min' => 0),
                array('right_value', 'numerical', 'integerOnly' => true, 'max' => 1500, 'min' => 0),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id, left_method_id, right_method_id, left_value, right_value, left_notes, right_notes', 'safe', 'on' => 'search'),
        );
    }

    public function sidedFields()
    {
        return array('value', 'method_id');
    }

    public function canCopy()
    {
        return true;
    }

    public function sidedDefaults()
    {
        return array();
    }
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'left_method' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_CCT_Method', 'left_method_id'),
                'right_method' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_CCT_Method', 'right_method_id'),
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
                'left_value' => 'Value',
                'right_value' => 'Value',
                'left_method' => 'Method',
                'right_method' => 'Method',
                'left_notes' => 'Comments',
                'right_notes' => 'Comments',
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
        $criteria->compare('left_value', $this->left_value);
        $criteria->compare('right_value', $this->right_value);
        $criteria->compare('left_method_id', $this->left_method_id);
        $criteria->compare('right_method_id', $this->right_method_id);
        $criteria->compare('left_notes', $this->left_notes);
        $criteria->compare('right_notes', $this->right_notes);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }
}
