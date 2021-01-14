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
 * This is the model class for table "ophciexamination_visual_acuity_unit".
 *
 * @property int $id
 * @property string $name
 * @property OphCiExamination_VisualAcuityUnitValue[] $values
 * @property OphCiExamination_VisualAcuityUnitValue[] $selectableValues
 */
class OphCiExamination_VisualAcuityUnit extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_VisualAcuityUnit the static model class
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
        return 'ophciexamination_visual_acuity_unit';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.name');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('name', 'required'),
                array('id, name', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'values' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue', 'unit_id', 'order' => 'base_value ASC'),
                'selectableValues' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue', 'unit_id', 'on' => 'selectableValues.selectable = true', 'order' => 'base_value ASC'),
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
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
        $criteria->compare('name', $this->name, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function __toString()
    {
        return $this->name ?? parent::__toString();
    }

    /**
     * Moves the values for CF, HM, PL, NPL for better graphing
     * @param $val float|int value of Visual Acuity to be adjusted
     * @return float|int adjusted value
     */
    public function getAdjustedVA($val)
    {
        return $val > 4 ? $val : ($val-2) * 10;
    }

    public function getVAUnit($unit_id){
        $va_unit_id = isset($unit_id)? $unit_id: Element_OphCiExamination_VisualAcuity::model()->getSetting('unit_id');
        $va_unit = $this->findByPk($va_unit_id);

        return $va_unit;
    }

    public function getInitVaTicks($va_unit){
        foreach ($va_unit->selectableValues as $value) {
            $va_ticks[] = array($this->getAdjustedVA($value->base_value), $value->value);
        }

        if ($va_ticks[0][1] !== 'NPL') {
            array_unshift($va_ticks, [$this->getAdjustedVA(4), 'CF']);
            array_unshift($va_ticks, [$this->getAdjustedVA(3), 'HM']);
            array_unshift($va_ticks, [$this->getAdjustedVA(2), 'PL']);
            array_unshift($va_ticks, [$this->getAdjustedVA(1), 'NPL']);
        }

        return $va_ticks;
    }


    public function sliceVATicks($va_ticks, $gap) {
        $va_len = sizeof($va_ticks);
        $step = $va_len/$gap;
        $no_numeric_val_count = 4;   //keep the 4 no number labels: CF, HM, PL, NPL

        $new_ticks = array_slice($va_ticks, 0, $no_numeric_val_count);

        for ($i = $no_numeric_val_count+1; $i<=$va_len-$step; $i+=$step) {
            array_push($new_ticks, $va_ticks[$i]);
        }

        $tick_data = array('tick_position'=> array(), 'tick_labels'=> array());
        foreach ($new_ticks as $tick) {
            array_push($tick_data['tick_position'], (float)$tick[0]);
            array_push($tick_data['tick_labels'], $tick[1]);
        }
        return $tick_data;
    }
}
