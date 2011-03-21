<?php

class DefaultController extends Controller
{
	public $layout = 'main';

	protected function beforeAction(CAction $action)
	{
		// Sample code to be used when RBAC is fully implemented.
		if (!Yii::app()->user->checkAccess('admin')) {
			throw new CHttpException(403, 'You are not authorised to perform this action.');
		}

		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		$this->render('index');
	}
}