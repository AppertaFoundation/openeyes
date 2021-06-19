<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2015
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2015, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class PrescriptionCommonController extends DefaultController
{
    protected static $action_types = array(
        'setForm' => self::ACTION_TYPE_FORM,
        'setFormAdmin' => self::ACTION_TYPE_FORM,
        'itemForm' => self::ACTION_TYPE_FORM,
        'itemFormAdmin' => self::ACTION_TYPE_FORM,
        'saveDrugSetAdmin' => self::ACTION_TYPE_FORM,
        'getDispenseLocation' => self::ACTION_TYPE_FORM,
        'getSetDrugs' => self::ACTION_TYPE_FORM,
    );

    /**
     * Ajax action to get prescription forms for a drug set.
     *
     * @param $key
     * @param $patient_id
     * @param $set_id
     */
    public function actionSetForm($key, $patient_id, $set_id)
    {
        $this->initForPatient($patient_id);

        $key = (integer)$key;

        $items = MedicationSet::model()->findByPk($set_id)->items;
        if ($items) {
            foreach ($items as $item) {
                $this->renderPrescriptionItem($key, $item);
                ++$key;
            }
        }
    }

    public function actionGetSetDrugs($set_id)
    {
        $drug_set_items = MedicationSetItem::model()->findAllByAttributes(array('medication_set_id' => $set_id));
        $drugs = [];
        /** @var MedicationSetItem[] $drug_set_items */
        foreach ($drug_set_items as $drug_set_item) {
            $drug = $drug_set_item->medication;
            $drugs[] = [
                'label' => $drug->getLabel(),
                'allergies' => array_map(function ($allergy) {
                    return $allergy->id;
                }, $drug->allergies),
            ];
        }
        $this->renderJSON($drugs);
    }

    /**
     * Ajax action to get the form for single drug.
     *
     * @param $key
     * @param $patient_id
     * @param $drug_id
     */
    public function actionItemForm($key, $patient_id, $drug_id, $label = null)
    {
        $this->initForPatient($patient_id);
        $this->renderPrescriptionItem($key, $drug_id, $label);
    }

    /**
     * Ajax action to get the form for single drug on admin page (we don't have patient_id there).
     *
     * @param $key
     * @param $patient_id
     * @param $drug_id
     */
    public function actionItemFormAdmin($key, $drug_id)
    {
        echo $this->renderPrescriptionItem($key, $drug_id);
    }

    public function actionGetDispenseLocation($condition_id)
    {
        if ($condition_id) {
            $institution_id = Yii::app()->session['selected_institution_id'];
            $criteria = new CDbCriteria();
            $criteria->with = array('dispense_location_institutions', 'dispense_location_institutions.dispense_location');
            $criteria->compare('t.dispense_condition_id', $condition_id);
            $criteria->compare('t.institution_id', $institution_id);
            $criteria->compare('dispense_location_institutions.institution_id', $institution_id);
            $dispense_condition = OphDrPrescription_DispenseCondition_Institution::model()->find($criteria);
            foreach ($dispense_condition->dispense_location_institutions as $location_institution) {
                echo '<option value="' . $location_institution->dispense_location->id . '">' . $location_institution->dispense_location->name . '</option>';
            }
        }
    }
}
