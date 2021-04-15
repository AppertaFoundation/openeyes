<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses;
use OEModule\OphCiExamination\models\OphCiExamination_Diagnosis;

/**
 * Class PatientDiagnosisParameterTest
 * @covers PatientDiagnosisParameter
 * @covers CaseSearchParameter
 * @method Patient patient($fixtureId)
 * @method OphCiExamination_Diagnosis ophciexamination_diagnosis($fixtureId)
 * @method Element_OphCiExamination_Diagnoses et_ophciexamination_diagnoses($fixtureId)
 * @method Disorder disorder($fixtureId)
 * @method Episode episode($fixtureId)
 * @method Event event($fixtureId)
 */
class PatientDiagnosisParameterTest extends CDbTestCase
{
    /**
     * @var PatientDiagnosisParameter $parameter
     */
    protected PatientDiagnosisParameter $parameter;

    protected $fixtures = array(
        'disorder' => 'Disorder',
        'ophciexamination_diagnosis' => OphCiExamination_Diagnosis::class,
        'et_ophciexamination_diagnoses' => Element_OphCiExamination_Diagnoses::class,
        'event' => 'Event',
        'patient' => 'Patient',
        'episode' => 'Episode',
    );

    public function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientDiagnosisParameter();
        $this->parameter->id = 0;
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter);
    }

    public function testBindValues(): void
    {
        $this->parameter->value = 'Diabetes';
        $this->parameter->firm_id = 1;
        $expected = array(
            'p_d_value_0' => $this->parameter->value,
            'p_d_firm_0' => $this->parameter->firm_id,
            'p_d_only_latest_event_0' => $this->parameter->only_latest_event,
        );

        // Ensure that all bind values are returned.
        self::assertEquals($expected, $this->parameter->bindValues());

        $this->parameter->firm_id = null;

        $expected = array(
            'p_d_value_0' => $this->parameter->value,
            'p_d_firm_0' => null,
            'p_d_only_latest_event_0' => $this->parameter->only_latest_event,
        );

        self::assertEquals($expected, $this->parameter->bindValues());
    }

    public function attributeValueTestList(): array
    {
        return array(
            'Operation' => array(
                'attribute' => 'operation',
                'expected' => '=',
            ),
            'Value' => array(
                'attribute' => 'value',
                'expected' => 'Myopia',
            ),
            'Firm' => array(
                'attribute' => 'firm_id',
                'expected' => 'Aylward Firm',
            ),
            'Only latest event' => array(
                'attribute' => 'only_latest_event',
                'expected' => 'Only patient\'s latest event',
            ),
            'Invalid attribute' => array(
                'attribute' => 'invalid',
                'expected' => 'null',
                'exception' => 'CException',
            ),
        );
    }

    /**
     * @dataProvider attributeValueTestList
     * @param $attribute
     * @param $expected
     * @param null|string $exception
     * @throws CException
     */
    public function testGetValueForAttribute($attribute, $expected, $exception = null): void
    {
        $this->parameter->operation = '=';
        $this->parameter->value = 1;
        $this->parameter->firm_id = 1;
        $this->parameter->only_latest_event = true;
        if ($exception) {
            $this->expectException($exception);
            $this->parameter->getValueForAttribute($attribute);
        } else {
            self::assertEquals($expected, $this->parameter->getValueForAttribute($attribute));
        }
    }

    /**
     * @covers CaseSearchParameter
     */
    public function testGetOptions(): void
    {
        $options = array(
            'value_type' => 'string_search',
        );
        $options['operations'][0] = array('label' => 'INCLUDES', 'id' => 'IN');
        $options['operations'][1] = array('label' => 'DOES NOT INCLUDE', 'id' => 'NOT IN');

        $firms = Firm::model()->getListWithSpecialties();
        $options['option_data'] = array(
            array(
                'id' => 'firm',
                'field' => 'firm_id',
                'options' => array_map(
                    static function ($item, $key) {
                        return array('id' => $key, 'label' => $item);
                    },
                    $firms,
                    array_keys($firms)
                ),
            ),
            array(
                'id' => 'latest-event',
                'field' => 'only_latest_event',
                'options' => array(
                    array('id' => 1, 'label' => 'Only latest event')
                ),
            ),
        );
        self::assertEquals($options, $this->parameter->getOptions());
    }

    /**
     * @covers PatientDiagnosisParameter
     * @covers CaseSearchParameter
     */
    public function testGetCommonItemsForTerm(): void
    {
        // Full match
        self::assertCount(1, PatientDiagnosisParameter::getCommonItemsForTerm('Myopia'));
        self::assertEquals('Myopia', PatientDiagnosisParameter::getCommonItemsForTerm('Myopia')[0]['label']);
        self::assertEquals(1, PatientDiagnosisParameter::getCommonItemsForTerm('Myopia')[0]['id']);

        // Partial match
        self::assertCount(2, PatientDiagnosisParameter::getCommonItemsForTerm('m'));
    }

    public function getSearchData(): array
    {
        return array(
            'Exact match, no firm' => array(
                'op' => 'IN',
                'value' => 1,
                'firm_id' => null,
                'expected_ids' => array(1, 2, 3, 7),
            ),
            'Exact match with firm' => array(
                'op' => 'IN',
                'value' => 1,
                'firm_id' => 2,
                'expected_ids' => array(),
            ),
            'Does not match, no firm' => array(
                'op' => 'NOT IN',
                'value' => 1,
                'firm_id' => null,
                'expected_ids' => array(4, 5, 6, 8, 9, 10),
            ),
            'Does not match, including firm' => array(
                'op' => 'NOT IN',
                'value' => 1,
                'firm_id' => 2,
                'expected_ids' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
            ),
        );
    }

    /**
     * @covers PatientDiagnosisParameter
     * @dataProvider getSearchData
     * @param $op
     * @param $value
     * @param $firm_id
     * @param $expected_ids
     */
    public function testSearch($op, $value, $firm_id, $expected_ids): void
    {
        $expected = array();
        foreach ($expected_ids as $patientNum) {
            $expected[] = $this->patient("patient$patientNum");
        }

        $this->parameter->operation = $op;
        $this->parameter->value = $value;
        $this->parameter->firm_id = $firm_id;
        $this->parameter->only_latest_event = false;

        self::assertTrue($this->parameter->validate());

        $results = Yii::app()->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }

        $patients = Patient::model()->findAllByPk($ids);

        self::assertEquals($expected, $patients);
    }

    /**
     * @dataProvider getSearchData
     * @param $op
     * @param $value
     * @param $firm_id
     */
    public function testSaveSearch($op, $value, $firm_id): void
    {
        $this->parameter->operation = $op;
        $this->parameter->value = $value;
        $this->parameter->firm_id = $firm_id;
        $this->parameter->only_latest_event = 0;

        $actual = $this->parameter->saveSearch();

        self::assertEquals($op, $actual['operation']);
        self::assertEquals($value, $actual['value']);
        self::assertEquals($firm_id, $actual['firm_id']);
        self::assertEquals(0, $actual['only_latest_event']);
    }

    public function getAuditParams(): array
    {
        return array(
            'All params' => array(
                'operator' => 'IN',
                'value' => 1,
                'firm_id' => 1,
                'only_latest_event' => true
            ),
            'Null firm_id, only latest event' => array(
                'operator' => 'IN',
                'value' => 1,
                'firm_id' => null,
                'only_latest_event' => true
            ),
            'Null firm_id, all events, not equal' => array(
                'operator' => 'NOT IN',
                'value' => 1,
                'firm_id' => null,
                'only_latest_event' => false
            ),
        );
    }

    /**
     * @dataProvider getAuditParams
     * @covers PatientDiagnosisParameter
     * @param $operator
     * @param $value
     * @param $firm_id
     * @param $only_latest_event
     */
    public function testGetAuditData($operator, $value, $firm_id, $only_latest_event): void
    {
        $op = '=';
        if ($operator !== 'IN') {
            $op = '!=';
        }

        $expected = "diagnosis: $op \"$value\"";

        $this->parameter->operation = $operator;
        $this->parameter->value = $value;
        $this->parameter->firm_id = $firm_id;
        $this->parameter->only_latest_event = $only_latest_event;

        if ($firm_id !== '' && $firm_id !== null) {
            $firm = Firm::model()->findByPk($firm_id);
            $expected .= " diagnosed by {$firm->getNameAndSubspecialty()}";
        }

        if ($only_latest_event) {
            $expected .= ' with only the latest event';
        }

        self::assertEquals($expected, $this->parameter->getAuditData());
    }
}
