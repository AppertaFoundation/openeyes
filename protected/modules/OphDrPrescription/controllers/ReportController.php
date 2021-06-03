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
    public $renderPatientPanel = false;

    public $subspecialtyId = null;
    public $siteId = null;

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('getDrugsBySubspecialty', 'prescribedDrugs', 'runReport', 'downloadReport'),
                'expression' => array('ReportController', 'checkSurgonOrRole'),
            ),
        );
    }

    public function init()
    {
        $modulePath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OphDrPrescription.assets'), true);
        Yii::app()->clientScript->registerScriptFile($modulePath.'/js/report.js', CClientScript::POS_HEAD);

        if (!$this->subspecialtyId) {
            $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
            if (isset($firm->serviceSubspecialtyAssignment->subspecialty_id)) {
                $this->subspecialtyId = $firm->serviceSubspecialtyAssignment->subspecialty_id;
            }
        }
        
        if (!$this->siteId) {
            $this->siteId = Yii::app()->session['selected_site_id'];
        }
    }

    public function actionPrescribedDrugs()
    {
        $drugs = Element_OphDrPrescription_Details::model()->commonDrugs();
        $users = User::model()->findAll(array('order' => 'first_name asc,last_name asc'));
        $dispense_conditions = OphDrPrescription_DispenseCondition::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION);

        Audit::add('Reports', 'view', print_r(['report-name' => 'Prescribed Drugs'], true));
        $this->pageTitle = 'Prescribed Drugs report';
        $this->render('prescribedDrugs', array('drugs' => $drugs, 'users' => $users, 'dispense_conditions' => $dispense_conditions));
    }

    public function actionGetDrugs()
    {
        $commonDrugs = array();
        $drugs = Element_OphDrPrescription_Details::model()->commonDrugs();

        if ($drugs) {
            foreach ($drugs as $drug) {
                $commonDrugs[] = array(
                    'id' => $drug->id,
                    'label' => $drug->preferred_term,
                );
            }
        }
        $this->renderJSON($commonDrugs);
        Yii::app()->end();
    }
}
