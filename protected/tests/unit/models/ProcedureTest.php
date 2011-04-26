<?php

class ProcedureTest extends CDbTestCase
{
	public $model;
	
	public $fixtures = array(
		'procedures' => 'Procedure',
	);
	
	public function setUp()
	{
		parent::setUp();
		$this->model = new Procedure;
	}
	
	public function dataProvider_ProcedureSearch()
	{
		$procedure1 = array(
			1 => array(
				'term' => 'Foobar Procedure',
				'short_format' => 'FUB',
				'duration' => 60,
			)
		);
		$procedure2 = array(
			2 => array(
				'term' => 'Test Procedure',
				'short_format' => 'TP',
				'duration' => 20,
			)
		);
		
		return array(
			array('Foo', array('Foobar Procedure - FUB'), $procedure1),
			array('Foobar', array('Foobar Procedure - FUB'), $procedure1),
			array('Fo', array('Foobar Procedure - FUB'), $procedure1),
			array('Test', array('Test Procedure - TP'), $procedure2),
			array('Test Pro', array('Test Procedure - TP'), $procedure2),
			array('Te', array('Test Procedure - TP'), $procedure2),
		);
	}
	
	public function testModel()
	{
		$this->assertEquals('Procedure', get_class(Procedure::model()));
	}
	
	public function testTableName()
	{
		$this->assertEquals('procedure', $this->model->tableName());
	}
	
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'term' => 'Term',
			'short_format' => 'Short Format',
			'default_duration' => 'Default Duration',
			'service_subsection_id' => 'Service Subsection',
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels());
	}
	
	/**
	 * @dataProvider dataProvider_ProcedureSearch
	 */
	public function testGetList_ValidTerms_ReturnsValidResults($term, $data, $session)
	{
		Yii::app()->session['Procedures'] = null;
		$this->assertNull(Yii::app()->session['Procedures']);
		
		$results = Procedure::getList($term);
		
		$this->assertEquals($data, $results);
		$this->assertEquals($session, Yii::app()->session['Procedures']);
	}
	
	public function testGetList_CalledTwice_AppendsSessionData()
	{
		Yii::app()->session['Procedures'] = null;
		$this->assertNull(Yii::app()->session['Procedures']);
		
		$expected = array(
			1 => array(
				'term' => 'Foobar Procedure',
				'short_format' => 'FUB',
				'duration' => 60,
			),
			2 => array(
				'term' => 'Test Procedure',
				'short_format' => 'TP',
				'duration' => 20,
			)
		);
		
		$results = Procedure::getList('Fo');
		$this->assertEquals(array_slice($expected, 0, 1, true), Yii::app()->session['Procedures']);
		$results = Procedure::getList('Te');
		$this->assertEquals($expected, Yii::app()->session['Procedures']);
	}
	
	public function testGetList_InvalidTerm_ReturnsEmptyResults_SessionDataUnchanged()
	{
		Yii::app()->session['Procedures'] = null;
		$this->assertNull(Yii::app()->session['Procedures']);
		
		$expected = array(
			1 => array(
				'term' => 'Foobar Procedure',
				'short_format' => 'FUB',
				'duration' => 60,
			)
		);
		
		$results = Procedure::getList('Fo');
		$this->assertEquals($expected, Yii::app()->session['Procedures']);
		
		$results = Procedure::getList('Bar');
		$this->assertEquals(array(), $results);
		$this->assertEquals($expected, Yii::app()->session['Procedures']);
	}
}