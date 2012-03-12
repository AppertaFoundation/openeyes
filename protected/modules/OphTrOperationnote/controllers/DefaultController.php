<?php

class DefaultController extends BaseEventTypeController
{
	public function actionIndex()
	{
		$this->render('index');
	}
}
