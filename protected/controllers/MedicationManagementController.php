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
        $criteria->with = array('medicationSearchIndexes');
        $criteria->addCondition("deleted_date IS NULL");
        $criteria->together = true;

        // use Medication::model()->prescribable()->findAll() to find only prescribable medications
        // this will need to be used in prescription Adder dialog
        foreach (Medication::model()->findAll($criteria) as $med) {
            $info_box = new MedicationInfoBox();
            $info_box->medication_id = $med->id;
            $info_box->init();
            $tooltip = $info_box->getHTML();

            $defaults = $this->getMedicationDefaults($med, $this->getCommonDrugsRefSet());

            $ret_data[] = array_merge($med->getAttributes(), [
                    'label' => $med->getLabel() . ($med->isMemberOf("Formulary") ? " (*)" : ""),
                    'dose_unit_term' => $defaults->dose_unit_term,
                    'dose' => $defaults->dose,
                    'default_form' => $defaults->form_id,
                    'frequency_id' => $defaults->frequency_id,
                    'route_id' => $defaults->route_id,
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

        header('Content-type: application/json');
        echo CJSON::encode($ret_data);
    }

    public function actionGetInfoBox($medication_id)
    {
        $info_box = new MedicationInfoBox();
        $info_box->medication_id = $medication_id;
        $info_box->init();
        $info_box->run();
    }

    public function getCommonDrugsRefSet()
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        $site_id = Yii::app()->session['selected_site_id'];
        $rule = MedicationSetRule::model()->findByAttributes(array(
            'subspecialty_id' => $subspecialty_id,
            'site_id' => $site_id,
            'usage_code' => 'COMMON_OPH'
        ));
        if ($rule) {
            return $rule->medicationSet;
        }
        else {
            return null;
        }
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
            $r->dose = $defaults->default_dose;
            $r->dose_unit_term = $defaults->default_dose_unit_term ? $defaults->default_dose_unit_term : $medication->default_dose_unit_term;
            $r->form_id = $defaults->default_form_id ? $defaults->default_form_id : $medication->default_form_id;
        } else {
            $r->frequency_id = null;
            $r->route_id = $medication->default_route_id;
            $r->dose = 1;
            $r->dose_unit_term = $medication->default_dose_unit_term;
            $r->form_id = $medication->default_form_id;
        }

        return $r;
    }
}
