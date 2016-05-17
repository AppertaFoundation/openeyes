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

class PatientAppointment extends BaseResource
{
    static protected $resource_type = 'PatientAppointment';

    public $isNewResource;
    public $id;

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
        return Yii::app()->db->getCurrentTransaction() === null
            ? Yii::app()->db->beginTransaction()
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
            $assignment = $finder->findByResource(static::$resource_type, $this->id);
            $model = $assignment->getInternal();

            // track whether we are creating or updating
            $this->isNewResource = $model->isNewRecord;

            if ($this->saveModel($model)) {
                $assignment->internal_id = $model->id;
                $assignment->save();
                $assignment->unlock();

                $this->audit($this->isNewResource ? 'create' : 'update', null, null, array('patient_id' => $model->id));

                if ($transaction)
                    $transaction->commit();
            }
            return $model->id;
        }
        catch (\Exception $e) {
            if ($transaction)
                $transaction->rollback();

            throw $e;
        }
    }

    public function saveModel(WorklistPatient $model)
    {
        $manager = new \WorklistManager();

        // Not yet implemented
        return false;
        // extract the values to be passed to the manager instance for mapping

        if ($model->isNewRecord) {
            $manager->mapPatientToWorklistDefinition($patient, $when, $attributes);
        }
        else {
            // we should verify that the patient id still points to the same patient.
            $manager->updateWorklistPatientFromMapping($model, $when, $attributes);
        }
    }

}