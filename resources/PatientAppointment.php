<?php namespace OEModule\PASAPI\resources;

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Class PatientAppointment
 * @package OEModule\PASAPI\resources
 *
 * @property PatientId $PatientId
 * @property Appointment $Appointment
 */
class PatientAppointment extends BaseResource
{
    static protected $resource_type = 'PatientAppointment';
    /**
     * Class of model that is stored internally for this resource
     *
     * @var string
     */
    static protected $model_class = 'WorklistPatient';

    public $isNewResource;
    public $id;

    /**
     * @var WorklistManager
     */
    protected $worklist_manager;

    /**
     * PatientAppointment constructor.
     * @param $version
     * @param \WorklistManager|null $worklist_manager
     */
    public function __construct($version, \WorklistManager $worklist_manager = null)
    {
        parent::__construct($version);

        if (is_null($worklist_manager))
            $worklist_manager = new \WorklistManager();

        $this->worklist_manager = $worklist_manager;
    }

    /**
     * Abstraction for getting instance of class
     *
     * @param $class
     * @return mixed
     */
    protected function getInstanceForClass($class, $args = array())
    {
        if (empty($args))
            return new $class();

        $cls = new ReflectionClass($class);
        return $cls->newInstanceArgs($args);
    }

    /**
     * Wrapper for starting a transaction
     *
     * @return CDbTransaction|null
     */
    protected function startTransaction()
    {
        return \Yii::app()->db->getCurrentTransaction() === null
            ? \Yii::app()->db->beginTransaction()
            : null;
    }

    /**
     * As a primary resource (i.e. mapped to external resource) we need to ensure we have an id for tracking
     * the resource in the system
     *
     * @return bool
     */
    public function validate() {
        if (!$this->id) {
            $this->addError("Resource ID required");
        }
        return parent::validate();
    }

    public function save() {
        if (!$this->validate())
            return null;

        $transaction = $this->startTransaction();

        try {
            $finder = $this->getInstanceForClass("OEModule\\PASAPI\\models\\PasApiAssignment");
            $assignment = $finder->findByResource(static::$resource_type, $this->id, static::$model_class);
            $model = $assignment->getInternal();
            // track whether we are creating or updating
            $this->isNewResource = $model->isNewRecord;

            if ($model = $this->saveModel($model)) {
                $assignment->internal_id = $model->id;
                $assignment->save();
                $assignment->unlock();

                $this->audit($this->isNewResource ? 'create' : 'update', null, null, null);

                if ($transaction)
                    $transaction->commit();

                return $model->id;
            }
        }
        catch (\Exception $e) {
            if ($transaction)
                $transaction->rollback();

            throw $e;
        }
    }

    /**
     * @return \Patient
     * @throws \Exception
     */
    protected function resolvePatient()
    {
        return $this->PatientId->getModel();
    }

    protected function resolveWhen()
    {
        return $this->Appointment->getWhen();
    }

    protected function resolveAttributes()
    {
        return $this->Appointment->getMappingsArray();
    }

    /**
     * @param \WorklistPatient $model
     * @return bool|\WorklistPatient
     */
    public function saveModel(\WorklistPatient $model)
    {
        // extract the values to be passed to the manager instance for mapping
        $patient = $this->resolvePatient();
        $when = $this->resolveWhen();
        $attributes = $this->resolveAttributes();

        if ($model->isNewRecord) {
            if (!$model = $this->worklist_manager->mapPatientToWorklistDefinition($patient, $when, $attributes)) {
                foreach ($this->worklist_manager->getErrors() as $err) {
                    $this->addError($err);
                }
                throw new \Exception("Could not add patient to worklist");
            }
        }
        else {
            if (!$this->worklist_manager->updateWorklistPatientFromMapping($model, $when, $attributes)) {
                foreach ($this->worklist_manager->getErrors() as $err) {
                    $this->addError($err);
                }
                throw new \Exception("Could not update patient worklist entry");
            };
        }

        return $model;
    }

}