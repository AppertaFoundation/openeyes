<?php
class ProcedureControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'procedures' => 'Procedure',
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new ProcedureController('ProcedureController');
		parent::setUp();
	}
	
	public function testActionDetails_EmptySession_RendersNothing()
	{
		Yii::app()->session['Procedures'] = null;
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		$mockController->actionDetails();
	}
	
	public function testActionDetails_ValidSessionData_NonMatchingTerm_RendersNothing()
	{
		$session = array();
		foreach ($this->procedures as $procedure) {
			$session[$procedure['id']] = array(
				'term' => $procedure['term'],
				'short_format' => $procedure['short_format'],
				'duration' => $procedure['default_duration'],
			);
		}
		Yii::app()->session['Procedures'] = $session;
		
		$_GET['name'] = 'Bar Procedure';
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		$mockController->actionDetails();
	}
	
	public function testActionDetails_ValidSessionData_MatchingTerm_RendersAjaxPartial()
	{
		$session = array();
		foreach ($this->procedures as $procedure) {
			$session[$procedure['id']] = array(
				'term' => $procedure['term'],
				'short_format' => $procedure['short_format'],
				'duration' => $procedure['default_duration'],
			);
		}
		Yii::app()->session['Procedures'] = $session;
		
		$procedure = $this->procedures['procedure1'];
		$_GET['name'] = "{$procedure['term']} - {$procedure['short_format']}";
		
		$data = array(
			'term' => $procedure['term'],
			'short_format' => $procedure['short_format'],
			'duration' => $procedure['default_duration'],
			'id' => $procedure['id'],
		);
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('_ajaxProcedure', array('data' => $data), false, true);
		$mockController->actionDetails();
	}
	
	/*
	 * 
	
	public function actionDetails()
	{
		$list = Yii::app()->session['Procedures'];
		if (!empty($list)) {
			foreach ($list as $id => $procedure) {
				$match = "{$procedure['term']} - {$procedure['short_format']}";
				if ($match == $_GET['name']) {
					$data = $procedure;
					$data['id'] = $id;
					
					$this->renderPartial('_ajaxProcedure', array('data' => $data), false, true);
					break;
				}
			}
		}
	}
	 */
}