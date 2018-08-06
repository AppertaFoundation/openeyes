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
 * Class OphDrPrescription_Item
 * @property OphDrPrescription_ItemTaper[] $tapers
 * @property Element_OphDrPrescription_Details $prescription
 * @property OphDrPrescription_DispenseCondition $dispense_condition
 * @property OphDrPrescription_DispenseLocation $dispense_location
 */

class OphDrPrescription_Item extends EventMedicationUse
{
    /**
     * Returns the static model of the specified AR class.
     */

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getUsageType()
    {
        return "OphDrPrescription";
    }

    public static function getUsageSubtype()
    {
        return "";
    }

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('dispense_location_id, dispense_condition_id', 'required'),
        ));
    }

    public function relations()
    {
        return array_merge(parent::relations(), array(
            'tapers' => array(self::HAS_MANY, OphDrPrescription_ItemTaper::class, 'item_id'),
            'prescription' => array(
                self::HAS_ONE,
                Element_OphDrPrescription_Details::class,
                array('event_id' => 'event_id'),
            ),
            'dispense_condition' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseCondition', 'dispense_condition_id'),
            'dispense_location' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseLocation', 'dispense_location_id'),
        ));
    }

    /**
     * @return string
     * Method to ensure backwards compatibility
     */

    public function getDescription()
    {
        return $this->getMedicationDisplay();
    }

    public function loadDefaults()
    {
        /* TODO How to implement this? */
        /*
        if ($this->drug) {
            $this->duration_id = $this->drug->default_duration_id;
            $this->frequency_id = $this->drug->default_frequency_id;
            $this->route_id = $this->drug->default_route_id;
            $this->dose = trim($this->drug->default_dose);
        }
        */
    }

    public function afterValidate()
    {
        foreach ($this->tapers as $i => $taper) {
            if (!$taper->validate()) {
                foreach ($taper->getErrors() as $fld => $err) {
                    $this->addError('tapers', 'Taper ('.($i + 1).'): '.implode(', ', $err));
                }
            }
        }
    }

    /**
     * @return DateTime|null
     */
    public function stopDateFromDuration()
    {
        if (in_array($this->drugDuration->name, array('Other', 'Until review'))) {
            return null;
        }

        $start_date = new DateTime($this->prescription->event->event_date);
        $end_date = $start_date->add(DateInterval::createFromDateString($this->drugDuration->name));
        foreach ($this->tapers as $taper) {
            if (in_array($taper->duration->name, array('Other', 'Until review'))) {
                return null;
            }
            $end_date->add(DateInterval::createFromDateString($taper->duration->name));
        }
        return $end_date;
    }

    public function getAdministrationDisplay()
    {
        $dose = (string) $this->dose;
        $freq = (string) $this->frequency;
        $route = (string) $this->route;

        if ($this->tapers) {
            $last_taper = array_slice($this->tapers, -1)[0];
            $last_dose = (string) $last_taper->dose;
            if ($last_dose != $dose) {
                $dose .= ' - ' . $last_dose;
            }
            $last_freq = (string) $last_taper->frequency;
            if ($last_freq != $freq) {
                $freq .= ' - ' . $last_freq;
            }
        }
        return $dose . ($this->laterality ? ' ' . $this->getLateralityDisplay() : '') . ' ' . $route . ' ' . $freq;
    }
}