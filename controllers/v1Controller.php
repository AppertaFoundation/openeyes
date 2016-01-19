<?php namespace OEModule\PASAPI\controllers;

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class v1Controller extends \CController
{

    static protected $resources = array('Patient');
    static protected $version = "v1";

    /**
     * This overrides the default behaviour for supported resources by pushing the resource
     * into the GET parameter and updating the actionID
     *
     * This is necessary because there's no way of pushing the appropriate pattern to the top of the
     * URLManager config, so this captures calls where the id doesn't contain non-numerics.
     *
     * @param string $actionID
     * @return \CAction|\CInlineAction
     */
    public function createAction($actionID)
    {
        if (in_array($actionID, static::$resources)) {
            $_GET['resource_type'] = $actionID;
            switch (\Yii::app()->getRequest()->getRequestType())
            {
                case 'PUT':
                    return parent::createAction('Update');
                    break;
                default:
                    $this->sendResponse(405);
                    break;
            }
        }

        return parent::createAction($actionID);
    }

    public function actionUpdate($resource_type, $id)
    {
        if (!in_array($resource_type, static::$resources))
            $this->sendResponse(404, "Unrecognised Resource type {$resource_type}");

        if (!$id)
            $this->sendResponse(404, "External Resource ID required");

        $body = \Yii::app()->request->rawBody;

        $resource_model = "\\OEModule\\PASAPI\\resources\\{$resource_type}";

        $resource = $resource_model::fromXml(static::$version, $body);

        $resource->id = $id;
        try {
            $resource->save();

            if ($resource->isNewResource) {
                $status_code = 201;
                $message = $resource_type . " created.";
            }
            else {
                $status_code = 200;
                $message = $resource_type . " updated.";
            }

            if ($resource->warnings) {
                $message .= "\nWARNINGS:\n" . implode("\n", $resource->warnings);
            }

            $this->sendResponse($status_code, $message);
        }
        catch (\Exception $e)
        {
            $this->sendResponse(500, YII_DEBUG ? $e->getMessage() : "Could not save resource");
        }



    }

    protected function sendResponse($status = 200, $body = '')
    {
        header('HTTP/1.1 ' . $status);
        if ($status == 401) header('WWW-Authenticate: Basic realm="OpenEyes"');
        // TODO: configure allowed methods per resource
        if ($status == 405) header('Allow: PUT');
        echo $body;
        \Yii::app()->end();
    }

}