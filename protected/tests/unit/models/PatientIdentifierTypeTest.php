<?php

/**
 * (C) OpenEyes Foundation, 2022
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

/**
 * Class PatientIdentifierTypeTest
 *
 * @method patient_identifier_type($fixtureId)
 * @method patient_identifier_type_display_order($fixtureId)
 */
class PatientIdentifierTypeTest extends ActiveRecordTestCase
{
    public PatientIdentifierType $model;
    public $fixtures = array(
        'patient_identifier_type' => PatientIdentifierType::class,
        'patient' => Patient::class,
        'patient_identifier' => PatientIdentifier::class,
        'patient_identifier_type_display_order' => PatientIdentifierTypeDisplayOrder::class,
    );

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->model = new PatientIdentifierType();
    }

    /**
     * @covers PatientIdentifierType
     *
     */
    public function testModel()
    {
        $this->assertEquals('PatientIdentifierType', get_class(PatientIdentifierType::model()), 'Class name should match model.');
    }

    /**
     * @covers PatientIdentifierType
     */
    public function testGetNextValueForIdentifierType_IncrementsCurrentHighestValueForIdentifierType()
    {
        $patient_identifier_type_display_order = $this->patient_identifier_type_display_order('patient_identifier_type_display_order_1');
        $value = $this->patient_identifier_type('ID')->getNextValueForIdentifierType($patient_identifier_type_display_order->patient_identifier_type_id, $patient_identifier_type_display_order->auto_increment_start);
        // The expected value is the highest value from the patient_identifier fixture after adding one to it.
        $this->assertEquals(5550103, $value);
    }
}
