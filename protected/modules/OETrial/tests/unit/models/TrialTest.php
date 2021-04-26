<?php

/**
 * Class TrialTest
 *
 * @method trial($fixtureId)
 * @method trial_patient_status($fixtureId)
 * @method user($fixtureId)
 * @method patient($fixtureId)
 * @method treatment_type($fixtureId)
 * @method trial_patient($fixtureId)
 * @method trial_permission($fixtureId)
 * @method user_trial_assignment($fixtureId)
 */
class TrialTest extends ActiveRecordTestCase
{
    public $fixtures = array(
        'user' => 'User',
        'trial_type' => 'TrialType',
        'trial' => 'Trial',
        'patient' => 'Patient',
        'treatment_type' => 'TreatmentType',
        'trial_patient_status' => 'TrialPatientStatus',
        'trial_patient' => 'TrialPatient',
        'trial_permission' => 'TrialPermission',
        'user_trial_assignment' => 'UserTrialAssignment',
    );

    public function getModel()
    {
        return Trial::model();
    }

    public static function setupBeforeClass() : void
    {
        Yii::app()->getModule('OETrial');
    }

    /**
     * @throws Exception
     */
    public function testTitle()
    {
        $trial = new Trial();
        $trial->name = null;
        $this->assertFalse($trial->save(), 'A Trial cannot be saved with a null name');
    }

    public function testCreatedDate()
    {
        $trial = new Trial();
        $trial->started_date = date('Y-m-d', strtotime('2012-12-21'));
        $this->assertEquals('21 Dec 2012', $trial->getStartedDateForDisplay());

        $trial->started_date = date('Y-m-d', strtotime('1972-1-1'));
        $this->assertEquals('1 Jan 1972', $trial->getStartedDateForDisplay());

        $trial->started_date = null;
        $this->assertEquals('Pending', $trial->getStartedDateForDisplay());
    }

    public function testClosedDate()
    {
        $trial = new Trial();
        $trial->started_date = date('Y-m-d', strtotime('1970-01-01'));
        $trial->closed_date = date('Y-m-d', strtotime('2012-12-21'));
        $this->assertEquals('21 Dec 2012', $trial->getClosedDateForDisplay());

        $trial->closed_date = date('Y-m-d', strtotime('1972-1-1'));
        $this->assertEquals('1 Jan 1972', $trial->getClosedDateForDisplay());

        $trial->started_date = null;
        $trial->closed_date = null;
        $this->assertNull($trial->getClosedDateForDisplay());

        $trial->started_date = date('Y-m-d', strtotime('1972-01-01'));
        $trial->closed_date = null;
        $this->assertEquals('present', $trial->getClosedDateForDisplay());
    }

    public function testDataProvidersExist()
    {
        $providers = $this->trial('trial1')->getPatientDataProviders(null, null);
        $this->assertArrayHasKey('ACCEPTED', $providers);

        $this->assertGreaterThan(0, count($providers), 'There should be at least one data provider returned');

        foreach (TrialPatientStatus::model()->findAll() as $id => $status) {
            $this->assertArrayHasKey(
                $status->code,
                $providers,
                'A data provider of each patient status should be returned'
            );
        }
    }

    public function testDataProviderContent()
    {
        $providers = $this->trial('trial1')->getPatientDataProviders(null, null);

        /* @var CActiveDataProvider $shortlistedPatientProvider */
        $shortlistedPatientProvider = $providers['SHORTLISTED'];
        $data = $shortlistedPatientProvider->getData();
        $this->assertCount(2, $data, 'Trial1 should have exactly 2 shortlisted patients');
    }

    public function testNoPatientsInDataProvider()
    {
        $providers = $this->trial('trial2')->getPatientDataProviders(null, null);

        /* @var CActiveDataProvider $shortlistedPatientProvider */
        $shortlistedPatientProvider = $providers['SHORTLISTED'];
        $data = $shortlistedPatientProvider->getData();
        $this->assertCount(0, $data, 'Trial2 should have no shortlisted patients');
    }

    public function testDataProviderNameOrdering()
    {
        $shortlisted = $this->trial_patient_status('trial_patient_status_shortlisted');
        $shortlistedPatientProvider = $this->trial('trial1')
            ->getPatientDataProvider($shortlisted, 'name', 'asc');
        $data = $shortlistedPatientProvider->getData();
        $this->assertCount(2, $data, 'There should be two patients in trial1');

        $this->assertLessThan(
            $data[1]->patient->last_name,
            $data[0]->patient->last_name,
            'The list of patients should be sorted alphabetically by last name'
        );
    }

