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

$config = [
    'params' => [
        'admin_structure' => [
            'Payload Processor Api' => [
                'Requests' => [
                    'module' => 'Api',
                    'uri' => '/Api/Request/admin/request/index',
                    'restricted' => array('admin'),
                ],
                'Request - Manual Upload' => [
                    'module' => 'Api',
                    'uri' => '/Api/Request/admin/default/manualupload',
                    'restricted' => array('admin'),
                ],
                'Request Type' => [
                    'module' => 'Api',
                    'uri' => '/Api/Request/admin/requestType/index',
                    'restricted' => array('admin'),
                ],
                'Request Queue' => [
                    'module' => 'Api',
                    'uri' => '/Api/Request/admin/requestQueue/index',
                    'restricted' => array('admin'),
                ],
                'Mime Type' => [
                    'module' => 'Api',
                    'uri' => '/Api/Request/admin/mimeType/index',
                    'restricted' => array('admin'),
                ],
                'Attachment Type' => [
                    'module' => 'Api',
                    'uri' => '/Api/Request/admin/attachmentType/index',
                    'restricted' => array('admin'),
                ],
            ],
        ],
    ],

    'components' => [
        'urlManager' => [
            'rules' => [
                'Api/Request/admin/<controller:\w+>/<action:\w+>' => '/Api/Request/RequestAdmin/<controller>/<action>',
                'Api/Request/admin/<controller:\w+>/<action:\w+>/<id:\d+>' => '/Api/Request/RequestAdmin/<controller>/<action>',
            ]
        ]
    ]
];

return $config;
