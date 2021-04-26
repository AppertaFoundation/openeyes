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

use OEModule\OphCiExamination\models\CoverAndPrismCover_Correction;
use OEModule\OphCiExamination\models\CoverAndPrismCover_Distance;
use OEModule\OphCiExamination\models\CoverAndPrismCover_HorizontalPrism;
use OEModule\OphCiExamination\models\CoverAndPrismCover_VerticalPrism;

trait AdminForCoverAndPrismCover
{
    public function actionCoverAndPrismCoverCorrection()
    {
        $this->genericAdmin('Cover and Prism Cover - Correction',
            CoverAndPrismCover_Correction::class,
            [
                'description' => 'Correction Types for Cover and Prism Cover',
            ]
        );
    }

    public function actionCoverAndPrismCoverDistance()
    {
        $this->genericAdmin('Cover and Prism Cover - Distance',
            CoverAndPrismCover_Distance::class,
            [
                'description' => 'Distance options for Cover and Prism Cover',
            ]
        );
    }

    public function actionCoverAndPrismCoverHorizontalPrism()
    {
        $this->genericAdmin('Cover and Prism Cover - Horizontal Prism',
            CoverAndPrismCover_HorizontalPrism::class,
            [
                'description' => 'Horizontal Prism options for Cover and Prism Cover',
            ]
        );
    }

    public function actionCoverAndPrismCoverVerticalPrism()
    {
        $this->genericAdmin('Cover and Prism Cover - Vertical Prism',
            CoverAndPrismCover_VerticalPrism::class,
            [
                'description' => 'Vertical Prism options for Cover and Prism Cover',
            ]
        );
    }
}