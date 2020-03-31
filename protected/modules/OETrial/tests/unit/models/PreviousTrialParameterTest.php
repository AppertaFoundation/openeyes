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

    public static function setUpBeforeClass()
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
            'IN' => array(
                'op' => 'IN',
                'mode' => 'empty',
            ),
            'NOT IN' => array(
                'op' => 'NOT IN',
                'mode' => 'partial'
            ),
            'INVALID' => array(
                'op' => 'no',
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
        $this->object->operation = $op;
        if ($mode === 'empty') {
            $this->object->status = '';
            $this->object->trialTypeId = '';
            $this->object->trial = '';
            $this->object->treatmentTypeId = '';
        } elseif ($mode === 'partial') {
            $this->object->status = '';
            $this->object->trialTypeId = $this->trial_type('trial_type_intervention')->id;
            $this->object->trial = '';
            $this->object->treatmentTypeId = '';
        } else {
            // Full
            $this->object->status = $this->trial_patient_status('trial_patient_status_accepted')->id;
            $this->object->trialTypeId = $this->trial_type('trial_type_intervention')->id;
            $this->object->trial = $this->trial('trial1')->id;
            $this->object->treatmentTypeId = $this->treatment_type('treatment_type_placebo')->id;
        }

        $this->object->query();

        $this->assertTrue(true);
    }
}
