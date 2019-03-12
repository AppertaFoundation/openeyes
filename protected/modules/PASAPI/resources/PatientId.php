<?php

namespace OEModule\PASAPI\resources;

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
class PatientId extends BaseResource
{
    protected static $resource_type = 'PatientId';

    public $isNewResource;

    /**
     * Valid tags for defining a patient identifier.
     *
     * @var array
     */
    protected $id_tags = array('Id', 'PasId', 'NHSNumber', 'HospitalNumber');

    /**
     * Abstraction for getting model instance of class.
     *
     * @param $class
     *
     * @return mixed
     */
    protected function getModelForClass($class)
    {
        return $class::model();
    }

    /**
     * As a primary resource (i.e. mapped to external resource) we need to ensure we have an id for tracking
     * the resource in the system.
     *
     * @return bool
     */
    public function validate()
    {
        $has_id = false;
        foreach ($this->id_tags as $attr) {
            if (isset($this->$attr)) {
                $has_id = true;
            }
        }
        if (!$has_id) {
            $this->addError('At least one Id tag of the form '.implode(',', $this->id_tags).' is required.');
        }

        return parent::validate();
    }

    /**
     * Function to resolve the referenced Patient model for this id resource.
     *
     * @return \Patient
     *
     * @throws \Exception
     */
    public function getModel()
    {
        foreach ($this->id_tags as $attr) {
            if (property_exists($this, $attr)) {
                return $this->{'resolveModel'.$attr}();
            }
        }
        // should never reach here assuming the resource has been validated
        throw new \Exception('No appropriate id tag provided.');
    }

    /**
     * Wrapper for patient not found behaviour.
     *
     * @throws \Exception
     */
    protected function patientNotFound()
    {
        throw new \Exception('Patient not found.');
    }

    /**
     * Convenience wrapper for marking a method not implemented (to generalise the exception for this).
     *
     * @param $method_name
     *
     * @throws \Exception
     */
    protected function methodNotImplemented($method_name)
    {
        throw new \Exception("{$method_name} not yet supported.");
    }

    /**
     * Resolve the model by the given Patient Id.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function resolveModelId()
    {
        $model = $this->getModelForClass('Patient');
        $instance = $model->findByPk($this->Id);

        if ($instance) {
            return $instance;
        }

        $this->patientNotFound();
    }

    protected function resolveModelPasId()
    {
        $this->methodNotImplemented('resolvePasId');
    }

    protected function resolveModelNHSNumber()
    {
        $this->methodNotImplemented('resolveModelNHSNumber');
    }

    protected function resolveModelHospitalNumber()
    {
        $this->methodNotImplemented('resolveModelHospitalNumber');
    }
}
