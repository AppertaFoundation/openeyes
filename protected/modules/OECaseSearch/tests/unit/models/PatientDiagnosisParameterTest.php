<?php

/**
 * Class PatientDiagnosisParameterTest
 */
class PatientDiagnosisParameterTest extends CDbTestCase
{
    /**
     * @var PatientDiagnosisParameter $parameter
     */
    protected $parameter;

    /**
     * @var DBProvider $searchProvider
     */
    protected $searchProvider;

    protected $fixtures = array(
        'disorder' => 'Disorder',
        'ophciexamination_diagnosis' => '\OEModule\OphCiExamination\models\OphCiExamination_Diagnosis',
        'et_ophciexamination_diagnoses' => '\OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses',
        'event' => 'Event',
        'patient' => 'Patient',
        'episode' => 'Episode',
    );

    protected function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientDiagnosisParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->parameter, $this->searchProvider);
    }

    /**
     * @covers PatientDiagnosisParameter::bindValues()
     */
    public function testBindValues()
    {
        $this->parameter->term = 'Diabetes';
        $this->parameter->firm_id = 1;
        $expected = array(
            'p_d_value_0' => '%' . $this->parameter->term . '%',
            'p_d_firm_0' => $this->parameter->firm_id,
            'p_d_only_latest_event_0' => $this->parameter->only_latest_event,
        );

        // Ensure that all bind values are returned.
        $this->assertEquals($expected, $this->parameter->bindValues());

        $this->parameter->firm_id = '';

        $expected = array(
            'p_d_value_0' => '%' . $this->parameter->term . '%',
            'p_d_firm_0' => null,
            'p_d_only_latest_event_0' => $this->parameter->only_latest_event,
        );

        $this->assertEquals($expected, $this->parameter->bindValues());
    }

    /**
     * @covers PatientDiagnosisParameter::query()
     */
    public function testSearchLike()
    {
        $expected = array();
        foreach (array(1, 2, 3, 7) as $patientNum) {
            $expected[] = $this->patient("patient$patientNum");
        }

        $this->parameter->operation = 'LIKE';
        $this->parameter->term = 'Myopia';
        $this->parameter->firm_id = '';

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }

        $patients = Patient::model()->findAllByPk($ids);

        $this->assertEquals($expected, $patients);
    }

    public function testFirmEqualitySearch()
    {
        $expected = array();
        /*foreach (array(3) as $patientNum) {
            $expected[] = $this->patient("patient$patientNum");
        }*/

        $this->parameter->operation = 'LIKE';
        $this->parameter->term = 'Myopia';
        $this->parameter->firm_id = 2;

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }

        $patients = Patient::model()->findAllByPk($ids);

        $this->assertEquals($expected, $patients);
    }

    /**
     * @covers PatientDiagnosisParameter::query()
     */
    public function testSearchNotLike()
    {
        $expected = array();
        foreach (array(4, 5, 6, 8, 9) as $patientNum) {
            $expected[] = $this->patient("patient$patientNum");
        }

        $this->parameter->operation = 'NOT LIKE';
        $this->parameter->term = 'Myopia';
        $this->parameter->firm_id = '';

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }

        $patients = Patient::model()->findAllByPk($ids);

        $this->assertEquals($expected, $patients);
    }

    public function testSearchFirmInequality()
    {
        $expected = array();
        foreach (array(1, 2, 3, 4, 5, 6, 7, 8, 9) as $patientNum) {
            $expected[] = $this->patient("patient$patientNum");
        }

        $this->parameter->operation = 'NOT LIKE';
        $this->parameter->term = 'Myopia';
        $this->parameter->firm_id = 2;

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $patients = Patient::model()->findAllByPk($ids);

        $this->assertEquals($expected, $patients);
    }
}
