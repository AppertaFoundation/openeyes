<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCvi\seeders;

use OE\seeders\BaseSeeder;
use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder as ClinicalInfoDisorder;

/**
 * CviPINSignSeeder is a seeder for generating data used solely in the 'CVI PIN sign setting' test (cvi\13352-CVI-PIN-sign-setting-toggle.cy.js)
 */
class ClinicalDisorderSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $adult_disorder = ClinicalInfoDisorder::factory()->create([
            'name' => 'Test Adult Clinical Info Disorder',
            'term_to_display' => 'Test Adult Clinical Info Disorder',
            'code' => 'TEST.0',
            'section_id' => '10',
            'active' => 1,
            'patient_type' => ClinicalInfoDisorder::PATIENT_TYPE_ADULT
        ]);

        $child_disorder = ClinicalInfoDisorder::factory()->create([
            'name' => 'Test Child Clinical Info Disorder',
            'term_to_display' => 'Test Child Clinical Info Disorder',
            'code' => 'TEST.1',
            'section_id' => '10',
            'active' => 1,
            'patient_type' => ClinicalInfoDisorder::PATIENT_TYPE_CHILD
        ]);

        return [
            'ClinicalInfoDisorder' => [
                'PATIENT_TYPE_ADULT' => $adult_disorder,
                'PATIENT_TYPE_CHILD' => $child_disorder
            ]
        ];
    }
}
