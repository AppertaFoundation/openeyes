<?php

namespace OEModule\BreakGlass;

class BreakGlassModule extends \BaseEventTypeModule
    {
    // this property is really only relevant to gii auto-generation, specifically
    // for updates to the module through gii
    public $moduleShortSuffix;

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'BreakGlass.controllers.*',
            'BreakGlass.models.*',
            'BreakGlass.views.*',
        ));

        $this->moduleShortSuffix = "Break Glass";

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
