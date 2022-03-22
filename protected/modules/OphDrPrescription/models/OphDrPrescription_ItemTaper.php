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

/**
 * The followings are the available columns in table 'ophdrprescription_item_taper':.
 *
 * @property int $id
 * @property int $frequency_id
 * @property int $duration_id
 * @property int $item_id
 * @property string $dose
 * @property MedicationDuration $duration
 * @property MedicationFrequency $frequency
 * @property OphDrPrescription_Item $item
 */
class OphDrPrescription_ItemTaper extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphDrPrescription_ItemTaper the static model class
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
        return 'ophdrprescription_item_taper';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('frequency_id, duration_id', 'required'),
            array('dose, item_id, id', 'safe'),
            array('dose', 'numerical'),
            //array('', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, item_id, dose, frequency_id, duration_id', 'safe', 'on' => 'search'),
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
            'item' => array(self::BELONGS_TO, 'OphDrPrescription_Item', 'item_id'),
            'duration' => array(self::BELONGS_TO, MedicationDuration::class, 'duration_id'),
            'frequency' => array(self::BELONGS_TO, MedicationFrequency::class, 'frequency_id'),
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
            'duration_id' => 'Duration',
            'frequency_id' => 'Frequency',
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

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('dose', $this->dose, true);
        $criteria->compare('item_id', $this->item_id, true);
        $criteria->compare('duration_id', $this->duration_id, true);
        $criteria->compare('frequency_id', $this->frequency_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getDescription()
    {
        $return = 'then ' . $this->dose;
        $return .= ' ' . $this->frequency->term;
        $return .= ' for ' . $this->duration->name;

        return $return;
    }

    public function compareTo(OphDrPrescription_ItemTaper $taper)
    {
        foreach (['frequency_id', 'duration_id', 'dose'] as $attribute) {
            if ($this->$attribute !== $taper->$attribute) {
                return false;
            }
        }
        return true;
    }


    public function fpTenFrequency()
    {
        if (preg_match("/^\d+/", $this->duration->name)) {
            return 'FREQUENCY: ' . strtoupper($this->frequency->term) . ' FOR ' . strtoupper($this->duration->name);
        }

        return 'FREQUENCY: ' . strtoupper($this->frequency->term) . ' ' . strtoupper($this->duration->name);
    }

    public function fpTenDose()
    {
        return 'DOSE: ' . (is_numeric($this->dose) ? strtoupper($this->dose) . ' ' . strtoupper($this->item->dose_unit_term) : strtoupper($this->dose))
            . ', ' . strtoupper($this->item->route->term) . ($this->item->medicationLaterality ? ' (' . strtoupper($this->item->medicationLaterality->name) . ')' : null);
    }

    /**
     * Gets dosage for taper
     *
     * @return string
     */
    public function getDosage(): string
    {
        if ($this->dose) {
            if ($this->item->dose_unit_term) {
                return $this->dose . ' ' . $this->item->dose_unit_term;
            }

            return $this->dose;
        }

        return '';
    }

    /**
     * @param $current_date
     * @return DateTime|null
     */
    public function stopDateFromDuration($current_date)
    {
        if (in_array($this->duration->name, array('Other', 'Ongoing')) || is_null($this->item->prescription->event)) {
            return null;
        }

        if ($this->duration->name === 'Once') {
            return $current_date;
        }

        return $current_date->add(DateInterval::createFromDateString($this->duration->name));
    }

    /*
     * Get description with long frequency name
     */
    public function getDescriptionLongFrequency()
    {
        $return = ', then ' . Helper::formatPluralForDose($this->dose);
        $return .= ' ' . $this->frequency->long_name;
        $return .= ' for '.$this->duration->name;

        return $return;
    }
}
