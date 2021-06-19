<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\controllers\traits;


use OEModule\OphCiExamination\models\StrabismusManagement_Treatment;
use OEModule\OphCiExamination\models\StrabismusManagement_TreatmentOption;
use OEModule\OphCiExamination\models\StrabismusManagement_TreatmentReason;

trait AdminForStrabismusManagement
{

    public function actionStrabismusManagementTreatments()
    {
        $this->genericAdmin(
            'Strabismus Management Treatments',
            StrabismusManagement_Treatment::class,
            [
                'extra_fields' => [
                    "reason_required" => [
                        "field" => "reason_required",
                        "type" => "boolean"
                    ],
                    "column1_multiselect" => [
                        "field" => "column1_multiselect",
                        "type" => "boolean"
                    ],
                    "column2_multiselect" => [
                        "field" => "column2_multiselect",
                        "type" => "boolean"
                    ]
                ],
                'action_links' => [
                    [
                        'label' => "Options",
                        'url' => function ($treatment) {
                            return "/OphCiExamination/admin/StrabismusManagementTreatmentOptions?treatment_id=" . $treatment->id;
                        }
                    ]
                ]
            ]
        );
    }

    public function actionStrabismusManagementTreatmentOptions()
    {
        $this->genericAdmin(
            'Strabismus Management Treatment Options',
            StrabismusManagement_TreatmentOption::class,
            [
                'extra_fields' => [
                    "column_number" => [
                        "field" => "column_number",
                        "type" => "choice",
                        "allow_null" => false,
                        "choices" => [
                            1 => "Column 1",
                            2 => "Column 2"
                        ]
                    ]
                ],
                'filter_fields' => [
                    [
                        'field' => 'treatment_id',
                        'model' => StrabismusManagement_Treatment::class
                    ],
                    [
                        'field' => 'column_number',
                        'choices' => [
                            1 => "Column 1",
                            2 => "Column 2"
                        ]
                    ]
                ]
            ]
        );
    }

    public function actionStrabismusManagementReasons()
    {
        $this->genericAdmin(
            'Strabismus Management Reasons',
            StrabismusManagement_TreatmentReason::class
        );
    }
}