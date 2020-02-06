<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ReportController extends BaseReportController
{
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'diagnoses', 'runReport', 'downloadReport', 'ajaxReport', 'reportData'),
                'expression' => array('ReportController', 'checkSurgonOrRole'),
            ),

            array(
                'allow',
                'actions' => array('ajaxReport', 'reportData'),
                'expression' => 'Yii::app()->user->isSurgeon()',
            ),

        );
    }

    public function actionIndex()
    {
        $this->redirect(array('diagnoses'));
    }

    public function actionDiagnoses()
    {
        Audit::add('Reports', 'view', print_r(['report-name' => 'Diagnoses'], true));
        $this->pageTitle = 'Diagnoses Report';
        $this->render('diagnoses');
    }

    /**
     * @throws CException
     * @throws CHttpException
     */
    public function actionAjaxReport()
    {
        $reportObj = $this->loadReport();

        $this->renderPartial($reportObj->getTemplate(), array(
            'report' => $reportObj,
        ));
    }

    /**
     * @throws CHttpException
     */
    public function actionReportData()
    {
        $reportObj = $this->loadReport();

        $this->renderJSON($reportObj->dataSet());
    }

    /**
     * @return ReportInterface
     *
     * @throws CHttpException
     */
    private function loadReport()
    {
        $report = Yii::app()->request->getParam('report').'Report';
        $template = Yii::app()->request->getParam('template');
        if ($report && class_exists($report)) {
            $reportObj = new $report(Yii::app());
        } else {
            throw new CHttpException(404, 'Report not found');
        }
        if ($template == "analytics") {
            $reportObj->setTemplate('//report/plotly_report_analytics');
        }

        return $reportObj;
    }
}
