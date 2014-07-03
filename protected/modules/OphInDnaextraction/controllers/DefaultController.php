<?php

class DefaultController extends BaseEventTypeController
{
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
	protected function validatePOSTElements($elements)
	{
		$errors = parent::validatePOSTElements($elements);

		foreach ($elements as $element) {
			if ($element->getElementType()->class_name == 'Element_OphInDnaextraction_DnaTests') {
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
		}

		return $errors;
	}

	/*
	 * Process related items on event creation
	 */
	public function createElements($elements, $data, $firm, $patientId, $userId, $eventTypeId)
	{
		if ($id = parent::createElements($elements, $data, $firm, $patientId, $userId, $eventTypeId)) {
			$this->storePOSTManyToMany($elements);
		}

		return $id;
	}

	/*
	 * Process related items on event update
	 */
	public function updateElements($elements, $data, $event)
	{
		if (parent::updateElements($elements, $data, $event)) {
			// update has been successful, now need to deal with many to many changes
			$this->storePOSTManyToMany($elements);
		}
		return true;
	}

	/*
	 * Store related items
	 */
	protected function storePOSTManyToMany($elements)
	{
		foreach ($elements as $element) {
			if (get_class($element) == 'Element_OphInDnaextraction_DnaTests') {
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
		}
	}

	public function isRequiredInUI(BaseEventTypeElement $element)
	{
		return true;
	}
}
