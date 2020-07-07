<?php

/**
 * Class PreviousProceduresParameterTest
 * @method procedures($fixtureId)
 */
class PreviousProceduresParameterTest extends CDbTestCase
{
    public $parameter;

    protected $fixtures = array(
        'patients' => Patient::class,
        'procedures' => Procedure::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function getArgs()
    {
        return array(
            'Equal' => array(
                'operation' => '=',
            ),
            'Not equal' => array(
                'operation' => '!=',
            ),
        );
    }

    public function setUp()
    {
        parent::setUp();
        $this->parameter = new PreviousProceduresParameter();
        $this->parameter->id = 0;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter);
    }

    public function testGetCommonItemsForTerm()
    {
        $this->assertCount(1, PreviousProceduresParameter::getCommonItemsForTerm('Foobar'));
        $this->assertCount(3, PreviousProceduresParameter::getCommonItemsForTerm('Procedure'));
        $this->assertCount(1, PreviousProceduresParameter::getCommonItemsForTerm('test procedure 2'));
    }

    /**
     * @dataProvider getArgs
     * @param $operation
     * @throws CException
     */
    public function testGetValueForAttribute($operation)
    {
        $this->parameter->operation = $operation;
        $this->parameter->value = 1;

        $expected = $this->procedures('procedure1');

        $this->assertEquals($expected->term, $this->parameter->getValueForAttribute('value'));
        $this->assertEquals($operation, $this->parameter->getValueForAttribute('operation'));

        $this->expectException('CException');
        $this->parameter->getValueForAttribute('invalid');
    }

    public function testBindValues()
    {
        $this->parameter->value = 1;
        $expected = array(
            "p_p_value_0" => 1,
        );

        $this->assertEquals($expected, $this->parameter->bindValues());
    }

    /**
     * @dataProvider getArgs
     * @param $operation
     */
    public function testQuery($operation)
    {
        $this->parameter->operation = $operation;
        $this->parameter->value = 1;

        $this->assertTrue($this->parameter->validate());
        $query = "
            SELECT pa.id
            FROM patient pa
            JOIN episode ep ON ep.patient_id = pa.id
            JOIN event ev ON ep.id = ev.episode_id
            JOIN et_ophtroperationnote_procedurelist eop ON ev.id = eop.booking_event_id
            JOIN et_ophtroperationbooking_operation o ON ev.id = o.event_id
              AND o.status_id = (SELECT id FROM ophtroperationbooking_operation_status WHERE name = 'Completed')
            JOIN ophtroperationnote_procedurelist_procedure_assignment op ON eop.id = op.procedurelist_id
            JOIN proc ON op.proc_id = proc.id
            AND proc.id = :p_p_value_0
            UNION
            SELECT pa.id
            FROM patient pa
            JOIN episode ep ON ep.patient_id = pa.id
            JOIN event e on ep.id = e.episode_id
            JOIN et_ophciexamination_pastsurgery eop2 on e.id = eop2.event_id
            JOIN ophciexamination_pastsurgery_op o3 on eop2.id = o3.element_id
               AND o3.id = :p_p_value_0";

        if ($operation === '!=') {
            $query = "
                SELECT outer_pat.id
                FROM patient outer_pat 
                WHERE outer_pat.id NOT IN (
                  {$query}
                )";
        }

        $this->assertEquals($query, $this->parameter->query());
    }

    /**
     * @dataProvider getArgs
     * @param $operation
     */
    public function testGetAuditData($operation)
    {
        $this->parameter->operation = $operation;
        $this->parameter->value = 1;
        $expected = "previous_procedures: $operation Foobar Procedure";

        $this->assertEquals($expected, $this->parameter->getAuditData());
    }
}
