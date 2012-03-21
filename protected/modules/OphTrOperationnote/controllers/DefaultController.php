<?php

class DefaultController extends BaseEventTypeController
{
	public $surgeons;
	public $assistants;

	public function actionCreate() {
		$criteria = new CDbCriteria;
		$criteria->compare('title','Dr');
		$criteria->order = 'first_name,last_name asc';

		$this->surgeons = User::model()->findAll($criteria);

		$criteria = new CDbCriteria;
		$criteria->compare('title','Dr',false,'<>');
		$criteria->order = 'first_name,last_name asc';

		$this->assistants = User::model()->findAll($criteria);

		parent::actionCreate();
	}
}