    public function testDataProviderNameOrderingDesc()
    {
        $shortlisted = $this->trial_patient_status('trial_patient_status_shortlisted');
        $shortlistedPatientProvider = $this->trial('trial1')
            ->getPatientDataProvider($shortlisted, 'name', 'desc');
        $data = $shortlistedPatientProvider->getData();
        $this->assertCount(2, $data, 'There should be two patients in trial1');

        $this->assertGreaterThan(
            $data[1]->patient->last_name,
            $data[0]->patient->last_name,
            'The list of patients should be sorted alphabetically descending by last name'
        );
    }

    public function testDataProviderAgeOrdering()
    {
        $shortlisted = $this->trial_patient_status('trial_patient_status_shortlisted');
        $shortlistedPatientProvider = $this->trial('trial1')
            ->getPatientDataProvider($shortlisted, 'age', 'asc');
        $data = $shortlistedPatientProvider->getData();
        $this->assertCount(2, $data, 'There should be two patients in trial1');

        $this->assertGreaterThan(
            $data[1]->patient->getAge(),
            $data[0]->patient->getAge(),
            'The list of patients should be sorted by age ascending'
        );
    }

    public function testDataProviderAgeOrderingDesc()
    {
        $shortlisted = $this->trial_patient_status('trial_patient_status_shortlisted');
        $shortlistedPatientProvider = $this->trial('trial1')
            ->getPatientDataProvider($shortlisted, 'age', 'desc');
        $data = $shortlistedPatientProvider->getData();
        $this->assertCount(2, $data, 'There should be two patients in trial1');

        $this->assertLessThan(
            $data[1]->patient->getAge(),
            $data[0]->patient->getAge(),
            'The list of patients should be sorted by age descending'
        );
    }

    public function testDataProviderExternalRefOrdering()
    {
        $shortlisted = $this->trial_patient_status('trial_patient_status_shortlisted');
        $shortlistedPatientProvider = $this->trial('trial1')
            ->getPatientDataProvider($shortlisted, 'external_reference', 'asc');
        $data = $shortlistedPatientProvider->getData();
        $this->assertCount(2, $data, 'There should be two patients in trial1');

        $this->assertGreaterThan(
            $data[1]->external_trial_identifier,
            $data[0]->external_trial_identifier,
            'The list of patients should be sorted by external id ascending'
        );
    }

    public function testDataProviderExternalRefOrderingDesc()
    {
        $shortlisted = $this->trial_patient_status('trial_patient_status_shortlisted');
        $shortlistedPatientProvider = $this->trial('trial1')
            ->getPatientDataProvider($shortlisted, 'external_reference', 'desc');
        $data = $shortlistedPatientProvider->getData();
        $this->assertCount(2, $data, 'There should be two patients in trial1');

        $this->assertLessThan(
            $data[1]->external_trial_identifier,
            $data[0]->external_trial_identifier,
            'The list of patients should be sorted by external id descending'
        );
    }

    public function testHasShortlistedPatients()
    {
        $this->assertTrue(
            $this->trial('trial1')->hasShortlistedPatients(),
            'Trial1 should have at least one shortlisted patient'
        );
        $this->assertFalse(
            $this->trial('trial2')->hasShortlistedPatients(),
            'Trial2 should have no shortlisted patients'
        );
    }

    public function testGetTrialAccess()
    {
        /* @var Trial $trial */
        $trial = $this->trial('trial1');
        $this->assertTrue((bool)$trial->getUserPermission($this->user('user1')->id)->can_manage);
        $this->assertTrue((bool)$trial->getUserPermission($this->user('user1')->id)->can_edit);
        $this->assertTrue((bool)$trial->getUserPermission($this->user('user1')->id)->can_view);

        $this->assertFalse((bool)$trial->getUserPermission($this->user('user2')->id)->can_manage);
        $this->assertFalse((bool)$trial->getUserPermission($this->user('user2')->id)->can_edit);
        $this->assertTrue((bool)$trial->getUserPermission($this->user('user2')->id)->can_view);

        $this->assertFalse((bool)$trial->getUserPermission($this->user('user3')->id)->can_manage);
        $this->assertTrue((bool)$trial->getUserPermission($this->user('user3')->id)->can_edit);
        $this->assertTrue((bool)$trial->getUserPermission($this->user('user3')->id)->can_view);
    }

    /**
     * @throws Exception
     */
    public function testAddPatient()
    {
        /* @var Trial $trial */
        $trial = $this->trial('trial1');
        $patient = $this->patient('patient2');
        $shortlisted = $this->trial_patient_status('trial_patient_status_shortlisted');
        $trialPatient = $trial->addPatient($patient, $shortlisted);

        $this->assertNotNull($trialPatient, 'The patient should have been added to the trial');
        $this->assertEquals(
            $shortlisted->id,
            $trialPatient->status->id,
            'The patietn status should be shortlisted'
        );
        $this->assertEquals($trial->id, $trialPatient->trial->id, 'The trial id should match the patient trial id');
        $this->assertEquals(
            $this->treatment_type('treatment_type_unknown')->id,
            $trialPatient->treatment_type_id,
            'The patient treatment type should start at unknown'
        );
    }

