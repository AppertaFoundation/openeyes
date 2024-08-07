<?php

namespace OEModule\CypressHelper;

class CypressHelperModule  extends \BaseModule
{
    public $controllerNamespace = '\OEModule\CypressHelper\controllers';

    public function init()
    {
        // import the module-level components
        // $this->setImport(['CypressHelper.components.*']);

        $this->setImport(array(
            'CypressHelper.seeders.*',
            'CypressHelper.resources.*',
        ));

        parent::init();
    }
}
