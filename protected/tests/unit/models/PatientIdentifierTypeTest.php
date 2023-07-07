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

use OE\factories\ModelFactory;

/**
 * @group sample-data
 * @group patient-identifier-type
 */
class PatientIdentifierTypeTest extends ModelTestCase
{
    use WithTransactions;
    use WithFaker;
    use \MocksSession;
    use \MakesApplicationRequests;

    protected $element_cls = PatientIdentifierType::class;

    /**
     * @covers PatientIdentifierType
     */
    public function testGetNextValueForIdentifierType_IncrementsCurrentHighestValueForIdentifierType()
    {
        list($patient_identifier_type, $current_highest_identifier) = $this->initialisePatientIdentifierTypeForTesting();

        // subtracting some value from the max value and setting that as the auto increment start value
        $auto_increment_start_value = $current_highest_identifier->value - 4 ;

        $patient_identifier_type_display_order = PatientIdentifierTypeDisplayOrder::factory()
            ->create([
                'patient_identifier_type_id' => $patient_identifier_type->id,
                'auto_increment_start' => $auto_increment_start_value
                ]);

        $patient_identifier_type_next_value = PatientIdentifierType::getNextValueForIdentifierType(
            $patient_identifier_type->id,
            $patient_identifier_type_display_order->auto_increment_start
        );

        // The expected value is the highest value from  after adding one to it.
        $this->assertEquals($current_highest_identifier->value + 1, $patient_identifier_type_next_value);
    }

    /** @test */
    public function ensure_next_highest_value_returns_configured_starting_value_when_current_maximum_is_lower()
    {
        list($patient_identifier_type, $current_highest_identifier) = $this->initialisePatientIdentifierTypeForTesting();

        // subtracting some value from the max value and setting that as the auto increment start value
        $auto_increment_start_value = $current_highest_identifier->value + 4;

        $patient_identifier_type_display_order = PatientIdentifierTypeDisplayOrder::factory()
            ->create([
                'patient_identifier_type_id' => $patient_identifier_type->id,
                'auto_increment_start' => $auto_increment_start_value
                ]);

        $patient_identifier_type_next_value = PatientIdentifierType::getNextValueForIdentifierType(
            $patient_identifier_type->id,
            $patient_identifier_type_display_order->auto_increment_start
        );

        // The expected value is the highest value from  after adding one to it.
        $this->assertEquals($auto_increment_start_value, $patient_identifier_type_next_value);
    }

    /** @test */
    public function ensure_patient_identifier_is_editable_by_method_returns_true_for_an_admin_user()
    {
        list($patient_identifier_type, $institution) = $this->initialisePatientIdentifierTypeDisplayOrderForTesting(0);

        $user = $this->getMockBuilder('CWebUser')->disableOriginalConstructor()->getMock();
        $user->expects($this->any())->method('checkAccess')->with('admin')->will($this->returnValue(true));
        Yii::app()->setComponent('user', $user);

        $response = $patient_identifier_type->isEditableBy($user, $institution);

        // The value returned by isEditableBy method is always true for an admin user
        $this->assertEquals(true, $response);
    }

    /** @test */
    public function ensure_patient_identifier_is_editable_by_method_returns_true_if_only_editable_by_admin_is_false_for_a_non_admin_user()
    {
        list($patient_identifier_type, $institution) = $this->initialisePatientIdentifierTypeDisplayOrderForTesting(0);

        $user = $this->getMockBuilder('CWebUser')->disableOriginalConstructor()->getMock();
        $user->expects($this->any())->method('checkAccess')->with('admin')->will($this->returnValue(false));
        Yii::app()->setComponent('user', $user);

        $response = $patient_identifier_type->isEditableBy($user, $institution);

        // The value returned by isEditableBy method is true for a non-admin user,
        // if the only_editable_by_admin parameter is set to false
        $this->assertEquals(true, $response);
    }

    /** @test */
    public function ensure_patient_identifier_is_editable_by_method_returns_false_if_only_editable_by_admin_is_true_for_a_non_admin_user()
    {
        list($patient_identifier_type, $institution) = $this->initialisePatientIdentifierTypeDisplayOrderForTesting(1);

        $user = $this->getMockBuilder('CWebUser')->disableOriginalConstructor()->getMock();
        $user->expects($this->any())->method('checkAccess')->with('admin')->will($this->returnValue(false));
        Yii::app()->setComponent('user', $user);

        $response = $patient_identifier_type->isEditableBy($user, $institution);

        // The value returned by isEditableBy method is false for a non-admin user,
        // if the only_editable_by_admin parameter is set to true
        $this->assertEquals(false, $response);
    }

    /** @test */
    public function ensure_patient_identifier_is_editable_for_admins()
    {
        list($patient_identifier_type, $institution) = $this->initialisePatientIdentifierTypeDisplayOrderForTesting(0);

        $random_patient_identifier_value = $this->faker->unique()->numerify('######');

        $patient = Patient::factory()
            ->withIdentifierType($patient_identifier_type,
                function () use ($random_patient_identifier_value) {
                    return $random_patient_identifier_value;
                })
            ->create();

        // The admin user should be able to update the patient identifier value
        $this->assertEquals($random_patient_identifier_value, $patient->identifiers[0]->value);
    }

