<?php

class DefaultController extends BaseEventTypeController
{
	public $surgeons;

	public function actionCreate() {
		$criteria = new CDbCriteria;
		$criteria->compare('is_doctor',1);
		$criteria->order = 'first_name,last_name asc';

		$this->surgeons = User::model()->findAll($criteria);

		parent::actionCreate();
	}

	public function init() {
		$urlScript = Yii::app()->assetManager->publish(Yii::getPathOfAlias($this->getModule()->name).'/js/module.js', false, -1, true);
		Yii::app()->clientScript->registerScriptFile($urlScript, CClientScript::POS_HEAD);

		parent::init();
	}

	public function actionLoadElementByProcedure() {
		if (!$proc = Procedure::model()->findByPk((integer)@$_GET['procedure_id'])) {
			throw new SystemException('Procedure not found: '.@$_GET['procedure_id']);
		}

		$form = new BaseEventTypeCActiveForm;

		$criteria = new CDbCriteria;
		$criteria->compare('procedure_id',$proc->id);
		$criteria->order = 'display_order asc';

		foreach (ProcedureListOperationElement::model()->findAll($criteria) as $element) {
			$element = new $element->element_type->class_name;

			$this->renderPartial(
				'create' . '_' . get_class($element),
				array('element' => $element, 'data' => array(), 'form' => $form),
				false, true
			);
		}
	}

	public function actionGetElementsByProcedure() {
		if (!$proc = Procedure::model()->findByPk((integer)@$_GET['procedure_id'])) {
			throw new SystemException('Procedure not found: '.@$_GET['procedure_id']);
		}

		$criteria = new CDbCriteria;
		$criteria->compare('procedure_id',$proc->id);
		$criteria->order = 'display_order asc';

		$elements = array();

		foreach (ProcedureListOperationElement::model()->findAll($criteria) as $element) {
			$elements[] = $element->element_type->class_name;
		}

		die(json_encode($elements));
	}
}
