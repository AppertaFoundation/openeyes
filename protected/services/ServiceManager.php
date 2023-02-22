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

namespace services;

class ServiceManager extends \CApplicationComponent
{
    public $internal_services = array();

    private $service_config = array();
    private $services = array();

    public function init()
    {
        foreach ($this->internal_services as $service_class) {
            $resource_class = $service_class::getResourceClass();

            $this->service_config[$service_class::getServiceName()] = array(
                'service_class' => $service_class,
                'resource_class' => $resource_class,
                'fhir_type' => $resource_class::getFhirType(),
                'fhir_prefix' => $resource_class::getFhirPrefix(),
            );
        }

        parent::init();
    }

    /**
     * @param string $name
     *
     * @return Service
     */
    public function __get($name)
    {
        if (!($service = $this->getService($name))) {
            throw new \Exception("Service '{$name}' not defined");
        }

        return $service;
    }

    /**
     * @param string $name
     *
     * @return Service|null
     */
    public function getService($name)
    {
        if (!array_key_exists($name, $this->services)) {
            if (isset($this->service_config[$name])) {
                $class_name = $this->service_config[$name]['service_class'];
                $service = $class_name::load();
                if (!$service instanceof Service) {
                    throw new \Exception("Invalid service class: '{$class_name}'");
                }
                $this->services[$name] = $service;
            } else {
                $this->services[$name] = null;
            }
        }

        return $this->services[$name];
    }

    /**
     * Find an internal service for the specified FHIR resource type, using profiles to differentiate if necessary.
     *
     * @param string[] $profiles
     *
     * @return InternalService|null
     */
    public function getFhirService($fhir_type, array $profiles)
    {
        $candidates = array_filter($this->service_config, function ($config) use ($fhir_type) { return $config['fhir_type'] == $fhir_type;
        });

        if (!$candidates) {
            throw new NotFound("Unsupported resource type: '{$fhir_type}'");
        }

        // Unambiguous resource type
        if (count($candidates) == 1) {
            foreach ($candidates as $name => $config) {
                if (!isset($config['fhir_prefix']) || !$config['fhir_prefix']) {
                    return $this->{$name};
                }
            }
        }

        if (!$profiles) {
            throw new ProcessingNotSupported("A profile must be specified for resources of type '{$fhir_type}'");
        }

        foreach ($candidates as $name => $config) {
            $resource_class = $config['resource_class'];
            if (in_array($resource_class::getOeFhirProfile(), $profiles)) {
                return $this->{$name};
            }
        }

        // Profile(s) supplied but no match, not necessarily an error
        return;
    }

    /**
     * Convert a FHIR resource type and ID to an internal service reference.
     *
     * @param string $fhir_type
     * @param string $fhir_id
     *
     * @return InternalReference|null Null if no mapping found
     */
    public function fhirIdToReference($fhir_type, $fhir_id)
    {
        if (!preg_match('/^(?:(\w+)-)?(\d+)$/', $fhir_id, $m)) {
            return;
        }
        list(, $prefix, $id) = $m;

        foreach ($this->service_config as $service_name => $config) {
            if ($config['fhir_type'] == $fhir_type && $config['fhir_prefix'] == $prefix) {
                return new InternalReference($service_name, $id);
            }
        }

        return;
    }

    /**
     * Convert an internal service name and ID to a FHIR relative URL.
     *
     * @param string $service_name
     * @param int    $id
     *
     * @return string
     */
    public function serviceAndIdToFhirUrl($service_name, $id)
    {
        if (!isset($this->service_config[$service_name])) {
            throw new \Exception("Unknown service: '{$service_name}'");
        }

        if (!isset($this->service_config[$service_name]['fhir_type'])) {
            throw new \Exception("No FHIR resource type configured for service '{$service_name}'");
        }

        $prefix = $this->service_config[$service_name]['fhir_prefix'] ?
            $this->service_config[$service_name]['fhir_prefix'].'-' : '';

        return "{$this->service_config[$service_name]['fhir_type']}/{$prefix}{$id}";
    }

    /**
     * Convert an internal reference to a FHIR relative URL.
     *
     * @param InternalReference $ref
     *
     * @return string
     */
    public function referenceToFhirUrl(InternalReference $ref)
    {
        return $this->serviceAndIdToFhirUrl($ref->getServiceName(), $ref->getId());
    }

    /**
     * List FHIR 'supported' profiles for the system.
     *
     * http://hl7.org/implement/standards/fhir/conformance-definitions.html#Conformance.profile
     * From our point of view, this means profiles specific to each
     * internal resource type.
     *
     * @return ResourceReference[]
     */
    public function listFhirSupportedProfiles()
    {
        $refs = array();
        foreach ($this->service_config as $name => $config) {
            if (!isset($config['fhir_type']) || !\Yii::app()->fhirMarshal->isStandardType($config['fhir_type'])) {
                continue;
            }

            $class = $config['resource_class'];
            $refs[] = new ExternalReference($class::getOeFhirProfile());
        }

        return $refs;
    }

    /**
     * Describe FHIR REST server resources supported by the system.
     *
     * http://hl7.org/implement/standards/fhir/conformance-definitions.html#Conformance.rest.resource
     *
     * @return array
     */
    public function describeFhirServerResources()
    {
        $types = array();
        foreach ($this->service_config as $name => $config) {
            if (!isset($config['fhir_type']) || !\Yii::app()->fhirMarshal->isStandardType($config['fhir_type'])) {
                continue;
            }

            $class = $config['service_class'];
            $type = $config['fhir_type'];

            if (!isset($types[$type])) {
                $types[$type] = array('ops' => array(), 'sps' => array());
            }

            $types[$type]['ops'] = array_unique(array_merge($types[$type]['ops'], $class::getSupportedOperations()));
            $types[$type]['sps'] += $class::getSupportedSearchParams();
        }

        $resources = array();
        foreach ($types as $type => $data) {
            $res = (object) array(
                'type' => $type,
                'operation' => array(),
                'readHistory' => false,
                'updateCreate' => false,
                'searchParam' => array(),
            );

            if (in_array(InternalService::OP_READ, $data['ops'])) {
                $data['ops'][] = 'vread';
            }
            foreach ($data['ops'] as $op) {
                $res->operation[] = (object) array('code' => $op);
            }

            foreach ($data['sps'] as $sp_name => $sp_type) {
                if ($sp_name == 'id') {
                    $sp_name == '_id';
                }
                $res->searchParam[] = (object) array('name' => $sp_name, 'type' => $sp_type);
            }

            $resources[] = $res;
        }

        return $resources;
    }
}
