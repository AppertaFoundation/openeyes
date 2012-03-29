<?php

class DefaultController extends BaseEventTypeController
{
	public $surgeons;
	public $selected_procedures;
	public $selected_eye;

	public function actionCreate() {
		$criteria = new CDbCriteria;
		$criteria->compare('is_doctor',1);
		$criteria->order = 'first_name,last_name asc';

		$this->surgeons = User::model()->findAll($criteria);

		// Get the procedure list and eye from the most recent booking for the episode of the current user's subspecialty
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new SystemException('Patient not found: '.@$_GET['patient_id']);
		}

		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			if ($booking = $episode->getMostRecentBooking()) {
				$this->selected_procedures = array();

				foreach ($booking->elementOperation->procedures as $procedure) {
					$this->selected_procedures[] = $procedure;
				}

				$this->selected_eye = $booking->elementOperation->eye_id;
			}
		}

		parent::actionCreate();
	}

	public function actionUpdate($id) {
		$criteria = new CDbCriteria;
		$criteria->compare('is_doctor',1);
		$criteria->order = 'first_name,last_name asc';

		$this->surgeons = User::model()->findAll($criteria);

		parent::actionUpdate($id);
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

	public function actionGetElementsToDelete() {
		if (!$proc = Procedure::model()->findByPk((integer)@$_POST['procedure_id'])) {
			throw new SystemException('Procedure not found: '.@$_POST['procedure_id']);
		}

		$criteria = new CDbCriteria;
		$criteria->compare('procedure_id',$proc->id);
		$criteria->order = 'display_order asc';

		if (@$_POST['remaining_procedures']) {
			$procedures = explode(',',$_POST['remaining_procedures']);
		} else {
			$procedures = array();
		}

		$elements = array();

		foreach (ProcedureListOperationElement::model()->findAll($criteria) as $element) {
			if (empty($procedures) || !ProcedureListOperationElement::model()->find('procedure_id in ('.implode(',',$procedures).') and element_type_id = '.$element->element_type->id)) {
				$elements[] = $element->element_type->class_name;
			}
		}

		die(json_encode($elements));
	}
}
