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

use OEModule\OphCiExamination\controllers\DefaultController;

abstract class BaseDefaultControllerTest extends BaseControllerTest
{
    use \InteractsWithEventTypeElements;

    public function setUp()
    {
        parent::setUp();
        // stub out components that will cause failures
        \Yii::app()->setComponent('assetManager', $this->getMockAssetManager());
        \Yii::app()->setComponent('request', $this->getMockRequest());
        $this->mockSession();
        $_POST = $_GET = $_REQUEST = []; // reset the common globals
    }

    public function getDefaultController($methods = null)
    {
        $base_methods_to_mock = ['addToUnbookedWorklist'];
        if ($methods === null) {
            $methods = [];
        }
        return $this->getController(DefaultController::class, array_merge($base_methods_to_mock, $methods));
    }

    protected function performCreateRequestForRandomPatient()
    {
        $patient = $this->getPatientWithEpisodes();
        $episode = $patient->episodes[0];

        // enables controller to know what episode the event will be created in.
        $this->mockCurrentContext($episode->firm);

        // set up the request data for submitting values
        $_REQUEST['patient_id'] = $patient->id;

        return $this->performCreateRequestWithController();
    }

    protected function updateElementWithDataWithController($element, $data)
    {
        $model_name = \CHtml::modelName($element);
        $_POST[$model_name] = $data;

        $this->performUpdateRequestForEvent($element->event);
    }

    protected function performUpdateRequestForEvent(\Event $event)
    {
        $this->mockCurrentContext($event->episode->firm);
        $_GET['id'] = $event->id;

        $redirectedEventId = $this->performUpdateRequestWithController();

        $this->assertEquals($event->id, $redirectedEventId);
    }

    /**
     * Wrapper to mask the complexity of requesting an element to add to
     * the form. $_GET etc should be set before request
     */
    protected function performElementFormRequest()
    {
        $controller = $this->getDefaultController(['checkFormAccess']);
        $controller->method('checkFormAccess')
            ->willReturn(true);

        $action = $controller->createAction('elementForm');

        $controller->runAction($action);
    }

    /**
     * Wrapper to mask complexity of performing the create request through
     * the default controller. $_POST etc should be set prior to call
     *
     * @return mixed
     */
    protected function performCreateRequestWithController()
    {
        $controller = $this->getDefaultController(['checkCreateAccess', 'redirect', 'render']);
        // not concerned about permissions
        $controller->method('checkCreateAccess')
            ->willReturn(true);
        $controller->method('render')
            ->will($this->returnCallback(function (...$args) {
                $this->fail('Create request unexpectedly attempting to render with params: ' . print_r($args, true));
            }));

        return $this->runActionAndCaptureEventIdRedirect($controller, 'create');
    }

    /**
     * Wrapper to mask the complexity of running the update request through
     * the default controller. $_POST etc should be set prior to call
     *
     */
    protected function performUpdateRequestWithController()
    {
        $controller = $this->getDefaultController(['checkUpdateAccess', 'redirect', 'render']);
        // not concerned about permissions
        $controller->method('checkUpdateAccess')
            ->willReturn(true);
        $controller->method('render')
            ->will($this->returnCallback(function (...$args) {
                $this->fail('Update request unexpectedly attempting to render with params: ' . print_r($args, true));
            }));

        return $this->runActionAndCaptureEventIdRedirect($controller, 'update');
    }

    private function runActionAndCaptureEventIdRedirect($controller, $actionName)
    {
        // need to capture the redirect to get the success
        $redirected = null;
        $controller->method('redirect')
            ->willReturnCallback(function ($args) use (&$redirected) {
                $redirected = $args[0];
            });

        $action = $controller->createAction($actionName);

        $controller->runAction($action);

        $extract_event_id_regexp = '~default/view/(\d+)$~';

        $this->assertEquals(
            1,
            preg_match($extract_event_id_regexp, $redirected, $matches),
            'redirect action failed, there has been an unexpected test failure'
        );

        return $matches[1];
    }
}
