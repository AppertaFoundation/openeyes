<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\controllers;

class ReportController extends \BaseReportController
{
    public function actionIndex()
    {
        echo "OK";
    }

    /**
     * @throws \Exception
     */

    public function actionReadyForSecondEyeUnbooked()
    {
        \Audit::add('Reports', 'view', print_r(['report-name' => 'Examination ReadyForSecondEyeUnbooked'], true));
        $this->pageTitle = 'Ready for second eye (unbooked) report';
        $this->render('ready_for_second_eye_unbooked');
    }

    public function actionAE()
    {
        \Audit::add('Reports', 'view', print_r(['report-name' => 'AE'], true));
        $this->pageTitle = 'A&E Patient List';
        $this->render('ae_patient_list');
    }
}
