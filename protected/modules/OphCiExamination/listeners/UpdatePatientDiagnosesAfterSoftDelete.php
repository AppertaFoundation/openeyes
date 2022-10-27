<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\listeners;

use OE\concerns\InteractsWithApp;
use OELog;
use OEModule\OESysEvent\events\EventTypeEventSoftDeleted;
use OEModule\OphCiExamination\components\OphCiExamination_API;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses;
use SecondaryDiagnosis;

class UpdatePatientDiagnosesAfterSoftDelete
{
    use InteractsWithApp;

    protected \Event $event;
    protected ?\DataContext $data_context = null;
    protected ?OphCiExamination_API $module_api = null;


    public function __invoke(EventTypeEventSoftDeleted $system_event)
    {
        $this->event = $system_event->event;
        $diagnoses_element = $this->event->getElementByClass(Element_OphCiExamination_Diagnoses::class);

        if (!$diagnoses_element) {
            // event removal has no effect on patient diagnoses
            return;
        }

        $transaction = $this->getApp()->db->beginInternalTransaction();
        $this->updatePatientOphthalmicDiagnoses();
        // TODO: systemic diagnoses
        $transaction->commit();
    }

    protected function getPatient(): \Patient
    {
        return $this->event->episode->patient;
    }

    protected function getDataContext()
    {
        if (!$this->data_context) {
            $this->data_context = new \DataContext($this->getApp(), ['subspecialties' => $this->event->episode->getSubspecialty()]);
        }

        return $this->data_context;
    }

    /**
     * Returns a moduleApi instance that has the DataContext set to the subspecialty of the
     * EventTypeEvent that is attached to this class
     *
     * @return OphCiExamination_API
     */
    protected function getModuleApiForEventSubspecialty(): OphCiExamination_API
    {
        if (!$this->module_api) {
            $this->module_api = $this->getApp()->moduleAPI->get('OphCiExamination', $this->getDataContext());
        }

        return $this->module_api;
    }

    protected function getTipElementForEventContext(string $class_name): ?\BaseEventTypeElement
    {
        return $this->getModuleApiForEventSubspecialty()
            ->getLatestElement($class_name, $this->getPatient(), true);
    }

    protected function updatePatientOphthalmicDiagnoses()
    {
        $tip_element = Element_OphCiExamination_Diagnoses::model()->getTipElement($this->getPatient());

        if (!$tip_element || $tip_element->event->event_date < $this->event->event_date) {
            $this->setPatientSecondaryDiagnosesFrom($tip_element);
        }

        $this->setPatientPrincipalDiagnosisFrom($this->getTipElementForEventContext(Element_OphCiExamination_Diagnoses::class));
    }

    protected function setPatientSecondaryDiagnosesFrom(?Element_OphCiExamination_Diagnoses $element = null)
    {
        $initial_secondary_disorder_ids = array_map(
            function ($secondary_diagnosis) {
                return $secondary_diagnosis->disorder_id;
            },
            $this->getPatient()->getOphthalmicDiagnoses()
        );
        $retained_disorder_ids = $this->setPatientSecondaryDiagnosesAndGetDisorderIds($element->diagnoses ?? []);

        $this->removePatientSecondaryDiagnoses(array_diff($initial_secondary_disorder_ids, $retained_disorder_ids));
    }

    protected function setPatientPrincipalDiagnosisFrom(?Element_OphCiExamination_Diagnoses $element): void
    {
        $principal_diagnosis = array_filter(
            $element->diagnoses ?? [],
            function ($diagnosis) {
                return (bool) $diagnosis->principal;
            }
        );

        if (count($principal_diagnosis)) {
            $this->event->episode->disorder_id = $principal_diagnosis[0]->disorder_id;
            $this->event->episode->eye_id = $principal_diagnosis[0]->eye_id;
            $this->event->episode->disorder_date = $principal_diagnosis[0]->date;
        } else {
            $this->event->episode->disorder_id = null;
            $this->event->episode->eye_id = null;
            $this->event->episode->disorder_date = null;
        }

        $this->event->episode->save();
    }

    private function setPatientSecondaryDiagnosesAndGetDisorderIds($diagnoses_elements = []): array
    {
        return array_filter(
            array_map(
                function ($diagnosis_element) {
                    if ($diagnosis_element->principal) {
                        return null;
                    }
                    $this->getPatient()
                        ->addDiagnosis(
                            $diagnosis_element->disorder_id,
                            $diagnosis_element->eye_id,
                            $diagnosis_element->date
                        );
                    return $diagnosis_element->disorder_id;
                },
                $diagnoses_elements
            )
        );
    }

    private function removePatientSecondaryDiagnoses($disorder_ids = [])
    {
        $criteria = new \CDbCriteria();
        $criteria->addInCondition('disorder_id', $disorder_ids);
        $criteria->addColumnCondition(['patient_id' => $this->getPatient()->id]);

        $secondary_diagnoses = SecondaryDiagnosis::model()->findAll($criteria);

        foreach ($secondary_diagnoses as $secondary_diagnosis) {
            $this->getPatient()->removeDiagnosis($secondary_diagnosis->id);
        }
    }
}
