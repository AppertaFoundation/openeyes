<?php

class DefaultController extends BaseEventTypeController
{

	static protected $action_types = array(
		'addTransaction' => self::ACTION_TYPE_FORM,
	);

	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('Create', 'Update', 'View' , 'Print' ,'AddTransaction'),
				'roles' => array('OprnEditDNAExtraction'),
			),
			array('allow',
				'actions' => array('View' , 'Print'),
				'roles' => array('OprnViewDNAExtraction'),
			),
		);
	}

	public function volumeRemaining($event_id)
	{
		$api = Yii::app()->moduleAPI->get('OphInDnaextraction');
		return $api->volumeRemaining($event_id);
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

	public function actionAddTransaction()
	{
		if (!isset($_GET['i'])) {
			throw new Exception("Row number not set");
		}

		$transaction = new OphInDnaextraction_DnaTests_Transaction;
		$transaction->setDefaultOptions();

		$this->renderPartial('_dna_test', array(
			'i' => $_GET['i'],
			'transaction' => $transaction,
			'disabled' => false,
		));
	}

	public function getFormTransactions()
	{
		$transactions = array();

		if (!empty($_POST['date'])) {
			foreach ($_POST['date'] as $i => $date) {
				if ($_POST['transactionID'][$i]) {
					$_transaction = OphInDnaextraction_DnaTests_Transaction::model()->findByPk($_POST['transactionID'][$i]);
				} else {
					$_transaction = new OphInDnaextraction_DnaTests_Transaction;
				}
				$_transaction->date = date('Y-m-d',strtotime($date));
				$_transaction->investigator_id = $_POST['investigator_id'][$i];
				$_transaction->study_id = $_POST['study_id'][$i];
				$_transaction->volume = $_POST['volume'][$i];

				$transactions[] = $_transaction;
			} 
		}

		return $transactions;
	}

	/*
	 * Validate element related models
	 */

	protected function setAndValidateElementsFromData($data)
	{
		$errors = parent::setAndValidateElementsFromData($data);

		if (!empty($data['date'])) {
				foreach ($this->getFormTransactions() as $transaction) {
					if (!$transaction->validate()) {
						foreach ($transaction->getErrors() as $errormsgs) {
							foreach ($errormsgs as $error) {
								$errors['Tests'][] = $error;
							}
						}
					}
				}
		}

		return $errors;
	}

	protected function saveComplexAttributes_Element_OphInDnaextraction_DnaTests($element, $data, $index)
	{
		$item_ids = array();

		foreach ($this->getFormTransactions() as $transaction) {
			$transaction->element_id = $element->id;

			if (!$transaction->save()) {
				throw new Exception("Unable to save transaction: ".print_r($transaction->getErrors(),true));
			}

			$item_ids[] = $transaction->id;
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition('element_id = :element_id');
		$criteria->addNotInCondition('id',$item_ids);
		$criteria->params[':element_id'] = $element->id;

		foreach (OphInDnaextraction_DnaTests_Transaction::model()->findAll($criteria) as $transaction) {
			if (!$transaction->delete()) {
				throw new Exception("Unable to delete transaction: ".print_r($transaction->getErrors(),true));
			}
		}
	}

	public function isRequiredInUI(BaseEventTypeElement $element)
	{
		return true;
	}
}
