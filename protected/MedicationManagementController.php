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

        public function actionFindRefMedications($ref_set_id, $term = '', $limit = 50)
        {
            header('Content-type: application/json');
            /** @var RefSet $ref_set */
            if(!$ref_set = RefSet::model()->find(array('condition'=>'id=:id', 'params'=>array('id'=>$ref_set_id)))) {
                echo CJSON::encode([]);
                exit;
            }

            $ret_data = [];

            $criteria = new \CDbCriteria();

            if($term !== '') {
                $criteria->condition = "refMedications.preferred_term LIKE :term";
                $criteria->params['term'] = "%$term%";
            }

            $criteria->limit = $limit > 1000 ? 1000 : $limit;
            $criteria->order = "refMedications.preferred_term";

            foreach ($ref_set->refMedications($criteria) as $med) {
                $ref_med_set = RefMedicationSet::model()
                    ->find('ref_medication_id = :ref_medication_id AND ref_set_id = :ref_set_id', [
                        'ref_medication_id' => $med->id,
                        'ref_set_id' => $ref_set->id
                    ]);

                $tabsize = 0;

                if($med->isVMP()) {
                    $tabsize = 1;
                }
                elseif ($med->isAMP()) {
                    $tabsize = 2;
                }

                $ret_data[] = array_merge($med->getAttributes(), [
                        'dose_unit_term' => $ref_med_set->default_dose_unit_term,
                        'dose' => $ref_med_set->default_dose,
                        'default_form' => $ref_med_set->default_form,
                        'frequency_id' => $ref_med_set->default_frequency,
                        'route_id' => $ref_med_set->default_route,
                        'tabsize' => $tabsize
                    ]
                );
            }

            echo CJSON::encode($ret_data);
        }
    }