<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses;
use OEModule\OphCiExamination\models\OphCiExamination_Diagnosis;

/**
 * @method Patient patient($fixtureId)
 */
class OETrial_ReportTrialCohortTest extends CDbTestCase
{
    protected $instance;

    protected $fixtures = array(
        'user' => 'User',
        'trial_type' => 'TrialType',
        'trial' => 'Trial',
        'institution' => Institution::class,
        'site' => Site::class,
        'patient_identifier_type' => PatientIdentifierType::class,
        'patient' => Patient::class,
        'patient_identifier' => PatientIdentifier::class,
        'contact' => Contact::class,
        'treatment_type' => 'TreatmentType',
        'trial_patient_status' => 'TrialPatientStatus',
        'trial_patient' => 'TrialPatient',
        'trial_permission' => 'TrialPermission',
        'user_trial_assignment' => 'UserTrialAssignment',
        'event' => Event::class,
        'episode' => Episode::class,
        'et_ophciexamination_diagnose' => Element_OphCiExamination_Diagnoses::class,
        'ophciexamination_diagnosis' => OphCiExamination_Diagnosis::class,
        'secondary_diagnosis' => SecondaryDiagnosis::class,
        'disorder' => Disorder::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->session['selected_institution_id'] = 1;
        Yii::app()->session['selected_site_id'] = 1;
        Yii::app()->params['display_primary_number_usage_code'] = 'LOCAL';
        Yii::app()->getModule('OETrial');
    }

    public static function tearDownAfterClass()
    {
        unset(Yii::app()->session['selected_institution_id']);
        unset(Yii::app()->session['selected_site_id']);
        unset(Yii::app()->params['display_primary_number_usage_code']);
    }

    public function setUp()
    {
        parent::setUp();
        $this->instance = new OETrial_ReportTrialCohort();
        $this->instance->user_institution_id = 1;
        $this->instance->user_selected_site_id = 1;
    }

    public function tearDown()
    {
        unset($this->instance);
        parent::tearDown();
    }

    /**
     * @return string[][]
     */
    public function getData()
    {
        return array(
            array(
                'fixture' => 'patient1',
                'expected_row' => '12345,"1 Jan 1970",Jim,Aylward,abc,Unknown,Shortlisted,{{DIAGNOSES}},,,"{{IDs}}"' . "\n"
            ),
            array(
                'fixture' => 'patient3',
                'expected_row' => '34567,"1 Jan 1960",Edward,Allan,def,Unknown,Shortlisted,{{DIAGNOSES}},,,"{{IDs}}"' . "\n"
            ),
            array(
                'fixture' => 'patient4',
                'expected_row' => '34321,"1 Jan 1977",Sarah,Shore,qwerty,Intervention,Accepted,{{DIAGNOSES}},,,"{{IDs}}"' . "\n"
            ),
        );
    }

    /**
     * @covers OETrial_ReportTrialCohort
     */
    public function testRules()
    {
        $this->assertEquals(array(array('trialID', 'safe')), $this->instance->rules());
    }

    /**
     * @covers OETrial_ReportTrialCohort
     * @throws CException
     */
    public function testRun()
    {
        $expected = $this->trial('trial1');
        $this->instance->trialID = $expected->id;
        $this->instance->run();
        $this->assertCount(2, $this->instance->patients);
    }

    /**
     * @covers OETrial_ReportTrialCohort
     */
    public function testDescription()
    {
        $expected = $this->trial('trial1');
        $this->instance->trialID = $expected->id;
        $this->assertEquals("Participants in trial: {$expected->name}", $this->instance->description());
    }

