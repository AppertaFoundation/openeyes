<?php

class DefaultController extends BaseEventTypeController
{
    protected $show_element_sidebar = false;

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
}
