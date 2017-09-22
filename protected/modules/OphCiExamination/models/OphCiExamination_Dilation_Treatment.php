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
 * This is the model class for table "ophciexamination_dilation_treatment".
 *
 * @property int $id
 * @property int $element_id
 * @property int $side
 * @property int $drug_id
 * @property int $drops
 */
class OphCiExamination_Dilation_Treatment extends \BaseActiveRecordVersioned
{
    const LEFT = 1;
    const RIGHT = 0;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_Dilation_Treatment the static model class
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
        return 'ophciexamination_dilation_treatment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('side, drug_id, drops, element_id, treatment_time', 'safe'),
                array('treatment_time', 'isValidTimeValue'),
                array('id, side, drug_id, drops, element_id, treatment_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element' => array(self::BELONGS_TO, 'Element_OphCiExamination_Dilation', 'element_id'),
            'drug' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Dilation_Drugs', 'drug_id'),
        );
    }

    public function setDefaultOptions(\Patient $patient = null)
    {
        $this->treatment_time = date('H:i');
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

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Checks that the field is a valid time.
     *
     * @param $attribute
     * @param $params
     */
    public function isValidTimeValue($attribute, $params)
    {
        if (!preg_match('/^(([01]?[0-9])|(2[0-3])):?[0-5][0-9]$/', $this->$attribute)) {
            $this->addError($attribute, 'Invalid treatment time');
        }
    }
}