    /**
     * @covers OETrial_ReportTrialCohort
     * @dataProvider getData
     * @param $fixture
     * @param string|null $expected_row
     */
    public function testAddPatientResultItem($fixture, $expected_row = null)
    {
        $this->assertCount(0, $this->instance->patients);
        $item = $this->patient($fixture);
        $localIdentifier = $item->localIdentifiers[0];
        $globalIdentifier = $item->globalIdentifier;

        $patient = array(
            'id' => $item->id,
            'identifier' => $localIdentifier->value,
            'dob' => $item->dob,
            'first_name' => $item->first_name,
            'last_name' => $item->last_name,
            'external_trial_identifier' => $item->trials[0]->external_trial_identifier,
            'trial_patient_id' => $item->trials[0]->id,
            'comment' => $item->trials[0]->comment,
            'all_ids' => $localIdentifier->patientIdentifierType->short_title . ' (' . $localIdentifier->patientIdentifierType->institution->short_name . '): ' . $localIdentifier->getDisplayValue() . ', ' .
                            $globalIdentifier->patientIdentifierType->short_title . ' (' . $globalIdentifier->patientIdentifierType->institution->short_name . '): ' . $globalIdentifier->getDisplayValue() . ', ',
        );

        $this->instance->addPatientResultItem($patient);

        unset($patient['id']);
        $this->assertCount(1, $this->instance->patients);
        $this->assertEquals($patient, $this->instance->patients[$item->id]);
    }

    /**
     * @covers OETrial_ReportTrialCohort
     * @dataProvider getData
     * @param $fixture
     * @param $expected_row
     * @throws CHttpException
     */
    public function testToCSV($fixture, $expected_row = null)
    {
        $baseStr = "ID,\"Date of Birth\",\"First Name\",\"Last Name\",\"Trial Identifier\",\"Treatment Type\",\"Status Id\",Diagnoses,Medications,Comments,\"Patient IDs\"\n";
        $item = $this->patient($fixture);
        $diagnoses = array();
        foreach ($item->getOphthalmicDiagnosesSummary() as $diagnosis) {
            $name = explode('~', $diagnosis, 3)[1];
            $diagnoses[] = $name;
        }
        foreach ($item->systemicDiagnoses as $diagnosis) {
            $diagnoses[] = $diagnosis->disorder->term;
        }
        $diagStr = (!empty($diagnoses) ? '"' : null) . implode('; ', $diagnoses) . (!empty($diagnoses) ? '"' : null);
        $baseStr .= str_replace('{{DIAGNOSES}}', $diagStr, $expected_row);

        $localIdentifier = $item->localIdentifiers[0];
        $globalIdentifier = $item->globalIdentifier;

        $IDStr = $localIdentifier->patientIdentifierType->short_title . ' (' . $localIdentifier->patientIdentifierType->institution->short_name . '): ' . $localIdentifier->getDisplayValue() . ', ' .
            $globalIdentifier->patientIdentifierType->short_title . ' (' . $globalIdentifier->patientIdentifierType->institution->short_name . '): ' . $globalIdentifier->getDisplayValue() . ', ';
        $baseStr = str_replace('{{IDs}}', $IDStr, $baseStr);
        $patient = array(
            'id' => $item->id,
            'dob' => $item->dob,
            'first_name' => $item->first_name,
            'last_name' => $item->last_name,
            'external_trial_identifier' => $item->trials[0]->external_trial_identifier,
            'trial_patient_id' => $item->trials[0]->id,
            'comment' => $item->trials[0]->comment,
        );
        $this->instance->addPatientResultItem($patient);
        $this->assertEquals($baseStr, $this->instance->toCSV());
    }

    /**
     * @covers OETrial_ReportTrialCohort
     */
    public function testGetDbCommand()
    {
        $actual = $this->instance->getDbCommand();
        $expected_join = array(
            'JOIN `trial_patient` `t_p` ON t.id = t_p.trial_id',
            'JOIN `patient` `p` ON p.id = t_p.patient_id',
            'JOIN `contact` `c` ON p.contact_id = c.id'
        );
        $expected_group = '`p`.`id`, `c`.`first_name`, `c`.`last_name`, `p`.`dob`, `t_p`.`external_trial_identifier`, `t_p`.`treatment_type_id`, `t_p`.`status_id`, `t_p`.`comment`';
        $this->assertEquals('`trial` `t`', $actual->from);
        $this->assertEquals($expected_join, $actual->join);
        $this->assertEquals($expected_group, $actual->group);
        $this->assertEquals('`c`.`first_name`, `c`.`last_name`', $actual->order);
    }
}
