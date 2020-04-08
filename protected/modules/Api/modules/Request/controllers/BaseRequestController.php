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

class BaseRequestController extends BaseApiController
{

    const CONTENT_TYPE_MAP = [
        'none' => 'GetRequestHandler',
        'multipart/form-data' => 'FormDataHandler',
        'application/x-www-form-urlencoded' => 'FormUrlEncodedHandler'
    ];

    public function accessRules()
    {
        return [['allow', 'actions' => ['add']]];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'BasicAuthBehavior' => ['class' => 'application.modules.Api.behaviors.BasicAuthBehavior'],
        ]);
    }

    /**
     * Fetching content type from the header
     * Allow to override by GET param
     * @return mixed
     */
    public function getContentType()
    {
        $content_type = \Yii::app()->request->getParam('content_type', null);
        //Allow get param content_type to override the header value
        if ($content_type) {
            return $content_type;
        }

        $headers = getallheaders();
        foreach ($headers as $name => $value) {
            if (strtolower($name) == 'content-type') {
                $content_type = explode(';', $headers[$name])[0];
            }
        }
        return $content_type;
    }

    public function getHandlerClassName($content_type = 'none')
    {
        if ($content_type === null) {
            $content_type = 'none';
        }

        $handler = 'SingleRequestHandler';
        if (array_key_exists($content_type, self::CONTENT_TYPE_MAP)) {
            $handler = self::CONTENT_TYPE_MAP[$content_type];
        }

        return $handler;
    }
}
