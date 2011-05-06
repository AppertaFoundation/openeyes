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
		$found = false;
		if (!empty($_GET['name'])) {
			if (!empty($list)) {
				foreach ($list as $id => $procedure) {
					$match = "{$procedure['term']} - {$procedure['short_format']}";
					if ($match == $_GET['name']) {
						$data = $procedure;
						$data['id'] = $id;

						$found = true;
						$this->renderPartial('_ajaxProcedure', array('data' => $data), false, false);
						break;
					}
				}
			}

			// if not in the session, check in the db
			if (!$found) {
				$search = explode(' - ', $_GET['name']);
				$procedure = Yii::app()->db->createCommand()
					->select('*')
					->from('procedure')
					->where('term=:term AND short_format=:sf', 
						array(':term'=>$search[0], ':sf'=>$search[1]))
					->queryRow();
				if (!empty($procedure)) {
					$data = array(
						'term' => $procedure['term'],
						'short_format' => $procedure['short_format'],
						'duration' => $procedure['default_duration'],
					);
					$list[$procedure['id']] = $data;

					$data['id'] = $procedure['id'];

					Yii::app()->session['Procedures'] = $list;

					$this->renderPartial('_ajaxProcedure', array('data' => $data), false, false);
				}
			}
		}
	}
	
	public function actionSubsection()
	{
		if (!empty($_GET['service'])) {
			$service = $_GET['service'];
			$subsections = ServiceSubsection::model()->findAllByAttributes(
				array('service_id' => $service));
			
			$this->renderPartial('_subsectionOptions', array('subsections' => $subsections), false, false);
		}
	}
	
	public function actionList()
	{
		if (!empty($_GET['subsection'])) {
			$subsection = $_GET['subsection'];
			$procedures = Procedure::model()->findAllByAttributes(
				array('service_subsection_id' => $subsection));
			
			$this->renderPartial('_procedureOptions', array('procedures' => $procedures), false, false);
		}
	}
}
