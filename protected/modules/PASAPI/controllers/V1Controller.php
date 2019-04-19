<?php

namespace OEModule\PASAPI\controllers;

/*
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

use UserIdentity;

class V1Controller extends \CController
{
    protected static $resources = array('Patient', 'PatientAppointment');
    protected static $version = 'V1';
    protected static $supported_formats = array('xml');

    public static $UPDATE_ONLY_HEADER = 'HTTP_X_OE_UPDATE_ONLY';
    public static $PARTIAL_RECORD_HEADER = 'HTTP_X_OE_PARTIAL_RECORD';

    /**
     * @var string output format defaults to xml
     */
    protected $output_format = 'xml';

    /**
     * @TODO: map from output_format when we support multiple.
     *
     * @return string
     */
    protected function getContentType()
    {
        return 'application/xml';
    }

    /**
     * This overrides the default behaviour for supported resources by pushing the resource
     * into the GET parameter and updating the actionID.
     *
     * This is necessary because there's no way of pushing the appropriate pattern to the top of the
     * URLManager config, so this captures calls where the id doesn't contain non-numerics.
     *
     * @param string $actionID
     * @throws \CException
     * @return \CAction|\CInlineAction
     */
    public function createAction($actionID)
    {
        if (in_array($actionID, static::$resources)) {
            $_GET['resource_type'] = $actionID;
            switch (\Yii::app()->getRequest()->getRequestType()) {
                case 'PUT':
                    return parent::createAction('Update');
                    break;
                case 'DELETE':
                    return parent::createAction('Delete');
                    break;
                default:
                    $this->sendResponse(405);
                    break;
            }
        }

        return parent::createAction($actionID);
    }

    /**
     * @param \CAction $action
     *
     * @return bool
     */
    public function beforeAction($action)
    {
        foreach (\Yii::app()->request->preferredAcceptTypes as $type) {
            if ($type['baseType'] == 'xml' || $type['subType'] == 'xml' || $type['subType'] == '*') {
                $this->output_format = 'xml';
                break;
            } else {
                $this->output_format = $type['baseType'];
            }
        }

        if (!in_array($this->output_format, static::$supported_formats)) {
            $this->sendResponse(406, 'PASAPI only supports '.implode(',',  static::$supported_formats));
        }

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            $this->sendResponse(401);
        }
        $identity = new UserIdentity($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        if (!$identity->authenticate()) {
            $this->sendResponse(401);
        }

        \Yii::app()->user->login($identity);

        if (!\Yii::app()->user->checkAccess('OprnApi')) {
            $this->sendResponse(403);
        }

        return parent::beforeAction($action);
    }

    /**
     * Simple wrapper to encapsulate the arguments required for any of the API actions.
     */
    public function expectedParametersForAction($action)
    {
        return array(
            'update' => 'id',
            'delete' => 'id',
        )[strtolower($action->id)];
    }

    /**
     * @param \CAction $action
     *
     * @throws CHttpException
     */
    public function invalidActionParams($action)
    {
        $this->sendErrorResponse(400, array('Missing request parameter(s). Required parameter(s) are: '.$this->expectedParametersForAction($action)));
    }

    public function getResourceModel($resource_type)
    {
        return "\\OEModule\\PASAPI\\resources\\{$resource_type}";
    }

    /**
     * Whether the request is an "updateOnly" request.
     *
     * @return bool
     */
    public function getUpdateOnly()
    {
        if (!array_key_exists(static::$UPDATE_ONLY_HEADER, $_SERVER)) {
            return false;
        }

        return (bool) $_SERVER[static::$UPDATE_ONLY_HEADER];
    }

    /**
     * Whether the request is a partial record which only sets the fields that are provided
     * on the given record.
     *
     * @return bool
     */
    public function getPartialRecord()
    {
        if (!array_key_exists(static::$PARTIAL_RECORD_HEADER, $_SERVER)) {
            return false;
        }

        return (bool) $_SERVER[static::$PARTIAL_RECORD_HEADER];
    }

    /**
     * @param $resource_type
     * @param $id
     */
    public function actionUpdate($resource_type, $id)
    {
        if (!in_array($resource_type, static::$resources)) {
            $this->sendErrorResponse(404, "Unrecognised Resource type {$resource_type}");
        }

        if (!$id) {
            $this->sendResponse(404, 'External Resource ID required');
        }

        $resource_model = $this->getResourceModel($resource_type);

        $body = \Yii::app()->request->rawBody;

        try {
            $resource = $resource_model::fromXml(static::$version, $body, array(
                'update_only' => $this->getUpdateOnly(),
                'partial_record' => $this->getPartialRecord(),
            ));

            if ($resource->errors) {
                $this->sendErrorResponse(400, $resource->errors);
            }

            $resource->id = $id;

            if (!$internal_id = $resource->save()) {
                if ($resource->errors && !$resource->warn_errors) {
                    $this->sendErrorResponse(400, $resource->errors);
                } else {
                    // no internal id indicates we didn't get a resource
                    $response = array('Message' => $resource_type.' not created');
                    // map errors to warnings if this is the case
                    if ($resource->errors) {
                        $response['Warnings'] = $resource->errors;
                    }

                    // success in that we are happy for there to have been no action taken
                    $this->sendSuccessResponse(200, $response);
                }
            }

            $response = array(
                'Id' => $internal_id,
            );

            if ($resource->isNewResource) {
                $status_code = 201;
                $response['Message'] = $resource_type.' created.';
            } else {
                $status_code = 200;
                $response['Message'] = $resource_type.' updated.';
            }

            if ($resource->warnings) {
                $response['Warnings'] = $resource->warnings;
            }

            $this->sendSuccessResponse($status_code, $response);
        } catch (\Exception $e) {
            $errors = $resource->errors;
            $errors[] = $e->getMessage();

            $this->sendErrorResponse(500, $errors);
        }
    }

    public function actionDelete($resource_type, $id)
    {
        if (!in_array($resource_type, static::$resources)) {
            $this->sendErrorResponse(404, "Unrecognised Resource type {$resource_type}");
        }

        if (!$id) {
            $this->sendResponse(404, 'External Resource ID required');
        }

        $resource_model = $this->getResourceModel($resource_type);

        if (!method_exists($resource_model, 'delete')) {
            $this->sendResponse(405);
        }

        try {
            if (!$resource = $resource_model::fromResourceId(static::$version, $id)) {
                $this->sendResponse(404, 'Could not find resource for external Id');
            }

            if ($resource->delete()) {
                $this->sendResponse(204);
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();

            $this->sendErrorResponse(500, $errors);
        }
    }

    protected function sendErrorResponse($status, $messages = array())
    {
        $body = '<Failure><Errors><Error>'.implode('</Error><Error>', $messages).'</Error></Errors></Failure>';

        $this->sendResponse($status, $body);
    }

    protected function sendSuccessResponse($status, $response)
    {
        $body = '<Success>';
        if (isset($response['Id'])) {
            $body .= "<Id>{$response['Id']}</Id>";
        }

        $body .= "<Message>{$response['Message']}</Message>";

        if (isset($response['Warnings'])) {
            $body .= '<Warnings><Warning>'.implode('</Warning><Warning>', $response['Warnings']).'</Warning></Warnings>';
        }

        $body .= '</Success>';

        $this->sendResponse($status, $body);
    }

    protected function sendResponse($status = 200, $body = '')
    {
        header('HTTP/1.1 '.$status);
        header('Content-type: '.$this->getContentType());
        if ($status == 401) {
            header('WWW-Authenticate: Basic realm="OpenEyes"');
        }
        // TODO: configure allowed methods per resource
        if ($status == 405) {
            header('Allow: PUT');
        }
        echo $body;
        \Yii::app()->end();
    }
}
