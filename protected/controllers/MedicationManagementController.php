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

        public function actionFindRefMedications($term = '', $limit = 50)
        {
            header('Content-type: application/json');
            /** @var MedicationSet $medication_set */
            if(!$medication_sets = MedicationSet::model()->findAll('id IN (SELECT medication_set_id FROM medication_set_rule WHERE usage_code =\'Formulary\')')) {
                echo CJSON::encode([]);
                exit;
            }

            $ret_data = [];

            $criteria = new \CDbCriteria();

            if($term !== '') {
                $criteria->condition = "medications.preferred_term LIKE :term OR medicationSearchIndexes.alternative_term LIKE :term";
                $criteria->params['term'] = "%$term%";
            }

            $criteria->limit = $limit > 1000 ? 1000 : $limit;
            $criteria->order = "medications.preferred_term";
            $criteria->with = 'medicationSearchIndexes';

            foreach ($medication_sets as $medication_set) {
                foreach ($medication_set->medications($criteria) as $med) {
                    /** @var MedicationSetItem $med_set_item */
                    $med_set_item = MedicationSetItem::model()
                        ->find('medication_id = :medication_id AND medication_set_id = :medication_set_id', [
                            'medication_id' => $med->id,
                            'medication_set_id' => $medication_set->id
                        ]);

                    $tabsize = 0;

                    if($med->isVMP()) {
                        $tabsize = 1;
                    }
                    elseif ($med->isAMP()) {
                        $tabsize = 2;
                    }

                    $infoBox = new MedicationInfoBox();
                    $infoBox->medication_id = $med->id;
                    $infoBox->init();
                    $tooltip = $infoBox->getHTML();

                    $ret_data[] = array_merge($med->getAttributes(), [
                            'label' => $med->preferred_term,
                            'dose_unit_term' => $med_set_item->default_dose_unit_term,
                            'dose' => $med_set_item->default_dose,
                            'default_form' => $med_set_item->default_form_id,
                            'frequency_id' => $med_set_item->default_frequency_id,
                            'route_id' => $med_set_item->default_route_id,
                            'tabsize' => $tabsize,
                            'will_copy' => $med->getToBeCopiedIntoMedicationManagement(),
                            'prepended_markup' => $tooltip
                        ]
                    );
                }
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