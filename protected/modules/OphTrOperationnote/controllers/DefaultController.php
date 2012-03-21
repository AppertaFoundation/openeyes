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
}
