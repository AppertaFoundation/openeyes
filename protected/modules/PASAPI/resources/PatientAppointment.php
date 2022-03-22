<?php

namespace OEModule\PASAPI\resources;

use Pathway;

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class PatientAppointment.
 *
 * @property PatientId $PatientId
 * @property Appointment $Appointment
 */
class PatientAppointment extends BaseResource
{
    public static $resource_type = 'PatientAppointment';
    /**
     * Class of model that is stored internally for this resource.
     *
     * @var string
     */
    protected static $model_class = 'WorklistPatient';

    public $isNewResource;
    public $id;

    /**
     * @var \WorklistManager
     */
    protected $worklist_manager;

    /**
     * PatientAppointment constructor.
     *
     * @param $version
     * @param array $options
     */
    public function __construct($version, $options = array())
    {
        if (!isset($options['worklist_manager'])) {
            $options['worklist_manager'] = new \WorklistManager();
        }

        parent::__construct($version, $options);
    }

    /**
     * @return bool
     */
    public function shouldValidateRequired()
    {
        return $this->isNewResource || !$this->partial_record;
    }

    /**
     * As a primary resource (i.e. mapped to external resource) we need to ensure we have an id for tracking
     * the resource in the system.
     *
     * @return bool
     */
    public function validate()
    {
        if (!$this->id) {
            $this->addError('Resource ID required');
        }

        try {
            $this->resolvePatient();
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }

        return parent::validate();
    }

    public function save()
    {
        $assignment = $this->getAssignment();
        /** @var \WorklistPatient $model */
        $model = $assignment->getInternal(true);
        // track whether we are creating or updating
        $this->isNewResource = $model->isNewRecord;

        if ($this->isNewResource && $this->partial_record) {
            $this->addError('Cannot perform partial update on a new record');
            return false;
        }

        if (!$this->validate()) {
            return false;
        }

        $transaction = $this->startTransaction();

        try {
            if ($model = $this->saveModel($model)) {
                $assignment->internal_id = $model->id;
                $assignment->save();
                $assignment->unlock();

                $this->audit($this->isNewResource ? 'create' : 'update');

                if ($transaction) {
                    $transaction->commit();
                }

                return $model->id;
            }
            else {
                return false;
            }
        } catch (\Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }
            throw $e;
        }
    }

    public function delete()
    {
        if (!$this->assignment) {
            $this->addError('Resource ID required');

            return false;
        }

        $transaction = $this->startTransaction();
        try {
            $model = $this->assignment->getInternal();
            if (!$model) {
                // reference exists, but internal model could not be found
                // TODO: decide if the assignment model should be cleared out given that the internal reference
                // doesn't exist.
                throw new \Exception('Could not find internal model to delete.');
            }

            if ($model->isNewRecord) {
                throw new \Exception('No appointment reference found for this id');
            }

            // set the event.worklist_patient_id to null before doing the delete
            \Event::model()->updateAll(['worklist_patient_id' => null], 'worklist_patient_id = :wp', [':wp' => $model->id]);

            // Delete all pathways joined to the worklist
            $pathways = \Pathway::model()->findAll('worklist_patient_id = ?', array($model->id));
            foreach ($pathways as $pathway) {
                // Delete all pathwayStep joined to the Pathway
                \PathwayStep::model()->deleteAll('pathway_id = ?', array($pathway->id));

                // Delete pathway by id
                \Pathway::model()->deleteByPk($pathway->id);
            }


            if (!$model->delete()) {
                $this->addModelErrors($model->getErrors());
                throw new \Exception('Could not delete internal model.');
            }

            if (!$this->assignment->delete()) {
                $this->addModelErrors($this->assignment->getErrors());
                throw new \Exception('Could not delete external reference.');
            }

            if ($transaction) {
                $transaction->commit();
            }

            return true;
        } catch (\Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }

            throw $e;
        }
    }

    /**
     * @return \Patient
     *
     * @throws \Exception
     */
    protected function resolvePatient()
    {
        if (!isset($this->_patient)) {
            $this->_patient = property_exists($this, 'PatientId') ? $this->PatientId->getModel() : null;
        }

        return $this->_patient;
    }

    protected function resolveWhen($default_when)
    {
        if ($this->Appointment) {
            $this->Appointment->setDefaultWhen($default_when);
        }

        return $this->Appointment ? $this->Appointment->getWhen() : null;
    }

    protected function resolveAttributes()
    {
        return $this->Appointment ? $this->Appointment->getMappingsArray() : null;
    }

    /**
     * @param \WorklistPatient $wp
     *
     * @return \Patient
     */
    protected function mapPatient(\WorklistPatient $wp)
    {
        $patient = $this->resolvePatient();
        if (!$patient && $this->partial_record) {
            $patient = $wp->patient;
        }

        return $patient;
    }

    protected function mapWhen(\WorklistPatient $wp)
    {
        $default_when = $wp->when ? \DateTime::createFromFormat('Y-m-d H:i:s', $wp->when) : null;

        return $this->resolveWhen($default_when);
    }

    protected function mapAttributes(\WorklistPatient $wp)
    {
        $attributes = $this->resolveAttributes();
        if ($this->partial_record) {
            // get current values for attributes that have not been passed in as part of
            // this partial record so we are applying the full set of attributes in later
            // worklist resolution.
            foreach ($wp->worklist_attributes as $attr) {
                if (!$attr->worklistattribute) {
                    throw new \Exception("Data consistency issue with worklist attribute {$attr->id}");
                }
                if (!array_key_exists($attr->worklistattribute->name, $attributes)) {
                    $attributes[$attr->worklistattribute->name] = $attr->attribute_value;
                }
            }
        }

        return $attributes;
    }

    /**
     * @param \WorklistPatient $model
     *
     * @return bool|\WorklistPatient
     */
    public function saveModel(\WorklistPatient $model)
    {
        // extract the values to be passed to the manager instance for mapping
        $patient = $this->mapPatient($model);
        $when = $this->mapWhen($model);
        $attributes = $this->mapAttributes($model);

        // allow the suppression of errors for appointments received prior to the ignore date
        if ($warning_limit = $this->worklist_manager->getWorklistIgnoreDate()) {
            if ($when < $warning_limit) {
                $this->warn_errors = true;
            }
        }

        if ($model->isNewRecord) {
            if (!$model = $this->worklist_manager->mapPatientToWorklistDefinition($patient, $when, $attributes)) {
                foreach ($this->worklist_manager->getErrors() as $err) {
                    $this->addError($err);
                }
                if ($this->warn_errors) {
                    return false;
                }

                throw new \Exception('Could not add patient to worklist');
            }
        } else {
            $model->patient_id = $patient->id;
            if (!$this->worklist_manager->updateWorklistPatientFromMapping($model, $when, $attributes, !$this->partial_record)) {
                foreach ($this->worklist_manager->getErrors() as $err) {
                    $this->addError($err);
                }
                if ($this->warn_errors) {
                    return false;
                }

                throw new \Exception('Could not update patient worklist entry');
            };
        }

        return $model;
    }

    public function setPatientIdentifierType(\PatientIdentifierType $patientIdentifierType) {
        if(isset($this->worklist_manager)) {
            $this->worklist_manager->patient_identifier_type = $patientIdentifierType;
        }
    }
}
