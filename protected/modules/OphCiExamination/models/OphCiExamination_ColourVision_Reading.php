<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "ophciexamination_colourvision_reading".
 *
 * @property integer $id
 * @property integer $element_id
 * @property integer $eye_id
 * @property integer $value_id
 *
 * The followings are the available model relations:
 *
 * @property Element_OphCiExamination_ColourVision $element
 * @property \Eye $eye
 * @property OphCiExamination_ColourVision_Value $value
 * @property OphCiExamination_ColourVision_Method $method
 */
class OphCiExamination_ColourVision_Reading extends \BaseActiveRecordVersioned
{
    const LEFT = 1;
    const RIGHT = 0;

    /**
     * Returns the static model of the specified AR class.
     * @return OphCiExamination_Dilation_Treatment the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_colourvision_reading';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('eye_id, value_id, element_id', 'safe'),
                array('id, eye_id, value_id, element_id', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision', 'element_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'value' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Value', 'value_id'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new \CDbCriteria;
        $criteria->compare('id', $this->id, true);
        return new \CActiveDataProvider(get_class($this), array(
                'criteria'=>$criteria,
        ));
    }

    /**
     * Get the method for this reading if it has one
     *
     * @return OphCiExamination_ColourVision_Method
     */
    public function getMethod()
    {
        if ($val = $this->value) {
            return $val->method;
        }
    }
}
