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

    public function actionGetDrugSetForm($set_id, $allergy_ids)
    {
        $allergy_ids = CJSON::decode($allergy_ids);
        $medication_set = MedicationSet::model()->findByPk($set_id);
        if ($medication_set) {
            $items = $medication_set->items;
            if ($items) {
                $set_items = array();
                foreach ($items as $item) {
                    if (is_a($item, 'MedicationSetItem')) {
                        $set_items[] = $this->extractEntryFromSet($item, $allergy_ids);
                    }
                }
                echo CJSON::encode($set_items);
            }
        } else {
            throw new \CHttpException(404, 'Could not find medication set.');
        }
    }
    public function actionGetPGDSetForm($pgd_id, $allergy_ids, $key)
    {
        $allergy_ids = CJSON::decode($allergy_ids);
        $pgd_set = \OphDrPGDPSD_PGDPSD::model()->findByPk($pgd_id);
        $user_id = \Yii::app()->user->id;
        $user = User::model()->findByPk($user_id);
        if ($pgd_set) {
            $items = $pgd_set->assigned_meds;
            if ($items) {
                $pgd_items = array();
                foreach ($items as $item) {
                    $temp = array();
                    $info_box = new MedicationInfoBox();
                    $info_box->medication_id = $item->medication_id;
                    $info_box->init();
                    $tooltip = $info_box->getHTML();
                    $temp['medication_id'] = $item->medication_id;
                    $temp['medication_name'] = $item->medication->preferred_term;
                    $temp['source_subtype'] = $item->medication->source_subtype;
                    $temp['pgdpsd_id'] = $item->pgdpsd_id;
                    $temp['dose'] = $item->dose;
                    $temp['dose_unit_term'] = $item->dose_unit_term;
                    $temp['route_id'] = $item->route_id;
                    $temp['frequency_id'] = $item->frequency_id;
                    $temp['duration_id'] = $item->duration_id;
                    $temp['dispense_condition_id'] = $item->dispense_condition_id;
                    $temp['dispense_location_id'] = $item->dispense_location_id;
                    $temp['comments'] = $item->comments;
                    $temp['to_be_copied'] = true;
                    $temp['will_copy'] = true;
                    $temp['prepended_markup'] = $tooltip;
                    $temp['pgd_info_icon'] = "<span class='highlighter inline js-has-tooltip' data-tooltip-content='PGD: <b>{$pgd_set->name}</b><br/>{$user->getFullName()}'>PGD</span>";
                    $temp['pgdpsd_id'] = $pgd_set->id;
                    $temp['allergy_ids'] =  array_map(function ($allergy) use ($allergy_ids) {
                        if (in_array($allergy->id, $allergy_ids)) {
                            return $allergy->id;
                        }
                    }, $item->medication->allergies);
                    $pgd_items[] = $temp;
                }
                echo CJSON::encode($pgd_items);
            }
        } else {
            throw new \CHttpException(404, 'Could not find medication set.');
        }
    }

    private function extractEntryFromSet($set_item, $allergy_ids)
    {
        $item = array();

        $item['medication_id'] = (int) $set_item->medication_id;
        $item['medication_name'] = $set_item->medication->preferred_term;
        $item['source_subtype'] = $set_item->medication->source_subtype;
        $item['frequency_id'] = (int) $set_item->default_frequency_id;
        $item['default_form'] = (int) ($set_item->default_form_id ? $set_item->default_form_id : $set_item->medication->default_form_id);
        $item['dose'] = $set_item->default_dose ? $set_item->default_dose: $set_item->medication->default_dose;
        $item['dose_unit_term'] = $set_item->default_dose_unit_term ? $set_item->default_dose_unit_term : $set_item->medication->default_dose_unit_term;
        $item['route_id'] = (int) ($set_item->default_route_id ? $set_item->default_route_id : $set_item->medication->default_route_id);
        $item['duration_id'] = (int) $set_item->default_duration_id;
        $item['dispense_condition_id'] = (int) $set_item->default_dispense_condition_id;
        $item['dispense_location_id'] = (int) $set_item->default_dispense_location_id;
        $item['to_be_copied'] = true;
        $item['will_copy'] = true;
        $item['allergy_ids'] =  array_map(function ($allergy) use ($allergy_ids) {
            if (in_array($allergy->id, $allergy_ids)) {
                return $allergy->id;
            }
        }, $set_item->medication->allergies);

        if ($set_item->tapers) {
            $tapers = array();
            foreach ($set_item->tapers as $taper) {
                $taper_model = array();
                $taper_model['duration_id'] = (int) ($taper->duration_id ? $taper->duration_id : $item['duration_id']);
                $taper_model['frequency_id'] = (int) ($taper->frequency_id ? $taper->frequency_id : $item['frequency_id']);
                $taper_model['dose'] = $taper->dose ? $taper->dose : $item['dose'];
                $tapers[] = $taper_model;
            }
            $item['tapers'] = $tapers;
        }

        return $item;
    }

    public function actionFindRefMedications($term = '', $include_branded = 1, $limit = 100)
    {
        $ret_data = [];
        $criteria = new \CDbCriteria();

        if ($term !== '') {
            $criteria->addCondition("preferred_term LIKE :term OR medicationSearchIndexes.alternative_term LIKE :term");
            $criteria->params['term'] = "%$term%";
        }

        if ($include_branded == 0) {
            $criteria->addCondition("source_subtype != 'AMP'");
        }

        $criteria->limit = $limit > 1000 ? 1000 : $limit;
        $criteria->order = "preferred_term";
        $criteria->with = array('medicationSearchIndexes', 'allergies');
        $criteria->addCondition("t.deleted_date IS NULL");
        $criteria->together = true;

        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        $site_id = Yii::app()->session['selected_site_id'];

        $source = \Yii::app()->request->getParam('source');
        $prescribable_sets = \MedicationSet::model()->findByUsageCode('PRESCRIBABLE_DRUGS', $site_id, $subspecialty_id);
        $prescribable_set_ids = array_map(fn($set) => $set->id, $prescribable_sets);

        // prescription(event) and MedicationManagement(element) where the request is coming from
        // we should find a better solution to make a restriction by source (event, element, etc)
        if ($prescribable_sets && ($source === 'prescription' || $source === 'MedicationManagement')) {
            $criteria->addInCondition('medicationSet.id', $prescribable_set_ids);
            $criteria->with = array_merge($criteria->with, ['medicationSetItems.medicationSet.medicationSetRules']);
        }

        // use Medication::model()->prescribable()->findAll() to find only prescribable medications
        // this will need to be used in prescription Adder dialog
        foreach (Medication::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, $criteria) as $med) {
            $info_box = new MedicationInfoBox();
            $info_box->medication_id = $med->id;
            $info_box->init();
            $tooltip = $info_box->getHTML();

            $defaults = $this->getMedicationDefaults($med, $this->getCommonDrugsRefSet());

            $ret_data[] = array_merge($med->getAttributes(), [
                    'label' => $med->getLabel(true),
                    'dose_unit_term' => $defaults->dose_unit_term,
                    'dose' => $defaults->dose,
                    'default_form' => $defaults->form_id,
                    'frequency_id' => $defaults->frequency_id,
                    'route_id' => $defaults->route_id,
                    'route' => "$defaults->route",
                    'is_eye_route' => $defaults->route ? $defaults->route->isEyeRoute() : false,
                    'tabsize' => null,
                    'will_copy' => $med->getToBeCopiedIntoMedicationManagement(),
                    'prepended_markup' => $tooltip,
                    'set_ids' => array_map(function ($e) {
                        return $e->id;
                    }, $med->getMedicationSetsForCurrentSubspecialty()),
                    'allergy_ids' => array_map(function ($e) {
                        return $e->id;
                    }, $med->allergies),
                ]);
        }

        header('Content-type: application/json');
        echo CJSON::encode($ret_data);
    }

    public function getMedicationDefaults(Medication $medication, MedicationSet $set = null)
    {
        $defaults = false;

        if (!is_null($set)) {
            $defaults = MedicationSetItem::model()->find(array(
                'condition' => 'medication_set_id = :med_set_id AND medication_id = :medication_id',
                'params' => array(':med_set_id' => $set->id, ':medication_id' => $medication->id)
            ));
        }

        $r = new stdClass();

        if ($defaults) {
            $r->frequency_id = $defaults->default_frequency_id;
            $r->route_id = $defaults->default_route_id ? $defaults->default_route_id : $medication->default_route_id;
            $r->dose = $defaults->default_dose ? $defaults->default_dose : $medication->default_dose;
            $r->dose_unit_term = $defaults->default_dose_unit_term ? $defaults->default_dose_unit_term : $medication->default_dose_unit_term;
            $r->form_id = $defaults->default_form_id ? $defaults->default_form_id : $medication->default_form_id;
            $r->route = $defaults->default_route_id ? $defaults->defaultRoute : $medication->defaultRoute;
        } else {
            $r->frequency_id = null;
            $r->route_id = $medication->default_route_id;
            $r->dose = $medication->default_dose;
            $r->dose_unit_term = $medication->default_dose_unit_term;
            $r->form_id = $medication->default_form_id;
            $r->route = $medication->defaultRoute;
        }

        return $r;
    }

    public function getCommonDrugsRefSet()
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        $site_id = Yii::app()->session['selected_site_id'];
        $usage_code = \MedicationUsageCode::model()->findByAttributes(['usage_code' => 'COMMON_OPH']);
        $rule = MedicationSetRule::model()->findByAttributes(array(
            'subspecialty_id' => $subspecialty_id,
            'site_id' => $site_id,
            'usage_code_id' => $usage_code->id
        ));
        if ($rule) {
            return $rule->medicationSet;
        }

        return null;
    }

    public function actionGetInfoBox($medication_id)
    {
        $info_box = new MedicationInfoBox();
        $info_box->medication_id = $medication_id;
        $info_box->init();
        $info_box->run();
    }
    public function actionGetPGDIcon($pgdpsd_id)
    {
        $pgd = \OphDrPGDPSD_PGDPSD::model()->findByPk($pgdpsd_id);
        if ($pgd) {
            echo "<i class='oe-i info small pad js-has-tooltip' data-tooltip-content='From PGD {$pgd->id}: {$pgd->name}'></i>";
            return;
        }
        echo null;
    }
}