    /**
     * @throws Exception
     */
    public function testRemovePatient()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_1');

        /* @var Trial $trial */
        $trial = $this->trial('trial1');
        $patient = $this->patient('patient1');

        $this->assertNotNull(TrialPatient::model()->find(
            'trial_id = :trialId AND patient_id = :patientId',
            array(
                ':trialId' => $trial->id,
                ':patientId' => $patient->id,
            )
        ), 'The patient should have started in the trial');

        $trial->removePatient($trialPatient->patient_id);

        $this->assertNull(TrialPatient::model()->find(
            'trial_id = :trialId AND patient_id = :patientId',
            array(
                ':trialId' => $trial->id,
                ':patientId' => $patient->id,
            )
        ), 'The patient should no longer be in the trial');
    }

    /**
     * @throws Exception
     */
    public function testAddUserPermission()
    {
        /* @var Trial $trial2 */
        $trial2 = $this->trial('trial2');
        /* @var User $user2 */
        $user2 = $this->user('user2');

        $result = $trial2->addUserPermission($user2->id, $this->trial_permission('trial_permission_view'), null);
        $this->assertEquals(
            Trial::RETURN_CODE_USER_PERMISSION_OK,
            $result,
            'The permission should have been added successfully'
        );
    }

    /**
     * @throws Exception
     */
    public function testAddUserPermissionClash()
    {
        /* @var Trial $trial */
        $trial = $this->trial('trial1');
        $user1 = $this->user('user1');

        $result = $trial->addUserPermission($user1->id, $this->trial_permission('trial_permission_view'), null);
        $this->assertEquals(
            Trial::RETURN_CODE_USER_PERMISSION_ALREADY_EXISTS,
            $result,
            'The permission already exists, and a duplicate should have been prevented'
        );
    }

    /**
     * @throws Exception
     */
    public function testRemoveUserPermission()
    {
        /* @var Trial $trial */
        $trial = $this->trial('trial1');
        /* @var UserTrialAssignment $userPermission */
        $userPermission = $this->user_trial_assignment('user_trial_assignment_2');

        $this->assertEquals(
            Trial::REMOVE_PERMISSION_RESULT_SUCCESS,
            $trial->removeUserAssignment($userPermission->id),
            'The permission should have been removed successfully'
        );
    }

    /**
     * @throws Exception
     */
    public function testRemoveOwner()
    {
        /* @var Trial $trial */
        $trial = $this->trial('trial1');
        /* @var UserTrialAssignment $userPermission */
        $userPermission = $this->user_trial_assignment('user_trial_assignment_1');

        $this->assertEquals(
            Trial::REMOVE_PERMISSION_RESULT_CANT_REMOVE_OWNER,
            $trial->removeUserAssignment($userPermission->id),
            'The manager should not have been removable'
        );
    }

    /**
     * @throws Exception
     */
    public function testCloseTrial()
    {
        /* @var Trial $trial */
        $trial = $this->trial('trial1');

        $this->assertEquals(1, $trial->is_open, 'The trial should initially be open');
        $result = $trial->close();
        $this->assertTrue($result, 'Closing an open trial should have been successful');
        $this->assertEquals(0, $trial->is_open, 'The trial should now be closed');
    }

    /**
     * @throws Exception
     */
    public function testReopenTrial()
    {
        /* @var Trial $trial */
        $trial = $this->trial('trial3');

        $this->assertEquals(0, $trial->is_open, 'The trial should initially be closed');
        $result = $trial->reopen();
        $this->assertTrue($result, 'Reopening the trial should have been successful');
        $this->assertEquals(1, $trial->is_open, 'The trial should now be open');
    }

    /**
     * @throws Exception
     */
    public function testClosingTrialDoesNotSetClosedDateIfNotEmpty()
    {
        /* @var Trial $trial */
        $trial = $this->trial('trial2');

        $this->assertEquals(1, $trial->is_open, 'The Trial should initially be open');
        $closed_date = $trial->closed_date;
        $this->assertNotNull($trial->closed_date, 'The trial should not initially have a closed date');
        $trial->close();
        $this->assertStringStartsWith(
            $trial->closed_date,
            $closed_date,
            'The closed date should have remained unchanged when the trial is closed'
        );
    }

    public function testDeepDeleteTrial()
    {
        $this->assertTrue($this->trial('trial1')->deepDelete(), 'Deleting a trial should be successful');
        $this->assertTrue($this->trial('trial2')->deepDelete(), 'Deleting a trial should be successful');
        $this->assertTrue($this->trial('trial3')->deepDelete(), 'Deleting a trial should be successful');
        $this->assertTrue(
            $this->trial('non_intervention_trial_1')->deepDelete(),
            'Deleting a trial should be successful'
        );
    }
}
