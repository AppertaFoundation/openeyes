<?php

class DisorderController extends Controller
{
	public $layout='column2';

	protected function beforeAction(CAction $action)
	{
		// Sample code to be used when RBAC is fully implemented.
//		if (!Yii::app()->user->checkAccess('admin')) {
//			throw new CHttpException(403, 'You are not authorised to perform this action.');
//		}

		return parent::beforeAction($action);
	}

	/**
	 * Lists all disorders for a given search term.
	 */
	public function actionDisorders()
	{
		echo CJavaScript::jsonEncode(
			Disorder::getDisorderOptions($_GET['term'])
		);
	}
}
