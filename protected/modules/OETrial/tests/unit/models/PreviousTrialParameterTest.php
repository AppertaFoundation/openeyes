<?php

/**
 * Class PreviousTrialParameterTest
 * @method patient($fixtureId)
 * @method trial_type($fixtureId)
 * @method trial($fixtureId)
 * @method trial_patient_status($fixtureId)
 * @method trial_patient($fixtureId)
 * @method treatment_type($fixtureId)
 */
class PreviousTrialParameterTest extends CDbTestCase
{
    /**
     * @var PreviousTrialParameter $object
     */
    protected $object;

    protected $fixtures = array(
        'user' => User::class,
        'trial_type' => TrialType::class,
        'trial' => Trial::class,
        'trial_permission' => TrialPermission::class,
        'user_trial_assignment' => UserTrialAssignment::class,
        'patient' => Patient::class,
        'trial_patient_status' => TrialPatientStatus::class,
        'treatment_type' => TreatmentType::class,
        'trial_patient' => TrialPatient::class,
    );

    public static function setUpBeforeClass() : void
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->object = new PreviousTrialParameter();
        $this->object->id = 0;
    }

    public function tearDown()
    {
        unset($this->object);
        parent::tearDown();
    }

    public function getData()
    {
        return array(
            'IN empty' => array(
                'op' => 'IN',
                'mode' => 'empty',
            ),
            'NOT IN' => array(
                'op' => 'NOT IN',
                'mode' => 'partial'
            ),
            'IN full' => array(
                'op' => 'IN',
                'mode' => 'full',
            ),
        );
    }

    /**
     * @dataProvider getData
     * @param string $op
     * @param string $mode
     */
    public function testQueryOperation($op, $mode)
    {
        $this->populateDummyData($op, $mode);

        $this->object->query();

        $this->assertTrue($this->object->validate());
    }

    protected function populateDummyData($op, $mode)
    {
        $this->object->operation = $op;
        switch ($mode) {
            case 'empty':
                $this->object->status = '';
                $this->object->trialTypeId = '';
                $this->object->trial = '';
                $this->object->treatmentTypeId = '';
                break;
            case 'partial':
                $this->object->status = '';
                $this->object->trialTypeId = $this->trial_type('trial_type_intervention')->id;
                $this->object->trial = '';
                $this->object->treatmentTypeId = '';
                break;
            default:
                // Full
                $this->object->status = $this->trial_patient_status('trial_patient_status_accepted')->id;
                $this->object->trialTypeId = $this->trial_type('trial_type_intervention')->id;
                $this->object->trial = $this->trial('trial1')->id;
                $this->object->treatmentTypeId = $this->treatment_type('treatment_type_placebo')->id;
                break;
        }
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     */
    public function testBindValues($op, $mode)
    {
        $this->populateDummyData($op, $mode);

        $binds = array();
        if ($this->object->trialType) {
            if ($this->object->trial === '') {
                $binds[":p_t_trial_type_0"] = $this->object->trialTypeId;
            } else {
                $binds[":p_t_trial_0"] = $this->object->trial;
            }
        }

        if ($this->object->status !== '' && $this->object->status !== null) {
            $binds[":p_t_status_0"] = $this->object->status;
        }
        if ((!$this->object->trialType || $this->object->trialType->code !== TrialType::NON_INTERVENTION_CODE)
            && $this->object->treatmentTypeId !== '' && $this->object->treatmentTypeId !== null
        ) {
            $binds[":p_t_treatment_type_id_0"] = $this->object->treatmentTypeId;
        }

        $this->assertEquals($binds, $this->object->bindValues());
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     * @throws CException
     */
    public function testGetValueForAttribute($op, $mode)
    {
        $this->populateDummyData($op, $mode);

        $this->assertEquals($op, $this->object->getValueForAttribute('operation'));

        switch ($mode) {
            case 'empty':
                $this->assertEquals('Any trial status', $this->object->getValueForAttribute('status'));
                $this->assertEquals('Participating in any trial', $this->object->getValueForAttribute('trialTypeId'));
                $this->assertEquals('', $this->object->getValueForAttribute('trial'));
                $this->assertEquals('Received any treatment', $this->object->getValueForAttribute('treatmentTypeId'));
                break;
            case 'partial':
                $this->assertEquals('Any trial status', $this->object->getValueForAttribute('status'));
                $this->assertEquals('Participating in ' . $this->trial_type('trial_type_intervention')->name . ' trial', $this->object->getValueForAttribute('trialTypeId'));
                $this->assertEquals('Any trial', $this->object->getValueForAttribute('trial'));
                $this->assertEquals('Received any treatment', $this->object->getValueForAttribute('treatmentTypeId'));
                break;
            default:
                $this->assertEquals($this->trial_patient_status('trial_patient_status_accepted')->name . ' into trial', $this->object->getValueForAttribute('status'));
                $this->assertEquals('Participating in ' . $this->trial_type('trial_type_intervention')->name . ' trial', $this->object->getValueForAttribute('trialTypeId'));
                $this->assertEquals($this->trial('trial1')->name, $this->object->getValueForAttribute('trial'));
                $this->assertEquals('Received ' . $this->treatment_type('treatment_type_placebo')->name . ' treatment', $this->object->getValueForAttribute('treatmentTypeId'));
                break;
        }

        $this->expectException('CException');

        $this->object->getValueForAttribute('invalid');
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     */
    public function testSaveSearch($op, $mode)
    {
        $this->populateDummyData($op, $mode);

        $results = $this->object->saveSearch();

        $this->assertEquals($op, $results['operation']);

        switch ($mode) {
            case 'empty':
                $this->assertEquals('', $results['status']);
                $this->assertEquals('', $results['trialTypeId']);
                $this->assertEquals('', $results['trial']);
                $this->assertEquals('', $results['treatmentTypeId']);
                break;
            case 'partial':
                $this->assertEquals('', $results['status']);
                $this->assertEquals($this->trial_type('trial_type_intervention')->id, $results['trialTypeId']);
                $this->assertEquals('', $results['trial']);
                $this->assertEquals('', $results['treatmentTypeId']);
                break;
            default:
                $this->assertEquals($this->trial_patient_status('trial_patient_status_accepted')->id, $results['status']);
                $this->assertEquals($this->trial_type('trial_type_intervention')->id, $results['trialTypeId']);
                $this->assertEquals($this->trial('trial1')->id, $results['trial']);
                $this->assertEquals($this->treatment_type('treatment_type_placebo')->id, $results['treatmentTypeId']);
                break;
        }
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     */
    public function testGetAuditData($op, $mode)
    {
        $this->populateDummyData($op, $mode);
        $trialTypes = TrialType::getOptions();

        $statusList = array(
            TrialPatientStatus::model()->find('code = "SHORTLISTED"')->id => 'Shortlisted in',
            TrialPatientStatus::model()->find('code = "ACCEPTED"')->id => 'Accepted in',
            TrialPatientStatus::model()->find('code = "REJECTED"')->id => 'Rejected from',
        );
        $trials = Trial::getTrialList(isset($this->trialType) ? $this->object->trialType->id : '');
        $treatmentTypeList = TreatmentType::getOptions();

        $status = $this->object->status === null || $this->object->status === '' ? 'Included in' : $statusList[$this->object->status];
        $type = !$this->object->trialType ? 'Any Trial Type with' : $trialTypes[$this->object->trialTypeId];
        $trial = $this->object->trial === null || $this->object->trial === '' ? 'Any trial with' : $trials[$this->object->trial] . ' with ';
        $treatment = $this->object->treatmentTypeId === null || $this->object->treatmentTypeId === '' ? 'Any Treatment' : $treatmentTypeList[$this->object->treatmentTypeId];

        $expected = "previous_trial: {$this->object->operation} $status $type $trial $treatment";

        $this->assertEquals($expected, $this->object->getAuditData());
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     */
    public function testGetTrialType($op, $mode)
    {
        $this->populateDummyData($op, $mode);
        $expected = null;
        if ($mode !== 'empty') {
            $expected = $this->trial_type('trial_type_intervention');
        }
        $this->assertEquals($expected, $this->object->getTrialType());
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     */
    public function testGetTreatmentType($op, $mode)
    {
        $this->populateDummyData($op, $mode);
        $expected = null;
        if ($mode === 'full') {
            $expected = $this->treatment_type('treatment_type_placebo');
        }
        $this->assertEquals($expected, $this->object->getTreatmentType());
    }
}
