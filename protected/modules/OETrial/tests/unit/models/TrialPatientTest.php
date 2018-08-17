<?php

class TrialPatientTest extends CDbTestCase
{
    public $fixtures = array(
        'user' => 'User',
        'trial' => 'Trial',
        'patient' => 'Patient',
        'trial_patient' => 'TrialPatient',
        'user_trial_permission' => 'UserTrialPermission',
    );

    public static function setupBeforeClass()
    {
        Yii::app()->getModule('OETrial');
    }

    public function testChangeStatus()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_1');
        $result = $trialPatient->changeStatus(TrialPatient::STATUS_ACCEPTED);
        $this->assertEquals(TrialPatient::STATUS_CHANGE_CODE_OK, $result);
        $this->assertEquals(TrialPatient::STATUS_ACCEPTED, $trialPatient->patient_status);
    }

    public function testChangeStatusAlreadyInIntervention()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_2');
        $result = $trialPatient->changeStatus(TrialPatient::STATUS_ACCEPTED);
        $this->assertEquals(TrialPatient::STATUS_CHANGE_CODE_ALREADY_IN_INTERVENTION, $result);
        $this->assertEquals(TrialPatient::STATUS_SHORTLISTED, $trialPatient->patient_status);
    }

    public function testUpdateExternalId()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_1');

        $this->assertEquals('abc', $trialPatient->external_trial_identifier);
        $trialPatient->updateExternalId('123');
        $this->assertEquals('123', $trialPatient->external_trial_identifier);
        $trialPatient->updateExternalId(null);
        $this->assertNull($trialPatient->external_trial_identifier);
    }

    public function testUpdateTreatmentType()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_4');
        $trialPatient->updateTreatmentType(TrialPatient::TREATMENT_TYPE_INTERVENTION);
        $this->assertEquals(TrialPatient::TREATMENT_TYPE_INTERVENTION, $trialPatient->treatment_type);
    }

    public function testUpdateTreatmentTypeInvalid()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_1');
        $this->setExpectedException('Exception', 'Invalid treatment type: 1234');
        $trialPatient->updateTreatmentType(1234);
    }

    public function testUpdateTreatmentTypeClosed()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_1');
        $this->setExpectedException('Exception', 'You cannot change the treatment type until the trial is closed.');
        $trialPatient->updateTreatmentType(TrialPatient::TREATMENT_TYPE_INTERVENTION);
    }

    public function testIsInInterventionTrial()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_3');
        $this->assertTrue(TrialPatient::isPatientInInterventionTrial($trialPatient->patient),
            'The patient is in an intervention trial, this should return true');
    }

    public function testIsInAnotherInterventionTrial()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_3');
        $this->assertFalse(TrialPatient::isPatientInInterventionTrial($trialPatient->patient, $trialPatient->trial_id),
            'The optional trial id argument has been passed. The patient is in no other intervention trial, so this should return false.');
    }

    public function testPatientPreviousTreatmentType()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_3');
        $this->assertNull(TrialPatient::getLastPatientTreatmentType($trialPatient->patient),
            'The patient has not been in an intervention trial, and should not have a treatment type');
    }


    public function testPatientPreviousTreatmentTypeIntervention()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_4');
        $this->assertEquals(TrialPatient::TREATMENT_TYPE_INTERVENTION,
            TrialPatient::getLastPatientTreatmentType($trialPatient->patient),
            'The patient has been in an intervention trial with intervention treatment, which should be returned.');


        $this->assertNull(TrialPatient::getLastPatientTreatmentType($trialPatient->patient, $trialPatient->trial_id),
            'The patient has been in no intervention trial , this value should be null ');
    }
}
