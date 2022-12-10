<?php

class CatProm5QuestionsTest extends ActiveRecordTestCase
{
    public $model;
    public $fixtures = array(
        'questions' => 'CatProm5Questions'
    );

    public function getModel()
    {
        return $this->model;
    }

    public function dataProvider_Search()
    {
        return array(
        array(array('id'=>1, 'display_order'=>10), 1, array('question1')),
        array(array('id'=>2, 'display_order'=>20), 1, array('question2')),
        array(array('id'=>3, 'display_order'=>30), 1, array('question3')),
        array(array('display_order'=>'40'), 1, array('question4')),
        array(array('display_order'=>'50'), 1, array('question5')),
        array(array('display_order'=>'60'), 1, array('question6')),
        array(array('mandatory'=>1), 6, array('question1','question2','question3','question4','question5','question6')),

        array(array('question'=>"In the past month"), 5, array('question1','question2','question3', 'question4', 'question5')),
        array(array('question'=>'In the past month, how much has your eyesight interfered with your life in general?'), 1, array('question2')),
        array(array('question'=>'How would you describe your vision overall in the past month – with both eyes open, wearing glasses or contact lenses if you usually do?'), 1, array('question3')),
        array(array('question'=>'In the past month, how often has your eyesight prevented you from doing the things you would like to do?'), 1, array('question4')),
        array(array('question'=>'In the past month, have you had difficulty reading normal print in books or newspapers because of trouble with your eyesight?'), 1, array('question5')),
        array(array('question'=>'Please tell us who actually gave the answers to the questions and who wrote them down'), 1, array('question6')),
        array(array('question'=>'Non existent question'), 0, array())
        );
    }

    public static function setUpBeforeClass(): void
    {
        Yii::app()->getModule('OphOuCatprom5');
    }

    public function setUp(): void
    {
        $this->getFixtureManager()->basePath = Yii::getPathOfAlias('application.modules.OphOuCatprom5.tests.fixtures');
        parent::setUp();
        $this->model = new CatProm5Questions();
    }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
    public function tearDown(): void
    {
        $this->getFixtureManager()->basePath = Yii::getPathOfAlias('application.modules.OphOuCatprom5.tests.fixtures');
        parent::tearDown();
    }


  /**
   * @covers CatProm5Questions::model
   */
    public function testModel()
    {
        $this->assertEquals('CatProm5Questions', get_class(CatProm5Questions::model()), 'Class name should match model');
    }

  /**
   * @covers CatProm5Questions::tableName
   */
    public function testTableName()
    {
        $this->assertEquals('cat_prom5_questions', $this->model->tableName());
    }

    /**
     * @covers CatProm5Questions::rules
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->questions('question1')->validate());
        $this->assertEmpty($this->questions('question1')->errors);
    }

  /**
   * @covers CatProm5Questions::attributeLabels
   */
    public function testAttributeLabels()
    {
        $expected = array(
        'id' => 'ID',
        'question' => 'Question',
        'mandatory' => 'Mandatory',
        'display_order' => 'Display Order',
        );

        $this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match');
    }

  /**
   * @dataProvider dataProvider_Search
   */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $catProm5Question = new CatProm5Questions();
        $catProm5Question->setAttributes($searchTerms);
        $results = $catProm5Question->search();
        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->questions($key);
            }
        }

        $this->assertEquals($numResults, $results->getItemCount(), 'Number of Results should match.');
        $this->assertEquals($expectedResults, $data, 'Actual results should match');
    }
}
