<?php

class DefaultController extends BaseEventTypeController
{
 
    public function volumeRemaining($event_id)
    {
        $volume_remaining = 0;
        if ($api = Yii::app()->moduleAPI->get('OphInDnaextraction')) {
            $volume_remaining = $api->volumeRemaining($event_id);
        }

        return $volume_remaining;
    }

    public function checkCreateAccess()
    {
        return $this->checkAccess('OprnEditDnaSample')||;
    }

    public function checkUpdateAccess()
    {
        return $this->checkAccess('OprnEditDnaSample');
    }

    public function checkViewAccess()
    {
        return $this->checkAccess('OprnEditDnaSample') || $this->checkAccess('OprnViewDnaSample')
    }

    public function checkPrintAccess()
    {
        return $this->checkAccess('OprnEditDnaSample') || $this->checkAccess('OprnViewDnaSample');
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

    public function isRequiredInUI(BaseEventTypeElement $element)
    {
        return true;
    }
}
