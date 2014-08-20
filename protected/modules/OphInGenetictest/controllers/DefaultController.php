<?php

class DefaultController extends BaseEventTypeController
{

	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('Create', 'Update', 'View' , 'Print'),
				'roles' => array('OprnEditGeneticTest'),
			),
			array('allow',
				'actions' => array('View' , 'Print'),
				'roles' => array('OprnViewGeneticTest'),
			),
		);
	}

	public function actionCreate()
	{
		parent::actionCreate();
	}

	public function actionUpdate($id)
	{
		parent::actionUpdate($id);
	}

	public function actionView($id)
	{
		parent::actionView($id);
	}

	public function actionPrint($id)
	{
		parent::actionPrint($id);
	}

	public function isRequiredInUI(BaseEventTypeElement $element)
	{
		return true;
	}
}
