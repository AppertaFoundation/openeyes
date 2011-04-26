<?php

class ProcedureController extends Controller
{
	public $layout='column2';

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
		echo CJavaScript::jsonEncode(Procedure::getList($_GET['term']));
	}
	
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
}
