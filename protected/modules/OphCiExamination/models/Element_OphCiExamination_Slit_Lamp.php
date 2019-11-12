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
 * This is the model class for table "et_ophciexamination_slit_lamp".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $event_id
 * @property int $eye_id
 * @property int $right_allergic_conjunctivitis_id
 * @property int $right_blepharitis_id
 * @property int $right_dry_eye_id
 * @property int $right_cornea_id
 * @property int $left_allergic_conjunctivitis_id
 * @property int $left_blepharitis_id
 * @property int $left_dry_eye_id
 * @property int $left_cornea_id
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_Slit_Lamp extends \SplitEventTypeElement
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
        return 'et_ophciexamination_slit_lamp';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('eye_id, right_allergic_conjunctivitis_id, right_blepharitis_id, right_dry_eye_id, right_cornea_id, 
            left_allergic_conjunctivitis_id, left_blepharitis_id, left_dry_eye_id, left_cornea_id', 'safe'),

            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, eye_id, event_id, right_allergic_conjunctivitis_id, right_blepharitis_id, right_dry_eye_id, 
            right_cornea_id, left_allergic_conjunctivitis_id, left_blepharitis_id, left_dry_eye_id, left_cornea_id', 'safe', 'on' => 'search'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
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
            'right_allergic_conjunctivitis_id' => 'Allergic Conjunctivitis',
            'right_blepharitis_id' => 'Blepharitis',
            'right_dry_eye_id' => 'Dry Eye',
            'right_cornea_id' => 'Cornea',
            'left_allergic_conjunctivitis_id' => 'Allergic Conjunctivitis',
            'left_blepharitis_id' => 'Blepharitis',
            'left_dry_eye_id' => 'Dry Eye',
            'left_cornea_id' => 'Cornea'
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
        $criteria->compare('right_allergic_conjunctivitis_id', $this->allergic_conjunctivitis_id);
        $criteria->compare('right_blepharitis_id', $this->blepharitis_id);
        $criteria->compare('right_dry_eye_id', $this->dry_eye_id);
        $criteria->compare('right_cornea_id', $this->cornea_id);
        $criteria->compare('left_allergic_conjunctivitis_id', $this->allergic_conjunctivitis_id);
        $criteria->compare('left_blepharitis_id', $this->blepharitis_id);
        $criteria->compare('left_dry_eye_id', $this->dry_eye_id);
        $criteria->compare('left_cornea_id', $this->cornea_id);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

}
