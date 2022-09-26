<?php

/**
 * Class PreviousTrialParameterTest
 * @covers PreviousTrialParameter
 * @covers CaseSearchParameter
 * @method patient($fixtureId)
 * @method trial_type($fixtureId)
 * @method trial($fixtureId)
 * @method trial_patient_status($fixtureId)
 * @method trial_patient($fixtureId)
 * @method treatment_type($fixtureId)
 */

class PreviousTrialParameterTest extends OEDbTestCase
{
    /**
     * @var PreviousTrialParameter $object
     */
    protected PreviousTrialParameter $object;

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

    public static function setUpBeforeClass(): void
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->object = new PreviousTrialParameter();
        $this->object->id = 0;
    }

    public function tearDown(): void
    {
        unset($this->object);
        parent::tearDown();
    }

    public function getData(): array
    {
        return array(
            'IN empty' => array(
                'op' => 'IN',
                'mode' => 'empty',
            ),
            'NOT IN INTERVENTION' => array(
                'op' => 'NOT IN',
                'mode' => 'partial-intervention'
            ),
            'NOT IN NONINTERVENTION' => array(
                'op' => 'NOT IN',
                'mode' => 'partial-non-intervention'
            ),
            'IN full INTERVENTION' => array(
                'op' => 'IN',
                'mode' => 'full-intervention',
            ),
            'IN full NONINTERVENTION' => array(
                'op' => 'IN',
                'mode' => 'full-non-intervention',
            ),
        );
    }

    /**
     * @dataProvider getData
     * @param string $op
     * @param string $mode
     */
    public function testQueryOperation(string $op, string $mode): void
    {
        $this->populateDummyData($op, $mode);

        self::assertTrue($this->object->validate());
    }

    protected function populateDummyData($op, $mode): void
    {
        $this->object->operation = $op;
        switch ($mode) {
            case 'partial-intervention':
                $this->object->status = '';
                $this->object->trialTypeId = $this->trial_type('trial_type_intervention')->id;
                $this->object->interventionTrial = '';
                $this->object->nonInterventionTrial = '';
                $this->object->treatmentTypeId = '';
                break;
            case 'partial-non-intervention':
                $this->object->status = '';
                $this->object->trialTypeId = $this->trial_type('trial_type_non_intervention')->id;
                $this->object->interventionTrial = '';
                $this->object->nonInterventionTrial = '';
                $this->object->treatmentTypeId = '';
                break;
            case 'full-intervention':
                $this->object->status = $this->trial_patient_status('trial_patient_status_accepted')->id;
                $this->object->trialTypeId = $this->trial_type('trial_type_intervention')->id;
                $this->object->interventionTrial = $this->trial('trial1')->id;
                $this->object->nonInterventionTrial = '';
                $this->object->treatmentTypeId = $this->treatment_type('treatment_type_intervention')->id;
                break;
            case 'full-non-intervention':
                $this->object->status = $this->trial_patient_status('trial_patient_status_shortlisted')->id;
                $this->object->trialTypeId = $this->trial_type('trial_type_non_intervention')->id;
                $this->object->interventionTrial = '';
                $this->object->nonInterventionTrial = $this->trial('non_intervention_trial_1')->id;
                $this->object->treatmentTypeId = $this->treatment_type('treatment_type_placebo')->id;
                break;
            default:
                $this->object->status = '';
                $this->object->trialTypeId = '';
                $this->object->interventionTrial = '';
                $this->object->nonInterventionTrial = '';
                $this->object->treatmentTypeId = '';
                break;
        }
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     */
    public function testBindValues($op, $mode): void
    {
        $this->populateDummyData($op, $mode);

        $binds = array();
        if ($this->object->trialType) {
            if ($this->object->trialType->code === TrialType::INTERVENTION_CODE && $this->object->interventionTrial !== '') {
                $binds[":p_t_trial_0"] = $this->object->interventionTrial;
            } elseif ($this->object->trialType->code === TrialType::NON_INTERVENTION_CODE && $this->object->nonInterventionTrial !== '') {
                $binds[":p_t_trial_0"] = $this->object->nonInterventionTrial;
            } else {
                $binds[":p_t_trial_type_0"] = $this->object->trialTypeId;
            }
        }

        if ($this->object->status && $this->object->status !== null) {
            $binds[":p_t_status_0"] = $this->object->status;
        }
        if (
            (!$this->object->trialType || $this->object->trialType->code !== TrialType::NON_INTERVENTION_CODE)
            && $this->object->treatmentTypeId && $this->object->treatmentTypeId !== null
        ) {
            $binds[":p_t_treatment_type_id_0"] = $this->object->treatmentTypeId;
        }

        self::assertEquals($binds, $this->object->bindValues());
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     * @throws CException
     */
    public function testGetValueForAttribute($op, $mode): void
    {
        $this->populateDummyData($op, $mode);

        self::assertEquals($op, $this->object->getValueForAttribute('operation'));

        switch ($mode) {
            case 'partial-intervention':
                $this->assertEquals('Any trial status', $this->object->getValueForAttribute('status'));
                $this->assertEquals('Participating in ' . $this->trial_type('trial_type_intervention')->name . ' trial', $this->object->getValueForAttribute('trialTypeId'));
                $this->assertEquals('Any trial', $this->object->getValueForAttribute('interventionTrial'));
                $this->assertEquals('', $this->object->getValueForAttribute('nonInterventionTrial'));
                $this->assertEquals('Received any treatment', $this->object->getValueForAttribute('treatmentTypeId'));
                break;
            case 'partial-non-intervention':
                $this->assertEquals('Any trial status', $this->object->getValueForAttribute('status'));
                $this->assertEquals('Participating in ' . $this->trial_type('trial_type_non_intervention')->name . ' trial', $this->object->getValueForAttribute('trialTypeId'));
                $this->assertEquals('', $this->object->getValueForAttribute('interventionTrial'));
                $this->assertEquals('Any trial', $this->object->getValueForAttribute('nonInterventionTrial'));
                $this->assertEquals('Received any treatment', $this->object->getValueForAttribute('treatmentTypeId'));
                break;
            case 'full-intervention':
                $this->assertEquals($this->trial_patient_status('trial_patient_status_accepted')->name . ' into trial', $this->object->getValueForAttribute('status'));
                $this->assertEquals('Participating in ' . $this->trial_type('trial_type_intervention')->name . ' trial', $this->object->getValueForAttribute('trialTypeId'));
                $this->assertEquals($this->trial('trial1')->name, $this->object->getValueForAttribute('interventionTrial'));
                $this->assertEquals('', $this->object->getValueForAttribute('nonInterventionTrial'));
                $this->assertEquals('Received ' . $this->treatment_type('treatment_type_intervention')->name . ' treatment', $this->object->getValueForAttribute('treatmentTypeId'));
                break;
            case 'full-non-intervention':
                $this->assertEquals($this->trial_patient_status('trial_patient_status_shortlisted')->name . ' in trial', $this->object->getValueForAttribute('status'));
                $this->assertEquals('Participating in ' . $this->trial_type('trial_type_non_intervention')->name . ' trial', $this->object->getValueForAttribute('trialTypeId'));
                $this->assertEquals('', $this->object->getValueForAttribute('interventionTrial'));
                $this->assertEquals($this->trial('non_intervention_trial_1')->name, $this->object->getValueForAttribute('nonInterventionTrial'));
                $this->assertEquals('Received ' . $this->treatment_type('treatment_type_placebo')->name . ' treatment', $this->object->getValueForAttribute('treatmentTypeId'));
                break;
            default:
                $this->assertEquals('Any trial status', $this->object->getValueForAttribute('status'));
                $this->assertEquals('Participating in any trial', $this->object->getValueForAttribute('trialTypeId'));
                $this->assertEquals('', $this->object->getValueForAttribute('interventionTrial'));
                $this->assertEquals('', $this->object->getValueForAttribute('nonInterventionTrial'));
                $this->assertEquals('Received any treatment', $this->object->getValueForAttribute('treatmentTypeId'));
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
    public function testSaveSearch($op, $mode): void
    {
        $this->populateDummyData($op, $mode);

        $results = $this->object->saveSearch();

        self::assertEquals($op, $results['operation']);

        switch ($mode) {
            case 'partial-intervention':
                $this->assertEquals('', $results['status']);
                $this->assertEquals($this->trial_type('trial_type_intervention')->id, $results['trialTypeId']);
                $this->assertEquals('', $results['interventionTrial']);
                $this->assertEquals('', $results['nonInterventionTrial']);
                $this->assertEquals('', $results['treatmentTypeId']);
                break;
            case 'partial-non-intervention':
                $this->assertEquals('', $results['status']);
                $this->assertEquals($this->trial_type('trial_type_non_intervention')->id, $results['trialTypeId']);
                $this->assertEquals('', $results['interventionTrial']);
                $this->assertEquals('', $results['nonInterventionTrial']);
                $this->assertEquals('', $results['treatmentTypeId']);
                break;
            case 'full-intervention':
                $this->assertEquals($this->trial_patient_status('trial_patient_status_accepted')->id, $results['status']);
                $this->assertEquals($this->trial_type('trial_type_intervention')->id, $results['trialTypeId']);
                $this->assertEquals($this->trial('trial1')->id, $results['interventionTrial']);
                $this->assertEquals('', $results['nonInterventionTrial']);
                $this->assertEquals($this->treatment_type('treatment_type_intervention')->id, $results['treatmentTypeId']);
                break;
            case 'full-non-intervention':
                $this->assertEquals($this->trial_patient_status('trial_patient_status_shortlisted')->id, $results['status']);
                $this->assertEquals($this->trial_type('trial_type_non_intervention')->id, $results['trialTypeId']);
                $this->assertEquals('', $results['interventionTrial']);
                $this->assertEquals($this->trial('non_intervention_trial_1')->id, $results['nonInterventionTrial']);
                $this->assertEquals($this->treatment_type('treatment_type_placebo')->id, $results['treatmentTypeId']);
                break;
            default:
                $this->assertEquals('', $results['status']);
                $this->assertEquals('', $results['trialTypeId']);
                $this->assertEquals('', $results['interventionTrial']);
                $this->assertEquals('', $results['nonInterventionTrial']);
                $this->assertEquals('', $results['treatmentTypeId']);
                break;
        }
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     */
    public function testGetAuditData($op, $mode): void
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
        $type = !$this->object->getTrialType() ? 'Any Trial Type with' : $trialTypes[$this->object->trialTypeId] . ' with ';
        $trial = 'Any trial with';
        if ($this->object->trialType) {
            if ($this->object->trialType->code === TrialType::INTERVENTION_CODE && $this->object->interventionTrial !== null && $this->object->interventionTrial !== '') {
                $trial = $trials[$this->object->interventionTrial] . ' with ';
            } elseif ($this->object->trialType->code === TrialType::NON_INTERVENTION_CODE && $this->object->nonInterventionTrial !== null && $this->object->nonInterventionTrial !== '') {
                $trial = $trials[$this->object->nonInterventionTrial] . ' with ';
            }
        }
        $treatment = $this->object->treatmentTypeId === null || $this->object->treatmentTypeId === '' ? 'Any Treatment' : $treatmentTypeList[$this->object->treatmentTypeId];

        $expected = "previous_trial: {$this->object->operation} $status $type $trial $treatment";

        self::assertEquals($expected, $this->object->getAuditData());
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     */
    public function testGetTrialType($op, $mode): void
    {
        $this->populateDummyData($op, $mode);
        $expected = '';
        if ($mode === 'partial-intervention' || $mode === 'full-intervention') {
            $expected = $this->trial_type('trial_type_intervention');
        } elseif ($mode === 'partial-non-intervention' || $mode === 'full-non-intervention') {
            $expected = $this->trial_type('trial_type_non_intervention');
        }
        self::assertEquals($expected, $this->object->getTrialType());
    }

    /**
     * @dataProvider getData
     * @param $op
     * @param $mode
     */
    public function testGetTreatmentType($op, $mode): void
    {
        $this->populateDummyData($op, $mode);
        $expected = '';
        if ($mode === 'full-intervention') {
            $expected = $this->treatment_type('treatment_type_intervention');
        } elseif ($mode === 'full-non-intervention') {
            $expected = $this->treatment_type('treatment_type_placebo');
        }
        self::assertEquals($expected, $this->object->getTreatmentType());
    }
}
