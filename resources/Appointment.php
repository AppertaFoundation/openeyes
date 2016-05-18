<?php namespace OEModule\PASAPI\resources;

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

/**
 * Class Appointment
 * @package OEModule\PASAPI\resources
 *
 * @property string $AppointmentDate
 * @property string $AppointmentTime
 * @property MappingItems $MappingItems
 */
class Appointment extends BaseResource
{
    static protected $resource_type = 'Appointment';

    /**
     * @return \DateTime
     */
    public function getWhen()
    {
        $result = \DateTime::createFromFormat('Y-m-d H:i:00', substr($this->AppointmentDate,0,10) . $this->AppointmentTime);

        if (!$result)
            throw new \Exception("Could not parse date and time values");

        return $result;
    }

}