    /** @test */
    public function ensure_patient_identifier_is_not_editable_for_non_admins()
    {
        list($patient_identifier_type, $institution) = $this->initialisePatientIdentifierTypeDisplayOrderForTesting(1);

        $user = \User::factory()->withLocalAuthForInstitution($institution, 'password')->withAuthItems(['User', 'Edit', 'Add patient'])->create();

        $random_patient_identifier_value = $this->faker->unique()->numerify('######');

        $contact = Contact::factory()->make();
        $address = Address::factory()->make(['contact_id' => $contact->id]);

        $patient = Patient::factory()
            ->make(['contact_id' => $contact->id]);

        $form_data = $this->getPatientFormData($patient, $contact, $address, $random_patient_identifier_value, $patient_identifier_type);

        $response = $this->actingAs($user, $institution)
            ->post('/patient/create', $form_data);

        $urlArray = explode('/', $response->redirect->url);
        $id = $urlArray[count($urlArray) -1];
        $patient_response = Patient::model()->findByPk($id);

        // The non-admin user should not be able to update the patient identifier value
        // when the only_editable_by_admin flag is set to true (1)
        $this->assertEmpty($patient_response->identifiers, 'Expected to patient identifiers to be empty');

        $response->assertRedirectContains('summary', 'Expected to redirect to patient summary page');
    }

    /** @test */
    public function ensure_patient_identifier_is_editable_for_non_admins()
    {
        list($patient_identifier_type, $institution) = $this->initialisePatientIdentifierTypeDisplayOrderForTesting(0);

        $user = \User::factory()->withLocalAuthForInstitution($institution, 'password')->withAuthItems(['User', 'Edit', 'Add patient'])->create();

        $random_patient_identifier_value = $this->faker->unique()->numerify('######');

        $contact = Contact::factory()->make();
        $address = Address::factory()->make(['contact_id' => $contact->id]);

        $patient = Patient::factory()
            ->make(['contact_id' => $contact->id]);

        $form_data = $this->getPatientFormData($patient, $contact, $address, $random_patient_identifier_value, $patient_identifier_type);

        $response = $this->actingAs($user, $institution)
            ->post('/patient/create', $form_data);

        $urlArray = explode('/', $response->redirect->url);
        $id = $urlArray[count($urlArray) -1];
        $patient_response = Patient::model()->findByPk($id);

        // The non-admin user should be able to update the patient identifier value
        // when the only_editable_by_admin flag is set to false (0)
        $this->assertNotEmpty($patient_response->identifiers, 'Expected to patient identifiers not to be empty');
        $this->assertEquals($random_patient_identifier_value, $patient_response->identifiers[0]->value);
        $response->assertRedirectContains('summary', 'Expected to redirect to patient summary page');

        $user = User::model()->findByAttributes(['first_name' => 'admin']);
        $this->mockCurrentUser($user);
    }

    private function getPatientFormData($patient, $contact, $address, $patient_identifier_value, $patient_identifier_type) {
        return [
            'Patient' => [
                'dob' => $patient->dob,
                'patient_source' => '0',
                'is_deceased' => '0',
            ],
            'Contact' => [
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'primary_phone' => $contact->primary_phone,
                'email' => $contact->email,
            ],
            'changePatientSource' => '0',
            'Address' => [
                'country_id' => $address->country_id,
            ],
            'PatientIdentifier' => [
                0 => [
                    'value' => $patient_identifier_value,
                    'patient_identifier_type_id' => $patient_identifier_type->id
                ]
            ],
            'autocomplete_gp_id' => '',
            'autocomplete_practice_id' => '',
            'PatientReferral' => [
                'uploadedFile' => '',
            ],
            'autocomplete_user_id' => '',
            'PatientUserReferral' => [
                'user_id' => ''
            ]
        ];
    }

    protected function initialisePatientIdentifierTypeDisplayOrderForTesting(int $only_editable_by_admin): array
    {
        $institution = \Institution::factory()->create();

        $patient_identifier_type = PatientIdentifierType::factory()->local()->create([
            'institution_id' => $institution
        ]);

        $patient_identifier_type_display_order = PatientIdentifierTypeDisplayOrder::factory()
            ->create([
                'institution_id' => $institution,
                'site_id' => null,
                'necessity' => 'optional',
                'patient_identifier_type_id' => $patient_identifier_type->id,
                'auto_increment' => 0,
                'auto_increment_start' => '0',
                'only_editable_by_admin' => $only_editable_by_admin
            ]);

        return [$patient_identifier_type, $institution];
    }

    protected function initialisePatientIdentifierTypeForTesting(): array
    {
        $institution = Institution::factory()->create();
        $patient_identifier_type = PatientIdentifierType::factory()->local()->create([
            'institution_id' => $institution
        ]);

        Patient::factory()
            ->count(5)
            ->withIdentifierType($patient_identifier_type, function () { return $this->faker->unique()->numerify('######'); })
            ->create();

        $current_highest_identifier = PatientIdentifierHelper::getMaxIdentifier($patient_identifier_type->id);

        return [$patient_identifier_type, $current_highest_identifier];
    }
}
