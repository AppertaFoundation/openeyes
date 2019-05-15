<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class PrescriptionCreator extends \EventCreator
{

    public function __construct()
    {
        $prescription_event_type = \EventType::model()->find('name = "Prescription"');
        parent::__construct($prescription_event_type->id);

        $this->elements['Element_OphDrPrescription_Details'] = new \Element_OphDrPrescription_Details();
    }

    public function addDrugSet($drug_set_id)
    {
        $set = \DrugSet::model()->findByPk($drug_set_id);
        $api = Yii::app()->moduleAPI->get('OphTrOperationnote');

        if ($set) {
            foreach ($set->items as $item) {
                $item_model = new OphDrPrescription_Item();
                $item_model->drug_id = $item->drug_id;
                $item_model->loadDefaults();
                $attr = $item->getAttributes();
                unset($attr['drug_set_id']);
                $item_model->attributes = $attr;

                $item_model->tapers = $item->tapers;

                if ($api) {
                    $eye = $api->getLastEye($this->patient, false);
                    if ($eye) {
                        $item_model->route_option_id = $eye;
                    }
                }

                $items[] = $item_model;
            }
        }

        $this->elements['Element_OphDrPrescription_Details']->items = $items;
    }

    protected function saveElements($event_id)
    {
        $error = false;
        foreach ($this->elements as $element) {
            $element->event_id = $event_id;

            if (!$element->save()) {
                $error = true;
                $this->addErrors($element->getErrors());
            } else {
                foreach ($element->items as $item) {
                    $item->prescription_id = $element->id;
                    if (!$item->save()){
                        $error = true;
                    }
                }
            }
        }
        return !$error;
    }
}
