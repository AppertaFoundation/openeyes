<?php

class DisorderController extends Controller
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
		echo CJavaScript::jsonEncode(Disorder::getList($_GET['term']));
	}
	
	public function actionDetails()
	{
		$list = Yii::app()->session['Disorders'];
		$found = false;
		if (!empty($_GET['name'])) {
			if (!empty($list)) {
				foreach ($list as $id => $disorder) {
					$match = "{$disorder['term']} - {$disorder['fully_specified_name']}";
					if ($match == $_GET['name']) {
						$data = $disorder;
						$data['id'] = $id;

						$found = true;
						$this->renderPartial('_ajaxDisorder', array('data' => $data), false, false);
						break;
					}
				}
			}

			// if not in the session, check in the db
			if (!$found) {
				$search = explode(' - ', $_GET['name']);
				$disorder = Yii::app()->db->createCommand()
					->select('*')
					->from('disorder')
					->where('term=:term AND fully_specified_name=:fqn', 
						array(':term'=>$search[0], ':fqn'=>$search[1]))
					->queryRow();
				if (!empty($disorder)) {
					$data = array(
						'term' => $disorder['term'],
						'fully_specified_name' => $disorder['fully_specified_name']
					);
					$list[$disorder['id']] = $data;

					$data['id'] = $disorder['id'];

					Yii::app()->session['Disorders'] = $list;

					$this->renderPartial('_ajaxDisorder', array('data' => $data), false, false);
				}
			}
		}
	}
}
