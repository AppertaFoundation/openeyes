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

use OEModule\OphCiExamination\models\PrismReflex_Finding;
use OEModule\OphCiExamination\models\PrismReflex_PrismBase;
use OEModule\OphCiExamination\models\PrismReflex_PrismDioptre;

trait AdminForPrismReflex
{
    public function actionDioptrePrismFinding()
    {
        $this->genericAdmin('Prism Reflex - Finding',
            PrismReflex_Finding::class,
            [
                'description' => 'Finding Options for Prism Reflex',
            ]
        );
    }

    public function actionDioptrePrismPrismBase()
    {
        $this->genericAdmin('Prism Reflex - Distance',
            PrismReflex_PrismBase::class,
            [
                'description' => 'Prism Base Options for Prism Reflex',
            ]
        );
    }

    public function actionDioptrePrismPrismDioptre()
    {
        $this->genericAdmin('Prism Reflex - Prism Dioptre',
            PrismReflex_PrismDioptre::class,
            [
                'description' => 'Prism Dioptre Options for Prism Reflex',
            ]
        );
    }
}