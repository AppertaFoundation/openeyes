<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "et_ophcocvi_clinicinfo_disorder_section_comment".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $element_id
 * @property integer $ophcocvi_clinicinfo_disorder_id
 *
 * The followings are the available model relations:
 *
 * @property Element_OphCoCvi_ClinicalInfo $element
 * @property OphCoCvi_ClinicalInfo_Disorder_Section $ophcocvi_clinicinfo_disorder_section
 *
 * @property User $user
 * @property User $usermodified
 */

class Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments extends \BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
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
        return 'et_ophcocvi_clinicinfo_disorder_section_comment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('element_id, ophcocvi_clinicinfo_disorder_section_id', 'safe'),
            array('element_id, ophcocvi_clinicinfo_disorder_section_id', 'required'),
            array('id, element_id, ophcocvi_clinicinfo_disorder_section_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element' => array(self::BELONGS_TO, 'Element_OphCoCvi_ClinicalInfo', 'element_id'),
            'ophcocvi_clinicinfo_disorder_section' => array(
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section',
                'ophcocvi_clinicinfo_disorder_section_id'
            ),
            'eye' => array(self::BELONGS_TO,'Eye','eye_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
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
            'name' => 'Name',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getDisorderSectionComments($disorder_section_id,$element_id) {
        $criteria=new \CDbCriteria;
        $criteria->select='comments';
        $criteria->condition = "element_id=:element_id";
        $criteria->addCondition("ophcocvi_clinicinfo_disorder_section_id=:disorder_section_id");
        $criteria->params = array(':element_id'=>$element_id,':disorder_section_id'=>$disorder_section_id);
        $item = Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments::model()->find($criteria);
        return $item['comments'] ? $item['comments'] : '';
    }
}
