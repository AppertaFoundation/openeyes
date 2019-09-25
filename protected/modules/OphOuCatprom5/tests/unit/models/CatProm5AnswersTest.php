<?php
class CatProm5AnswersTest extends CDbTestCase {
  public $model;

  public $fixtures = array(
		'cat_prom5_answers' => 'CatProm5Answers',
  );

  public function dataProvider_Search() {
    return array(
      'Answer'=>array(array('answer'=>'No, never'), 1, array('answer1')),
			array(array('answer'=>'No'), 3, array('answer1','answer5', 'answer22')),
			array(array('answer'=>'Never'), 2, array('answer1', 'answer18')),
			array(array('answer'=>'Yes'), 6, array('answer2', 'answer3', 'answer4', 'answer23', 'answer24', 'answer25')),

//      array(array('answer'=>'Yes, some of the time'), 1, array('answer2')),
//      array(array('answer'=>'Yes, most of the time'), 1, array('answer3')),
//      array(array('answer'=>'Yes, all of the time'), 1, array('answer4')),
//      array(array('answer'=>'Not at all'), 1, array('answer5')),
//      array(array('answer'=>'Hardly at all'), 1, array('answer6')),
//      array(array('answer'=>'A little'), 1, array('answer7')),
//      array(array('answer'=>'A fair amount'), 1, array('answer8')),
//      array(array('answer'=>'A lot'), 1, array('answer9')),
//      array(array('answer'=>'An extremely large amount'), 1, array('answer10')),
//      array(array('answer'=>'Excellent'), 1, array('answer11')),
//      array(array('answer'=>'Very good'), 1, array('answer12')),
//      array(array('answer'=>'Quite good'), 1, array('answer13')),
//      array(array('answer'=>'Average'), 1, array('answer14')),
//      array(array('answer'=>'Quite poor'), 1, array('answer15')),
//      array(array('answer'=>'Very poor'), 1, array('answer16')),
//      array(array('answer'=>'Appalling'), 1, array('answer17')),
//      array(array('answer'=>'Never'), 1, array('answer18')),
//      array(array('answer'=>'Some of the time'), 1, array('answer19')),
//      array(array('answer'=>'Most of the time'), 1, array('answer20')),
//      array(array('answer'=>'All of the time'), 1, array('answer21')),
//      array(array('answer'=>'No difficulty'), 1, array('answer22')),
//      array(array('answer'=>'Yes, a little difficulty'), 1, array('answer23')),
//      array(array('answer'=>'Yes, some difficulty'), 1, array('answer24')),
//      array(array('answer'=>'Yes, a great deal of difficulty'), 1, array('answer25')),
//      array(array('answer'=>'I cannot read any more because of my eyesight'), 1, array('answer26')),
//      array(array('answer'=>'I gave all the answers and wrote them down myself'), 1, array('answer27')),
//      array(array('answer'=>'I gave all the answers and someone else wrote them down as I spoke'), 1, array('answer28')),
//      array(array('answer'=>'A friend or relative gave some of the answers on my behalf'), 1, array('answer29')),
    );
  }

  public function setUp() {
		$this->getFixtureManager()->basePath = Yii::getPathOfAlias('application.modules.OphOuCatprom5.tests.fixtures');
		parent::setUp();
    $this->model = new CatProm5Answers();
  }

  protected function tearDown()
  {
    parent::tearDown();
  }

  /**
   * @covers CatProm5Answers::model
   */
  public function testModel() {
    $this->assertEquals('CatProm5Answers', get_class(CatProm5Answers::model()), 'Class name should match model');
  }

  /**
   * @covers CatProm5Answers::tableName
   */
  public function testTableName() {
    $this->assertEquals('cat_prom5_answers', $this->model->tableName());
  }

  /**
   * @covers CatProm5Answers::rules
   */
  public function testRules() {

  }

  /**
   * @covers CatProm5Answers::attributeLabels
   */
  public function testAttributeLabels() {
    $expected = array(
      'id' => 'ID',
      'question_id' => 'Question',
      'answer' => 'Answer',
      'score' => 'Score',
    );

    $this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match');
  }

  /**
   * @dataProvider dataProvider_Search
   */
  public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys) {
    $catProm5Answer = new CatProm5Answers();
    $catProm5Answer->setAttributes($searchTerms);
    $results = $catProm5Answer->search();
    $data = $results->getData();

    $expectedResults = array();
    if (!empty($expectedKeys)) {
      foreach ($expectedKeys as $key) {
      	$expectedResults[] = $this->cat_prom5_answers($key);
      }
    }

    $this->assertEquals($numResults, $results->getItemCount(), 'Number of Results should match');
		$this->assertEquals($expectedResults, $data, 'Actual results should match');

  }
}