<?php

class OperationProcedureAssignmentTest extends CDbTestCase
{
	public $model;
	
	public $fixtures = array(
		'procedures' => 'Procedure',
		'operations' => 'ElementOperation',
		'operationProcedures' => 'OperationProcedureAssignment'
	);
	
	public function setUp()
	{
		parent::setUp();
		$this->model = new OperationProcedureAssignment;
	}
	
	public function testModel()
	{
		$this->assertEquals('OperationProcedureAssignment', get_class(OperationProcedureAssignment::model()));
	}
	
	public function testTableName()
	{
		$this->assertEquals('operation_procedure_assignment', $this->model->tableName());
	}
	
	public function testAttributeLabels()
	{
		$expected = array(
			'operation_id' => 'Operation',
			'procedure_id' => 'Procedure',
			'display_order' => 'Display Order',
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels());
	}
	
	public function testPrimaryKey()
	{
		$expected = array('operation_id', 'procedure_id');
		$this->assertEquals($expected, $this->model->primaryKey());
	}
	
	public function testDefaultScope()
	{
		$expected = array('order'=>'display_order ASC');
		$this->assertEquals($expected, $this->model->defaultScope());
	}
}