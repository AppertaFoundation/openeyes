<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
require_once('Zend/Http/Client.php');

class PortalExamsCommand extends CConsoleCommand
{
	protected $client;

	protected $config = array();


	public function run($args)
	{
		$this->setConfig();
		$this->client = $this->initClient();
		$this->login();
		$examinations = $this->examinationSearch();
		$eventType = EventType::model()->find('name = "Examination"');
		$portalUserId = 1;//todo get portal user
		$refractionType = \OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type::model()->find('name = "Ophthalmologist"');

		$eyes = Eye::model()->findAll();
			$eyeIds = array();
			foreach($eyes as $eye){
				$eyeIds[strtolower($eye->name)] = $eye->id;
			}

		foreach($examinations as $examination){
			$uidArray = explode('-', $examination['patient']['unique_identifier']);
			$uniqueCode = $uidArray[1];
			$opNoteEvent = UniqueCodes::model()->eventFromUniqueCode($uniqueCode);
			if(!$opNoteEvent){
				echo 'No Event found for identifier: '.$examination['patient']['unique_identifier']. PHP_EOL;
				continue;
			}
			$transaction = $opNoteEvent->getDbConnection()->beginInternalTransaction();

			try {
				//Create main examination event
				$examinationEvent = new Event();
				$examinationEvent->episode_id = $opNoteEvent->episode_id;
				$examinationEvent->created_user_id = $examinationEvent->last_modified_user_id = $portalUserId;
				$examinationEvent->event_date = $examination['examination_date'];
				$examinationEvent->event_type_id = $eventType['id'];

				if($examinationEvent->save()){
					$examinationEvent->refresh();

					$refraction = new \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction();
					$refraction->event_id = $examinationEvent->id;
					$refraction->created_user_id = $refraction->last_modified_user_id = $portalUserId;

					$iop = new \OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure();
					$iop->event_id = $examinationEvent->id;
					$iop->created_user_id = $iop->last_modified_user_id = $portalUserId;
					$iop->eye_id = $eyeIds['both'];
					$iop->left_comments = 'Portal Add';
					$iop->right_comments = 'Portal Add';
					if(!$iop->save()){
						throw new CDbException('iop failed: '.print_r($iop->getErrors(), true));
					}
					$iop->refresh();

					$complications = new \OEModule\OphCiExamination\models\Element_OphCiExamination_PostOpComplications();
					$complications->event_id = $examinationEvent->id;
					$complications->created_user_id = $complications->last_modified_user_id = $portalUserId;
					$complications->eye_id = $eyeIds['both'];
					if(!$complications->save()){
						throw new CDbException('Complcaitions failed: '.print_r($iop->getErrors(), true));
					}
					$complications->refresh();

					foreach($examination['patient']['eyes'] as $eye){
						$eyeLabel = strtolower($eye['label']);

						/*$unit = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->find('name = ?', array($examination['patient']));
                      //Create visual acuity
                      $visualAcuity = new \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity();
                      $visualAcuity->event_id = $examinationEvent->id;
                      $visualAcuity->created_user_id = $visualAcuity->last_modified_user_id = $portalUserId;
                      $visualAcuity->eye_id = $both;*/

						$refractionReading = $eye['reading'][0]['refraction'];
						$typeSide = $eyeLabel.'_type_id';
						$sphereSide = $eyeLabel.'_sphere';
						$cylinderSide =  $eyeLabel .'_cylinder';
						$axisSide = $eyeLabel . '_axis';
						$refraction->$typeSide = $refractionType['id'];
						$refraction->$sphereSide = $refractionReading['sphere'];
						$refraction->$cylinderSide = $refractionReading['cylinder'];
						$refraction->$axisSide = $refractionReading['axis'];

						$iopReading = $eye['reading'][0]['iop'];
						$iopValue = new \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value();
						$iopValue->element_id = $iop->id;
						$iopValue->eye_id =  $eyeIds[$eyeLabel];
						$iopReadingValue = \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading::model()->find('value = ?', array($iopReading['mm_hg']));
						$instrument = \OEModule\OphCiExamination\models\OphCiExamination_Instrument::model()->find('name = ?', array($iopReading['instrument']));
						$iopValue->reading_id = $iopReadingValue['id'];
						$iopValue->instrument_id = $instrument['id'];
						if(!$iopValue->save()){
							throw new CDbException('iop value failed: '.print_r($iop->getErrors(), true));
						}

						if(count($eye['complications'])){
							foreach($eye['complications'] as $complicationArray){
								$eyeComplication = new \OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications();
								$eyeComplication->element_id = $complications->id;
								$complicationToAdd = \OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications::model()->find('name = "'.$complicationArray['complication'].'"');
								$eyeComplication->complication_id = $complicationToAdd->id;
								$eyeComplication->operation_note_id = $opNoteEvent->id;
								$eyeComplication->eye_id = $eyeIds[$eyeLabel];
								$eyeComplication->save();
							}
						} else {
							$eyeComplication = new \OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications();
							$eyeComplication->element_id = $complications->id;
							$complicationToAdd = \OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications::model()->find('name = "none"');
							$eyeComplication->complication_id = $complicationToAdd->id;
							$eyeComplication->operation_note_id = $opNoteEvent->id;
							$eyeComplication->eye_id = $eyeIds[$eyeLabel];
							$eyeComplication->save();
						}

					}

					$refraction->eye_id =  $eyeIds['both'];
					if(!$refraction->save()){
						throw new CDbException('Refraction failed: '.print_r($iop->getErrors(), true));
					}
				} else {
					echo 'Examination save failed: '. PHP_EOL;
					foreach($examinationEvent->getErrors() as $key => $error){
						echo $key . ' invalid: '. implode(', ', $error). PHP_EOL;
					}
				}

			}catch (Exception $e) {
				$transaction->rollback();
				echo 'Failed for examination ' . $examination['patient']['unique_identifier']. 'with exception: '.$e->getMessage(). PHP_EOL;
				continue;
			}
			$transaction->commit();
			echo 'Examination imported: '.$examinationEvent->id . PHP_EOL;
		}
	}

	protected function setConfig()
	{
		$this->config = Yii::app()->params['portal'];
	}

	protected function initClient()
	{
		$client = new Zend_Http_Client($this->config['uri']);
		$client->setHeaders('Accept', 'application/vnd.OpenEyesPortal.v1+json');

		return $client;
	}

	protected function login()
	{
		$this->client->setUri($this->config['uri'].$this->config['endpoints']['auth']);
		$this->client->setParameterPost($this->config['credentials']);
		$response = $this->client->request('POST');
		$jsonResponse = json_decode($response->getBody(), true);
		$this->client->setHeaders('Authorization', 'Bearer '.$jsonResponse['access_token']);
	}

	protected function examinationSearch()
	{
		$this->client->setUri($this->config['uri'].$this->config['endpoints']['examinations']);
		$response = $this->client->request('POST');
		$jsonResponse = json_decode($response->getBody(), true);

		return $jsonResponse;
	}
}