<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphGeneric;

use OEModule\OphGeneric\modules\OphGenericAdmin\OphGenericAdminModule;

/**
 * This is the module class for OphGeneric
 *
 * The followings are the available columns in table:
 * @property string $moduleShortSuffix
 */
class OphGenericModule extends \BaseEventTypeModule
{
    // this property is really only relevant to gii auto-generation, specifically
    // for updates to the module through gii
    public $moduleShortSuffix;

    public $controllerNamespace = '\OEModule\OphGeneric\controllers';

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'OphGeneric.models.*',
            'OphGeneric.views.*',
            'OphGeneric.components.*',
            'OphGeneric.controllers.*',
        ));

        $this->moduleShortSuffix = "OphGeneric";

        $this->setModules(['OphGenericAdmin' => ['class' => OphGenericAdminModule::class]]);

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
