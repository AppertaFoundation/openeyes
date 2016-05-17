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

class PatientId extends BaseResource
{
    static protected $resource_type = 'PatientId';

    public $isNewResource;

    protected $id_tags = array("Id", "PasId", "NHSNumber", "HospitalNumber");

    /**
     * As a primary resource (i.e. mapped to external resource) we need to ensure we have an id for tracking
     * the resource in the system
     *
     * @return bool
     */
    public function validate() {
        $has_id = false;
        foreach ($this->id_tags as $attr) {
            if ($this->$attr)
                $has_id = true;
        }
        if (!$has_id)
            $this->addError("At least one Id tag of the form " . implode(",", $this->id_tags) . " is required.");

        return parent::validate();
    }
}