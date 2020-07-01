<?php

use OEModule\OphCiExamination\models\FamilyHistoryCondition;
use OEModule\OphCiExamination\models\FamilyHistoryRelative;
use OEModule\OphCiExamination\models\FamilyHistorySide;

class FamilyHistoryParameterTest extends CDbTestCase
{
    public $parameter;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->parameter = new FamilyHistoryParameter();
        $this->parameter->id = 0;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter);
    }

    public function getFields()
    {
        return array(
            'Any match' => array(
                'relative' => '',
                'side' => '',
                'condition' => '',
            ),
            'Specific relative' => array(
                'relative' => 1,
                'side' => '',
                'condition' => '',
            ),
            'Specific side' => array(
                'relative' => '',
                'side' => 1,
                'condition' => '',
            ),
            'Specific condition' => array(
                'relative' => '',
                'side' => '',
                'condition' => 1,
            ),
            'Exact match' => array(
                'relative' => 1,
                'side' => 1,
                'condition' => 1,
            ),
        );
    }

    /**
     * @dataProvider getFields
     * @param $relative
     * @param $side
     * @param $condition
     */
    public function testGetAuditData($relative, $side, $condition)
    {
        $this->parameter->relative = $relative;
        $this->parameter->side = $side;
        $this->parameter->condition = $condition;

        $side = $this->parameter->side ?: 'Any side';
        $relative = $this->parameter->relative ?: 'any relative';
        $condition = $this->parameter->condition ?: 'any condition';

        $expected = "family_history: $side $relative = \"$condition\"";

        $this->assertEquals($expected, $this->parameter->getAuditData());
    }

    /**
     * @dataProvider getFields
     * @param $relative
     * @param $side
     * @param $condition
     */
    public function testSaveSearch($relative, $side, $condition)
    {
        $this->parameter->relative = $relative;
        $this->parameter->side = $side;
        $this->parameter->condition = $condition;

        $saved_search = $this->parameter->saveSearch();

        $this->assertEquals('FamilyHistoryParameter', $saved_search['class_name']);
        $this->assertEquals('IN', $saved_search['operation']);
        $this->assertEquals($relative, $saved_search['relative']);
        $this->assertEquals($side, $saved_search['side']);
        $this->assertEquals($condition, $saved_search['condition']);
    }

    /**
     * @dataProvider getFields
     * @param $relative
     * @param $side
     * @param $condition
     * @throws CException
     */
    public function testGetValueForAttribute($relative, $side, $condition)
    {
        $this->parameter->relative = $relative;
        $this->parameter->side = $side;
        $this->parameter->condition = $condition;

        $expected = FamilyHistoryRelative::model()->findByPk($relative) ? FamilyHistoryRelative::model()->findByPk($relative)->name : 'Any relative';
        $this->assertEquals($expected, $this->parameter->getValueForAttribute('relative'));

        $expected = FamilyHistorySide::model()->findByPk($side) ? FamilyHistorySide::model()->findByPk($side)->name : 'Any side of family';
        $this->assertEquals($expected, $this->parameter->getValueForAttribute('side'));

        $expected = FamilyHistoryCondition::model()->findByPk($condition) ? ('has ' . FamilyHistoryCondition::model()->findByPk($condition)->name) : 'Any condition';
        $this->assertEquals($expected, $this->parameter->getValueForAttribute('condition'));

        $expected = 'IN';
        $this->assertEquals($expected, $this->parameter->getValueForAttribute('operation'));

        $this->expectException('CException');
        $this->parameter->getValueForAttribute('invalid');
    }

    /**
     * @dataProvider getFields
     * @covers FamilyHistoryParameter
     * @param $relative
     * @param $side
     * @param $condition
     */
    public function testBindValues($relative, $side, $condition)
    {
        $expected = array(
            'f_h_relative_0' => $relative,
            'f_h_side_0' => $side,
            'f_h_condition_0' => $condition
        );

        $this->parameter->relative = $relative;
        $this->parameter->side = $side;
        $this->parameter->condition = $condition;

        $this->assertEquals($expected, $this->parameter->bindValues());
    }

    /**
     * @dataProvider getFields
     * @param $relative
     * @param $side
     * @param $condition
     */
    public function testQuery($relative, $side, $condition)
    {
        $this->parameter->relative = $relative;
        $this->parameter->side = $side;
        $this->parameter->condition = $condition;

        $this->assertTrue($this->parameter->validate());

        $query_side = '';
        $query_relative = '';
        $query_condition = '';

        if ($side !== '') {
            $query_side = "AND (:f_h_side_0 IS NULL OR fh.side_id = :f_h_side_0)";
        }
        if ($relative !== '') {
            $query_relative = " AND (:f_h_relative_0 IS NULL OR fh.relative_id = :f_h_relative_0)";
        }
        if ($condition !== '') {
            $query_condition = " AND (:f_h_condition_0 IS NULL OR fh.condition_id = :f_h_condition_0)";
        }

        $expected = "
SELECT DISTINCT p.id 
FROM patient p 
JOIN patient_family_history fh
  ON fh.patient_id = p.id
WHERE 1=1 {$query_side} {$query_relative} {$query_condition}";

        foreach (array('IN', 'NOT IN') as $sign) {
            $this->parameter->operation = $sign;
            if ($sign === 'NOT IN') {
                $expected = "SELECT id FROM patient p WHERE id NOT IN ({$expected})";
            }
            $this->assertEquals($expected, $this->parameter->query());
        }
    }
}
