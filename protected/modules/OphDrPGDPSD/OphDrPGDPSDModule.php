<?php

class OphDrPGDPSDModule extends BaseEventTypeModule
{
    public function init()
    {
        $this->setImport(array(
            'OphDrPGDPSD.models.*',
            'OphDrPGDPSD.components.*',
            'OphDrPGDPSD.seeders.*',
            'OphDrPGDPSD.factories.*',
            'OphDrPGDPSD.views.*',
            'OphDrPGDPSD.widgets.*',
            'OphDrPGDPSD.controllers.*',
        ));
        parent::init();
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else {
            return false;
        }
    }
}
