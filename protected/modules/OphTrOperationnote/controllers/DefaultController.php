<?php

class DefaultController extends EventTypeController
{
	public function actionIndex()
	{
		$this->render('index');
	}
}
