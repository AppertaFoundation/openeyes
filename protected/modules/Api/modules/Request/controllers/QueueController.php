<?php
/**
 * (C) Copyright Apperta Foundation 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class QueueController extends BaseRequestController
{
    /**
     * Integrate files and media coming from external devices and systems
     */
    public function actionAdd()
    {
        $request_type = \Yii::app()->request->getParam('request_type');
        $system_message = \Yii::app()->request->getParam('system_message');

        /* Get content type form the header or from GET (GET overrides header content-type) */
        $content_type = $this->getContentType();

        /* Creating dynamically the handler class based on the content type, form-data, x-www-form-urlencoded, etc */
        $request = $this->getHandlerClassName($content_type);

        /* Init the handler */
        $handler = new $request($request_type, $system_message, $content_type);

        $request_id = $handler->save();
        if ($request_id === false) {
            $errors = $handler->errorSummary();

            /* [system] is set when the try{} block fails - Exception error added to the errorSummary */
            /* 422 - Unprocessable Entity - status code means the server understands the
                content type of the request entity and the syntax of the request entity is correct but was unable to
                process the contained instructions. */
            $status = isset($errors['system']) ? 500 : 422;
            $response = ['success' => 0, "message" => $handler->errorSummary()];
        } else {
            $status = 200;
            $response = ['request_id' => $request_id];
        }

        $this->renderJSON($status, $response);
    }
}
