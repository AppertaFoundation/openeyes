<?php

class TrialPatientTest extends CDbTestCase
{
    public $fixtures = array(
        'user' => 'User',
        'trial_type' => 'TrialType',
        'trial' => 'Trial',
        'patient' => 'Patient',
        'trial_patient_status' => 'TrialPatientStatus',
        'treatment_type' => 'TreatmentType',
        'trial_patient' => 'TrialPatient',
        'trial_permission' => 'TrialPermission',
        'user_trial_assignment' => 'UserTrialAssignment',
    );

    public static function setupBeforeClass()
    {
        Yii::app()->getModule('OETrial');
    }

    public function testChangeStatus()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_1');
        $trialPatient->changeStatus(TrialPatientStatus::model()->find('code = "ACCEPTED"'));
        $this->assertEquals('ACCEPTED', $trialPatient->status->code);
    }

    public function testChangeStatusAlreadyInIntervention()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_2');
        $this->setExpectedException(CHttpException::class, 500);
        $trialPatient->changeStatus(TrialPatientStatus::model()->find('code = "ACCEPTED"'));
        $this->assertEquals('SHORTLISTED', $trialPatient->status);
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
        $interventionTreatment = $this->treatment_type('treatment_type_intervention');
        $trialPatient->updateTreatmentType($interventionTreatment );
        $this->assertEquals($interventionTreatment ->id, $trialPatient->treatment_type_id);
    }

    public function testUpdateTreatmentTypeClosed()
    {
        /* @var TrialPatient $trialPatient */
        $trialPatient = $this->trial_patient('trial_patient_1');
        $this->setExpectedException('Exception', 'You cannot change the treatment type until the trial is closed.');
        $trialPatient->updateTreatmentType($this->treatment_type('treatment_type_intervention'));
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
        $this->assertEquals($this->treatment_type('treatment_type_intervention')->id,
            TrialPatient::getLastPatientTreatmentType($trialPatient->patient)->id,
            'The patient has been in an intervention trial with intervention treatment, which should be returned.');


        $this->assertNull(TrialPatient::getLastPatientTreatmentType($trialPatient->patient, $trialPatient->trial_id),
            'The patient has been in no intervention trial , this value should be null ');
    }
}
