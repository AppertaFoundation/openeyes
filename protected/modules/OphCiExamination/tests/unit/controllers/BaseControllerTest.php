<?php
/**
 * (C) Apperta Foundation, 2020
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

namespace OEModule\OphCiExamination\tests\unit\controllers;

use OEModule\OphCiExamination\OphCiExaminationModule;

abstract class BaseControllerTest extends \OEDbTestCase
{
    use \MocksSession;
    use \WithTransactions;

    public function getController($cls, $methods = null)
    {
        if ($methods === null) {
            $methods = ['getControllerPrefix'];
        } else {
            $methods = array_merge(['getControllerPrefix'], $methods);
        }

        $controller = $this->getMockBuilder($cls)
                    ->setConstructorArgs(['default', new OphCiExaminationModule('OphCiExamination', null)])
                    ->setMethods($methods)
                    ->getMock();

        $controller->method('getControllerPrefix')
            ->willReturn('default');

        $controller->init();

        \Yii::app()->controller = $controller;
        return $controller;
    }

    protected function getMockAssetManager()
    {
        return $this->getMockBuilder(\AssetManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockRequest()
    {
        $request = $this->getMockBuilder(\CHttpRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->method('getPost')
            ->will($this->returnCallback(function($key, $default) {
                return $default; // always return the default
            }));

        return $request;
    }

    protected function mockSession()
    {
        $this->stubSession();
    }
}
