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
        return $this->checkAccess('OprnEditDnaSample');
    }

    public function checkUpdateAccess()
    {
        return $this->checkAccess('OprnEditDnaSample');
    }

    public function checkViewAccess()
    {
        return $this->checkAccess('OprnEditDnaSample') || $this->checkAccess('OprnViewDnaSample');
    }

    public function checkPrintAccess()
    {
        return $this->checkAccess('OprnEditDnaSample') || $this->checkAccess('OprnViewDnaSample');
    }

    private function _registerDnaTestFormJs()
    {
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.OphInDnaextraction.assets'), true);
        Yii::app()->clientScript->registerScriptFile($assetPath.'/js/dna_tests_view.js');
    }

    public function actionCreate()
    {
        $this->_registerDnaTestFormJs();
        parent::actionCreate();
    }

    public function actionUpdate($id)
    {
        $this->_registerDnaTestFormJs();
        parent::actionUpdate($id);
    }

    public function actionView($id)
    {
        $this->_registerDnaTestFormJs();
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
