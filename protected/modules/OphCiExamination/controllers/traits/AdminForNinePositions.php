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

use OEModule\OphCiExamination\models\NinePositions_HorizontalEDeviation;
use OEModule\OphCiExamination\models\NinePositions_HorizontalXDeviation;
use OEModule\OphCiExamination\models\NinePositions_Movement;
use OEModule\OphCiExamination\models\NinePositions_VerticalDeviation;

trait AdminForNinePositions
{
    public function actionNinePositionsMovement()
    {
        $this->genericAdmin(
            'Nine Positions Ocular Movements',
            NinePositions_Movement::class,
        );
    }

    public function actionNinePositionsHorizontalEDeviation()
    {
        $this->genericAdmin(
            'Nine Positions Horizontal E Deviation',
            NinePositions_HorizontalEDeviation::class,
            [
                'extra_fields' => ["abbreviation" => [
                    "field" => "abbreviation",
                    "type" => "text"
                ]]
            ]
        );
    }

    public function actionNinePositionsHorizontalXDeviation()
    {
        $this->genericAdmin(
            'Nine Positions Horizontal X Deviation',
            NinePositions_HorizontalXDeviation::class,
            [
                'extra_fields' => ["abbreviation" => [
                    "field" => "abbreviation",
                    "type" => "text"
                ]]
            ]
        );
    }

    public function actionNinePositionsVerticalDeviation()
    {
        $this->genericAdmin(
            'Nine Positions Vertical Deviation',
            NinePositions_VerticalDeviation::class,
            [
                'extra_fields' => ["abbreviation" => [
                    "field" => "abbreviation",
                    "type" => "text"
                ]]
            ]
        );
    }
}
