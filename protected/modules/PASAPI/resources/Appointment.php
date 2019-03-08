<?php

namespace OEModule\PASAPI\resources;

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class Appointment.
 *
 * @property string $AppointmentDate
 * @property string $AppointmentTime
 * @property AppointmentMappingItems $AppointmentMappingItems
 */
class Appointment extends BaseResource
{
    protected static $resource_type = 'Appointment';

    /**
     * @return bool
     */
    public function shouldValidateRequired()
    {
        return !$this->partial_record;
    }

    public function validate()
    {
        $mapping_keys = array();
        if (isset($this->AppointmentMappingItems)) {
            foreach ($this->AppointmentMappingItems as $item) {
                if (in_array($item->Key, $mapping_keys)) {
                    $this->addError("Duplicate key {$item->Key} is not allowed.");
                }
            }
        }

        return parent::validate();
    }

    protected $default_date;
    protected $default_time;

    public function setDefaultWhen(\DateTime $when = null)
    {
        $this->default_date = $when ? $when->format('Y-m-d') : '';
        $this->default_time = $when ? $when->format('H:i') : '';
    }

    /**
     * @return \DateTime
     *
     * @throws \Exception
     */
    public function getWhen()
    {
        if (!property_exists($this, 'AppointmentDate')) {
            $this->AppointmentDate = $this->default_date;
        }
        if (!property_exists($this, 'AppointmentTime')) {
            $this->AppointmentTime = $this->default_time;
        }

        $concatenated = substr($this->AppointmentDate, 0, 10).' '.$this->AppointmentTime;

        $result = \DateTime::createFromFormat('Y-m-d H:i', $concatenated);

        if (!$result) {
            throw new \Exception("Could not parse date and time values ({$concatenated}):".print_r(\DateTime::getLastErrors(), true));
        }

        return $result;
    }

    /**
     * Parse all the mapping items and return them as a key value array.
     *
     * Note, expects resource to have been validated
     *
     * @return array
     */
    public function getMappingsArray()
    {
        $res = array();
        if (property_exists($this, 'AppointmentMappingItems')) {
            foreach ($this->AppointmentMappingItems as $item) {
                $res[$item->Key] = $item->Value;
            }
        }

        return $res;
    }
}
