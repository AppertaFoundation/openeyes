<?php

class OphOuCatprom5Module extends BaseEventTypeModule
{
    public $controllerNamespace = '\OEModule\OphOuCatprom5\controllers';
    public function init()
    {
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
