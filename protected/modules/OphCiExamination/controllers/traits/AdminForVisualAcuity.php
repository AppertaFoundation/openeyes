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


use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityFixation;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityOccluder;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuitySource;

trait AdminForVisualAcuity
{
    public function actionVisualAcuityFixations()
    {
        $this->genericAdmin(
            "Visual Acuity - Fixations",
            OphCiExamination_VisualAcuityFixation::class,
            [
                "description" => "Fixation options for Visual Acuity Elements",
            ]
        );
    }

    public function actionVisualAcuityOccluders()
    {
        $this->genericAdmin(
            "Visual Acuity - Occluders",
            OphCiExamination_VisualAcuityOccluder::class,
            [
                "description" => "Occluder options for Visual Acuity Elements",
            ]
        );
    }

    public function actionVisualAcuitySources()
    {
        $this->genericAdmin(
            "Visual Acuity - Sources",
            OphCiExamination_VisualAcuitySource::class,
            [
                "description" => "Measurement Source options for Visual Acuity Elements",
                "extra_fields" => [
                    "is_near" => [
                        "field" => "is_near",
                        "type" => "boolean"
                    ]
                ]
            ]
        );
    }
}