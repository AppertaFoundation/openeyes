<?php

/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OphInBiometry\Service;

class MeasurementIOLMaster extends \Service\Resource
{
    public $last_name;
    public $first_name;
    public $middle_name;
    public $name_prefix;
    public $name_suffix;
    public $patient_id;
    public $patients_birth_date;
    public $patients_comment;
    public $patients_priv_id;
    public $measurement_date;
    public $r_sphere;
    public $r_cylinder;
    public $r_axis;
    public $r_visual_acuity;
    public $r_eye_state;
    public $r_axial_length_mean;
    public $r_axial_length_cnt;
    public $r_axial_length_std;
    public $r_axial_length_changed;
    public $r_radius_se_mean;
    public $r_radius_se_cnt;
    public $r_radius_se_std;
    public $r_radius_r1;
    public $r_radius_r2;
    public $r_radius_r1_axis;
    public $r_radius_r2_axis;
    public $r_acd_mean;
    public $r_acd_cnt;
    public $r_acd_std;
    public $r_wtw_mean;
    public $r_wtw_cnt;
    public $r_wtw_std;
    public $l_sphere;
    public $l_cylinder;
    public $l_axis;
    public $l_visual_acuity;
    public $l_eye_state;
    public $l_axial_length_mean;
    public $l_axial_length_cnt;
    public $l_axial_length_std;
    public $l_axial_length_changed;
    public $l_radius_se_mean;
    public $l_radius_se_cnt;
    public $l_radius_se_std;
    public $l_radius_r1;
    public $l_radius_r2;
    public $l_radius_r1_axis;
    public $l_radius_r2_axis;
    public $l_acd_mean;
    public $l_acd_cnt;
    public $l_acd_std;
    public $l_wtw_mean;
    public $l_wtw_cnt;
    public $l_wtw_std;
    public $refractive_index;
    public $iol_machine_id;
    public $iol_poll_id;

    /**
     * @param type $fhirObject
     *
     * @return type
     */
    public static function fromFhir($fhirObject)
    {
        $report = parent::fromFhir($fhirObject);

        foreach ($fhirObject as $key => $value) {
            if ($key == 'resourceType') {
                continue;
            }
            $report->{$key} = $value;
        }

        return $report;
    }
}
