<?php

class CatProm5EventResultTest extends ActiveRecordTestCase
{
    public $model;
    public $fixtures = array(
        'elements' => 'CatProm5EventResult',
    );

    public function getModel()
    {
        return $this->model;
    }

    public function dataProvider_Search()
    {
        return array(
            array(array('total_raw_score'=>13), 1, array('cat_prom5_event1')),
            array(array('total_raw_score'=>6), 1, array('cat_prom5_event2')),
            array(array('total_raw_score'=>12), 0, array()),

            array(array('total_rasch_measure'=>1.22), 1, array('cat_prom5_event1')),
            array(array('total_rasch_measure'=>-2.29), 1, array('cat_prom5_event2')),
            array(array('total_rasch_measure'=>1.01), 0, array()),

            array(array('event_id' => 3686612), 1, array('cat_prom5_event1')),
            array(array('event_id' => 3686613), 1, array('cat_prom5_event2')),
            array(array('event_id' => 3124456), 0, array()),
        );
    }

    public function dataProvider_ScoreMap()
    {
        return [
            [0, -9.18], [1, -6.80], [2, -4.92], [3, -4.03], [4, -3.37],
            [5, -2.81], [6, -2.29], [7, -1.80], [8, -1.31], [9, -0.82],
            [10, -0.32], [11, 0.18], [12, 0.69], [13, 1.22],[14, 1.76],
            [15, 2.33], [16, 2.93], [17, 3.56], [18, 4.23],[19, 4.95],
            [20, 6.01], [21, 7.45]
        ];
    }

    public static function setUpBeforeClass(): void
    {
        Yii::app()->getModule('OphOuCatprom5');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new CatProm5EventResult();
    }

    /**
    * @covers CatProm5EventResult::model
    */
    public function testModel()
    {
        $this->assertEquals('CatProm5EventResult', get_class(CatProm5EventResult::model()), 'Class name should match model');
    }

    public function testTableName()
    {
        $this->assertEquals('cat_prom5_event_result', $this->model->tableName());
    }

    /**
     * @covers CatProm5EventResult::rules
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->elements('cat_prom5_event1')->validate());
        $this->assertEmpty($this->elements('cat_prom5_event1')->errors);
        $this->assertTrue($this->elements('cat_prom5_event2')->validate());
        $this->assertEmpty($this->elements('cat_prom5_event2')->errors);
    }

    /**
    * @covers CatProm5EventResult::attributeLabels
    */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'total_raw_score' => 'Total Raw Score',
            'total_rasch_measure' => 'Total Rasch Score',
            'event_id' => 'Event',
        );

        $this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match');
    }

    /**
    * @dataProvider dataProvider_Search
    */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $catProm5EventResult = new CatProm5EventResult();
        $catProm5EventResult->setAttributes($searchTerms);
        $results = $catProm5EventResult->search();
        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->elements($key);
            }
        }

        $this->assertEquals($numResults, $results->getItemCount(), 'Number of Results should match');
        $this->assertEquals($expectedResults, $data, 'Actual results should match');
    }

    /**
    * @covers CatProm5EventResult::rowScoreToRaschMeasure
    * @dataProvider dataProvider_ScoreMap
    */
    public function testRowScoreToRaschMeasure($rawScores, $expectedScores)
    {
        $catProm5EventResult = new CatProm5EventResult();
        $result = $catProm5EventResult->rowScoreToRaschMeasure($rawScores);

        $this->assertEquals($result, $expectedScores, 'rasch_measure does not match raw score');
    }

    /**
    * @covers CatProm5EventResult::setDefaultOptions
    */
    public function testSetDefaultOptions()
    {
        $catProm5EventResult = new CatProm5EventResult();
        $expectedEventResult = new CatProm5EventResult();
        $answerResult1 = new CatProm5AnswerResult();
        $answerResult1->question_id = 1;
        $answerResult2 = new CatProm5AnswerResult();
        $answerResult2->question_id = 2;
        $answerResult3 = new CatProm5AnswerResult();
        $answerResult3->question_id = 3;
        $answerResult4 = new CatProm5AnswerResult();
        $answerResult4->question_id = 4;
        $answerResult5 = new CatProm5AnswerResult();
        $answerResult5->question_id = 5;
        $answerResult6 = new CatProm5AnswerResult();
        $answerResult6->question_id = 6;

        $expectedEventResult->catProm5AnswerResults = array(
            $answerResult1, $answerResult2, $answerResult3,
            $answerResult4, $answerResult5, $answerResult6
        );
        $catProm5EventResult->setDefaultOptions();

        $this->assertEquals($catProm5EventResult, $expectedEventResult, 'SetDefaultOptions now working correctly');
    }
}
