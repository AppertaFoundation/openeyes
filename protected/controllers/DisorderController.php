<?php

class DisorderController extends Controller
{
	public $layout='column2';
	
	public function filters()
	{
		return array('accessControl');
	}
	
	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@')
			),
			// non-logged in can't view anything
			array('deny', 
				'users'=>array('?')
			),
		);
	}

	protected function beforeAction($action)
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
	public function actionAutocomplete()
	{
		echo CJavaScript::jsonEncode(Disorder::getList($_GET['term']));
	}

	public function actionDetails()
	{
		if (!isset($_REQUEST['name'])) {
			echo CJavaScript::jsonEncode(false);
			return;
		}

		$disorder = Disorder::model()->find('fully_specified_name = ? OR term = ?', array($_REQUEST['name'], $_REQUEST['name']));

		if (!isset($disorder)) {
			echo CJavaScript::jsonEncode(false);
			return;
		}

		echo $disorder->id;
	}
}
