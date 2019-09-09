<?php

/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class MedicationController extends BaseController
{
    public function accessRules()
    {
        return array(
            array('allow', 'roles' => array('OprnEditMedication')),
        );
    }

    /**
     * @param int $patientId
     * @param int $medicationId
     */
    public function actionForm($patientId, $medicationId = null, $prescriptionItemId = null)
    {
        if ($medicationId == 'adherence') {
            $this->renderPartial(
                'adherence_form',
                array(
                    'patient' => $this->fetchModel('Patient', $patientId),
                ),
                false, true
            );
        } else {
            if ($medicationId) {
                $medication = $this->fetchModel('ArchiveMedication', $medicationId, true);
            } elseif ($prescriptionItemId) {
                if ($api = Yii::app()->moduleAPI->get('OphDrPrescription')) {
                    $medication = $api->getMedicationForPrescriptionItem($patientId, $prescriptionItemId);
                    if (!$medication) {
                        throw new CHttpException(404, 'Could not get medication for prescription item.');
                    }
                } else {
                    throw new CHttpException(400, 'Missing prescription item or module');
                }
            } else {
                $medication = new ArchiveMedication();
            }

            $this->renderPartial(
                'form',
                array(
                    'patient' => $this->fetchModel('Patient', $patientId),
                    'ArchiveMedication' => $medication,
                    'firm' => Firm::model()->findByPk($this->selectedFirmId),
                ),
                false, true
            );
        }
    }

    /**
     * Searches across MedicationDrug and Drug models for the given term. If the term only matches
     * on an alias, the alias will be included in the returned label for that entry.
     *
     * Distinguishes between the data types to ensure relationship defined correctly.
     */
    public function actionFindDrug()
    {
        $return = array();

        if (isset($_GET['term']) && $term = strtolower($_GET['term'])) {
            $criteria = new CDbCriteria();
            $criteria->compare('LOWER(t.name)', $term, true, 'OR');
            $criteria->compare('LOWER(t.aliases)', $term, true, 'OR');

            foreach (MedicationDrug::model()->with('tags')->findAll($criteria) as $md) {
                $label = $md->name;
                if (strpos(strtolower($md->name), $term) === false) {
                    $label .= ' ('.$md->aliases.')';
                }
                $return[] = array(
                    'name' => $md->name,
                    'label' => $label,
                    'value' => $label,
                    'id' => $md->id,
                    'type' => 'md',
                    'tags' => array_map(function($t) { return $t->id;
                    }, $md->tags)
                );
            }

            foreach (Drug::model()->with('tags')->active()->findAll($criteria) as $drug) {
                $label = $drug->tallmanlabel;
                if (strpos(strtolower($drug->name), $term) === false) {
                    $label .= ' ('.$drug->aliases.')';
                }
                $return[] = array(
                    'name' => $drug->tallmanlabel,
                    'label' => $label,
                    'value' => $label,
                    'type' => 'd',
                    'id' => $drug->id,
                    'tags' => array_map(function($t) { return $t->id;
                    }, $drug->tags)
                );
            }
        }

        echo json_encode($return);
    }

    public function actionDrugDefaults($drug_id)
    {
        if (strpos($drug_id, '@@M') === false) {
            echo json_encode($this->fetchModel('Drug', $drug_id)->getDefaults());
        }
    }

    public function actionDrugRouteOptions($route_id)
    {
        $this->renderPartial(
            'route_option',
            array(
                'ArchiveMedication' => new ArchiveMedication(),
                'route' => $this->fetchModel('DrugRoute', $route_id),
            )
        );
    }

    public function actionRetrieveDrugRouteOptions($route_id)
    {
        $route = MedicationRoute::model()->findByPk($route_id);
        if($route->has_laterality) {
            echo json_encode([
                ['id' =>1, 'name' => 'Left'],
                ['id' =>2, 'name' => 'Right'],
                ['id' =>3, 'name' => 'Both'],
            ]);
        }
        else {
            echo json_encode([]);
        }
    }

    public function actionSave()
    {
        if (@$_POST['MedicationAdherence']) {
            $patient = $this->fetchModel('Patient', @$_POST['patient_id']);

            $medication_adherence = MedicationAdherence::model()->find('patient_id=:patient_id',
                array(':patient_id' => $patient->id));
            if (!$medication_adherence) {
                $medication_adherence = new MedicationAdherence();
                $medication_adherence->patient_id = $patient->id;
            }
            $medication_adherence->medication_adherence_level_id = $_POST['MedicationAdherence']['level'];
            $medication_adherence->comments = $_POST['MedicationAdherence']['comments'];

            if ($medication_adherence->save()) {
                $this->renderPartial('lists', array('patient' => $patient));
            } else {
                header('HTTP/1.1 422');
                echo json_encode($medication_adherence->errors);
            }
        } else {
            $patient = $this->fetchModel('Patient', @$_POST['patient_id']);
            $medication = $this->fetchModel('ArchiveMedication', @$_POST['medication_id'], true);

            $medication->patient_id = $patient->id;

            if (!@$_POST['dose']) {
                $_POST['dose'] = null;
            }
            if (!@$_POST['end_date']) {
                $_POST['end_date'] = null;
            }

            $post_data = $_POST;

            if (strpos($post_data['drug_id'], '@@M') !== false) {
                $post_data['drug_id'] = null;
                $medication_data = explode('@@M', $_POST['drug_id']);
                $post_data['medication_drug_id'] = $medication_data[0];
            }

            $medication->attributes = $post_data;

            if ($medication->save()) {
                $this->renderPartial('lists', array('patient' => $patient));
            } else {
                header('HTTP/1.1 422');
                echo json_encode($medication->errors);
            }
        }
    }

    public function actionStop()
    {
        $patient = $this->fetchModel('Patient', @$_POST['patient_id']);
        $medication = $this->fetchModel('ArchiveMedication', @$_POST['medication_id']);

        if ($patient->id != $medication->patient_id) {
            throw new Exception('Patient ID mismatch');
        }

        $medication->end_date = @$_POST['end_date'];
        $medication->stop_reason_id = @$_POST['stop_reason_id'] ?: null;
        $medication->save();

        $this->renderPartial('lists', array('patient' => $patient));
    }

    public function actionDelete()
    {
        $patient = $this->fetchModel('Patient', @$_POST['patient_id']);
        $medication = $this->fetchModel('ArchiveMedication', @$_POST['medication_id']);

        if ($patient->id != $medication->patient_id) {
            throw new Exception('Patient ID mismatch');
        }

        $medication->delete();

        $this->renderPartial('lists', array('patient' => $patient));
    }
}
