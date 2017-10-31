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

        $key = (integer) $key;

        $drug_set_items = DrugSetItem::model()->findAllByAttributes(array('drug_set_id' => $set_id));
        foreach ($drug_set_items as $drug_set_item) {
            $this->renderPrescriptionItem($key, $drug_set_item);
            ++$key;
        }
    }

    /**
     * Ajax function to get drug set for admin page (we do not have patient_id there), and we also load the
     * name and subspecialty here.
     *
     * @param $key
     * @param $set_id
     *
     * @throws CException
     */
    public function actionSetFormAdmin($key, $set_id)
    {
        $drugset = DrugSet::model()->findByPk($set_id);
        $returnData = array();

        $returnData['drugsetName'] = $drugset->name;
        $returnData['drugsetSubspecialtyId'] = $drugset->subspecialty_id;
        $returnData['tableRows'] = ''; // the HTML content for the prescription items table

        $key = (integer) $key;
        $drug_set_items = DrugSetItem::model()->findAllByAttributes(array('drug_set_id' => $set_id));

        foreach ($drug_set_items as $drug_set_item) {
            $returnData['tableRows'] .= $this->renderPrescriptionItem($key, $drug_set_item);
            ++$key;
        }

        echo json_encode($returnData);
    }

    /**
     * Ajax action to get the form for single drug.
     *
     * @param $key
     * @param $patient_id
     * @param $drug_id
     */
    public function actionItemForm($key, $patient_id, $drug_id)
    {
        $this->initForPatient($patient_id);
        $this->renderPrescriptionItem($key, $drug_id);
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
        $dispense_condition = OphDrPrescription_DispenseCondition::model()->with('locations')->findByPk($condition_id);
        foreach ($dispense_condition->locations as $location)
        {
            echo '<option value="'.$location->id.'">'.$location->name.'</option>';
        }
    }
}
