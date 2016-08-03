<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCoCvi\controllers;
use OEModule\OphCoCvi\models;

class DefaultController extends \BaseEventTypeController
{

    public $event_prompt;
    
    public $cvi_limit = 2;

    public function actionCreate()
	{
        
        $errors = array();
		if (isset($_GET['createnewcvi'])) {
            $cancel_url = ($this->episode) ? '/patient/episode/'.$this->episode->id
                : '/patient/episodes/'.$this->patient->id;
            ($_GET['createnewcvi'] == 1) ? parent::actionCreate()
                : $this->redirect(array($cancel_url));
		}
		else {
			$cvis_created = models\Element_OphCoCvi_EventInfo::model()->patientCviCount($this->patient->id);
			if(count($cvis_created) >= $this->cvi_limit) {
				$cvis_url = array();
				foreach($cvis_created as $cvi_event) {
					$cvis_url[] = '/OphCoCvi/default/view/'.$cvi_event['event_id'];
				}
			   $this->render('select_event',array(
					'cvi_url' => $cvis_url,
				), false, true);
			}
			else {
				parent::actionCreate();
			}
		}
	}

	protected function setElementDefaultOptions_Element_OphCoCvi_ClinicalInfo(models\Element_OphCoCvi_ClinicalInfo $element, $action)
	{
		if($action == 'create')
		{
			$exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
			$examination_date = $exam_api->getMostRecentVAElementForPatient($this->patient,'OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity');
			$element->examination_date = $examination_date['event_date'];
			$element->best_corrected_right_va =  $exam_api->getMostRecentVAForPatient($this->patient, 'right', 'aided');
			$element->best_corrected_left_va =  $exam_api->getMostRecentVAForPatient($this->patient, 'left', 'aided');
			$element->unaided_right_va =  $exam_api->getMostRecentVAForPatient($this->patient, 'right', 'unaided');
			$element->unaided_left_va =  $exam_api->getMostRecentVAForPatient($this->patient, 'left', 'unaided');
		}
	}

	protected function setElementDefaultOptions_Element_OphCoCvi_DemographicInfo()
	{
		
	}
	
	public function actionUpdate($id)
	{
		parent::actionUpdate($id);
	}

	public function actionView($id)
	{
		parent::actionView($id);
	}

	public function actionPrint($id)
	{
		parent::actionPrint($id);
	}
}
