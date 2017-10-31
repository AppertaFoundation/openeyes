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

class Patient extends Resource
{
    public static function fromFhirValues(array $values)
    {
        if (@$values['gender'] == 'UN') {
            $values['gender'] = 'O';
        }

        foreach ((array) @$values['care_providers'] as $ref) {
            switch ($ref->getServiceName()) {
                case 'Gp':
                    $values['gp_ref'] = $ref;
                    break;
                case 'Practice':
                    $values['prac_ref'] = $ref;
                    break;
                case 'CommissioningBody':
                    $values['cb_refs'][] = $ref;
                    break;
            }
        }
        unset($values['care_providers']);

        return parent::fromFhirValues($values);
    }

    public static function getServiceClass($fhirType)
    {
        if ($fhirType == 'Address') {
            return 'services\PatientAddress';
        }

        return parent::getServiceClass($fhirType);
    }

    protected static function getFhirTemplate()
    {
        return \DataTemplate::fromJsonFile(
            __DIR__.'/fhir_templates/Patient.json',
            array(
                'system_uri_nhs_num' => \Yii::app()->params['fhir_system_uris']['nhs_num'],
                'system_uri_hos_num' => \Yii::app()->params['fhir_system_uris']['hos_num'],
            )
        );
    }

    public $nhs_num;
    public $hos_num;

    public $title;
    public $family_name;
    public $given_name;

    public $gender;

    public $birth_date;
    public $date_of_death;

    public $primary_phone;
    public $addresses = array();

    public $gp_ref = null;
    public $prac_ref = null;
    public $cb_refs = array();

    /**
     * @return Gp|null
     */
    public function getGp()
    {
        return $this->gp_ref ? $this->gp_ref->resolve() : null;
    }

    /**
     * @return Practice|null
     */
    public function getPractice()
    {
        return $this->prac_ref ? $this->prac_ref->resolve() : null;
    }

    /**
     * @return CommissioningBody[]
     */
    public function getCommissioningBodies()
    {
        $cbs = array();
        foreach ($this->cb_refs as $cb_ref) {
            $cbs[] = $cb_ref->resolve();
        }

        return $cbs;
    }

    public function toFhirValues()
    {
        $values = parent::toFhirValues();

        if (!in_array($values['gender'], array(null, 'F', 'M'))) {
            $values['gender'] = 'UN';
        }

        $values['care_providers'] = array_filter(array_merge(array($values['gp_ref'], $values['prac_ref']), $values['cb_refs']));

        return $values;
    }
}
