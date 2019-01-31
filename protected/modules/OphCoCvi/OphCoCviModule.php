<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


namespace OEModule\OphCoCvi;

/**
 * This is the module class for $this->moduleSuffix
 *
 * The followings are the available columns in table:
 * @property string $moduleShortSuffix
 */
class OphCoCviModule extends \BaseEventTypeModule
{
    public $controllerNamespace = '\OEModule\OphCoCvi\controllers';

    public function init()
    {
        $this->setImport(array(
            'application.components.odtTemplateManager.*',
            'OphCoCvi.models.*',
            'OphCoCvi.views.*',
            'OphCoCvi.components.*',
            'OphCoCvi.controllers.*',
        ));

        $this->setModules(array('CviAdmin'));

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
