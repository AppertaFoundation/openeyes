<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class ReportController extends BaseReportController
{
    public $renderPatientPanel = false;

    public function accessRules()
    {
            return array(
                    array('allow',
                            'actions' => array('prescriptedDrugs','runReport','downloadReport'),
                            'roles' => array('OprnGenerateReport','admin'),
                    )
            );
    }
        
    public function init()
    {
        $modulePath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OphDrPrescription.assets'));
        Yii::app()->clientScript->registerScriptFile($modulePath . "/js/report.js", CClientScript::POS_HEAD);
    }
        

	public function actionPrescriptedDrugs()
	{
            $default_drugs = Yii::app()->db->createCommand('SELECT id, name FROM drug WHERE name
                                                                LIKE "%Methotrexate%"
                                                        OR NAME LIKE "%Mycophenolate%"
                                                        OR NAME LIKE "%Tacrolimus%"
                                                        OR NAME LIKE "%Azathioprine%"
                                                        OR NAME LIKE "%Cyclosporine%"
                                                        OR NAME LIKE "%Cyclophosphamide%"
                                                        ORDER BY NAME')->queryAll();
            
            $this->render('prescribedDrugs', array('default_drugs' => $default_drugs));
	}
}
