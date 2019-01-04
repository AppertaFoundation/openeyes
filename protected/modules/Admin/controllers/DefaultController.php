<?php

class DefaultController extends BaseAdminController
{
    public function actionIndex()
    {
        $this->render('index');
    }
}
