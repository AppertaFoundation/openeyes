<?php
    /**
     * OpenEyes
     *
     * (C) OpenEyes Foundation, 2018
     * This file is part of OpenEyes.
     * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
     * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
     * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
     * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
     * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
     * COPYING. If not, see <http://www.gnu.org/licenses/>.
     *
     * @package OpenEyes
     * @link http://www.openeyes.org.uk
     * @author OpenEyes <info@openeyes.org.uk>
     * @copyright Copyright (c) 2018, OpenEyes Foundation
     * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
     */
    
    
    class MedicationManagementController extends BaseController
    {
        public function accessRules()
        {
            return array(
                array(
                    'allow',
                )
            );
        }

        public function actionFindRefMedications($term = '', $include_branded = 1, $limit = 50)
        {
            header('Content-type: application/json');

            $ret_data = [];

            $criteria = new \CDbCriteria();

            if($term !== '') {
                $criteria->addCondition("preferred_term LIKE :term OR medicationSearchIndexes.alternative_term LIKE :term");
                $criteria->params['term'] = "%$term%";
            }

            if($include_branded == 0) {
            	$criteria->addCondition("source_subtype != 'AMP'");
			}

            $criteria->limit = $limit > 1000 ? 1000 : $limit;
            $criteria->order = "preferred_term";
            $criteria->with = array('medicationSearchIndexes');
            $criteria->together = true;

            foreach (Medication::model()->findAll($criteria) as $med) {
                /** @var Medication $med */
              
                $crit2 = new CDbCriteria();
                $crit2->condition = 'medication_id = :med_id';
                $crit2->params['med_id'] = $med->id;
                $crit2->limit = 1;

                $infoBox = new MedicationInfoBox();
				$infoBox->medication_id = $med->id;
				$infoBox->init();
				$tooltip = $infoBox->getHTML();

				$ret_data[] = array_merge($med->getAttributes(), [
						'label' => $med->getLabel(). ($med->isMemberOf("Formulary") ? " (*)" : ""),
						'dose_unit_term' => $med->default_dose_unit_term,
						'dose' => 1,
						'default_form' => $med->default_form_id,
						'frequency_id' => null,
						'route_id' => $med->default_route_id,
						'tabsize' => null,
						'will_copy' => $med->getToBeCopiedIntoMedicationManagement(),
						'prepended_markup' => $tooltip,
						'set_ids' => array_map(function ($e){
							return $e->id;
						} , $med->getMedicationSetsForCurrentSubspecialty()),
						'allergy_ids' => array_map(function ($e) {
							return $e->id;
						}, $med->allergies),
					]
				);
            }

            echo CJSON::encode($ret_data);
        }

        public function actionGetInfoBox($medication_id)
        {
            $infoBox = new MedicationInfoBox();
            $infoBox->medication_id = $medication_id;
            $infoBox->init();
            $infoBox->run();
        }


    }