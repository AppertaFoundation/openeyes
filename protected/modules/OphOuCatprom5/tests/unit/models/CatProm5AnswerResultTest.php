<?php
class CatProm5AnswerResultTest extends ActiveRecordTestCase {
    public $model;
    public $fixtures = array(
        'answerRecords' => 'CatProm5AnswerResult',
    );

    public function getModel()
    {
        return $this->model;
    }

    public function dataProvider_Search()
    {
        return array(
        array(array('question_id'=>1), 2, array('cat_prom5_answer_item1', 'cat_prom5_answer_item7')),
        array(array('question_id'=>2), 2, array('cat_prom5_answer_item2', 'cat_prom5_answer_item8')),
        array(array('question_id'=>3), 2, array('cat_prom5_answer_item3', 'cat_prom5_answer_item9')),
        array(array('question_id'=>4), 2, array('cat_prom5_answer_item4', 'cat_prom5_answer_item10')),
        array(array('question_id'=>5), 2, array('cat_prom5_answer_item5', 'cat_prom5_answer_item11')),
        array(array('question_id'=>6), 2, array('cat_prom5_answer_item6', 'cat_prom5_answer_item12')),
        array(array('element_id'=>1), 6, array('cat_prom5_answer_item1', 'cat_prom5_answer_item2', 'cat_prom5_answer_item3',
        'cat_prom5_answer_item4', 'cat_prom5_answer_item5', 'cat_prom5_answer_item6')),
        array(array('element_id'=>2), 6, array('cat_prom5_answer_item7', 'cat_prom5_answer_item8', 'cat_prom5_answer_item9',
        'cat_prom5_answer_item10', 'cat_prom5_answer_item11', 'cat_prom5_answer_item12')),
        array(array('answer_id'=>3), 1, array('cat_prom5_answer_item1')),
        array(array('answer_id'=>8), 1, array('cat_prom5_answer_item2')),
        array(array('answer_id'=>15), 1, array('cat_prom5_answer_item3')),
        array(array('answer_id'=>20), 1, array('cat_prom5_answer_item4')),
        array(array('answer_id'=>24), 1, array('cat_prom5_answer_item5')),
        array(array('answer_id'=>29), 1, array('cat_prom5_answer_item6')),
        array(array('answer_id'=>2), 1, array('cat_prom5_answer_item7')),
        array(array('answer_id'=>6), 1, array('cat_prom5_answer_item8')),
        array(array('answer_id'=>13), 1, array('cat_prom5_answer_item9')),
        array(array('answer_id'=>19), 1, array('cat_prom5_answer_item10')),
        array(array('answer_id'=>23), 1, array('cat_prom5_answer_item11')),
        array(array('answer_id'=>28), 1, array('cat_prom5_answer_item12')),
        );
    }

    public static function setUpBeforeClass(): void
    {
        Yii::app()->getModule('OphOuCatprom5');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new CatProm5AnswerResult();
    }

  /**
   * @covers CatProm5AnswerResult::model
   */
    public function testModel()
    {
        $this->assertEquals('CatProm5AnswerResult', get_class(CatProm5AnswerResult::model()), 'Class name should match model');
    }

  /**
   * @covers CatProm5AnswerResult::tableName
   */
    public function testTableName()
    {
        $this->assertEquals('cat_prom5_answer_results', $this->model->tableName());
    }

    /**
     * @covers CatProm5AnswerResult::rules
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->answerRecords('cat_prom5_answer_item1')->validate());
        $this->assertEmpty($this->answerRecords('cat_prom5_answer_item1')->errors);
    }

  /**
   * @covers CatProm5AnswerResult::attributeLabels
   */
    public function testAttributeLabels()
    {
        $expected = array(
        'id' => 'ID',
        'element_id' => 'Event',
        'answer_id' => 'Answer',
        'question_id' => 'Question',
        );
        $this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match');
    }

  /**
   * @dataProvider dataProvider_Search
   */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $catProm5AnswerResult = new CatProm5AnswerResult();
        $catProm5AnswerResult->setAttributes($searchTerms);
        $results = $catProm5AnswerResult->search();
        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->answerRecords($key);
            }
        }

        $this->assertEquals($numResults, $results->getItemCount(), 'Number of Results should match');
        $this->assertEquals($expectedResults, $data, 'Actual results should match');
    }
}
