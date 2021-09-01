<?php
/**
 * OpenEyes.
 *
 * (C] OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option] any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c] 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return [
    'import' => [
        'application.modules.PASAPI.components.*',
    ],
    'components' => [
        'request' => [
            'noCsrfValidationRoutes' => [
                'PASAPI/',
            ],
        ],
        'urlManager' => [
            'rules' => [
                // add a rule so that letters can be used in the external id for the resource
                ['PASAPI/V1/update', 'pattern' => 'PASAPI/<controller:\w+>/<resource_type:\w+>/<id:\w+>/identifier-type/<identifier_type>', 'verb' => 'PUT'],
                ['PASAPI/V1/delete', 'pattern' => 'PASAPI/<controller:\w+>/<resource_type:\w+>/<id:\w+>', 'verb' => 'DELETE'],
            ],
        ],

        'event' => [
            'observers' => [
                'patient_search_criteria' => [
                    'search_pas' => [
                        'class' => 'OEModule\PASAPI\components\PasApiObserver',
                        'method' => 'search',
                    ],
                ],
            ],
        ],
    ],

    'aliases' => [
        'PASAPIAdmin' => 'OEModule.PASAPI.modules.PASAPIAdmin',
    ],
    'modules' => ['PASAPIAdmin'],


    'params' => [

        'pasapi' => [
            'enabled' => getenv("OE_PASAPI_ENABLE") && trim(strtolower(getenv("OE_PASAPI_ENABLE"))) !== "false",

            // DEPRECATED 'url', this setting moved to the Admin section
            //'url' => getenv("OE_PASAPI_URL") ?: 'http://localhost:4200',

            'curl_timeout' => 10, //sec

            // DEPRECATED 'proxy', this setting moved to the Admin section
            // comment this out to use the params['curl_proxy']
            // use 'false' to bypass any proxies
            'proxy' => getenv("OE_PASAPI_PROXY") ?: false,

            // set the caching time in seconds - don't query the PAS for data that had been cached within the last X minutes
            // set cache_time to null (never stale] to never update the object from PAS
            // set cache_time to 0 (always stale] to update the object from PAS every time

            // DEPRECATED 'cache_time', this setting moved to the Admin section
            //'cache_time' => getenv("OE_PASAPI_CACHE_TIME") ?: 300, //sec

            // DEPRECATED 'allowed_params', this setting moved to the Admin section
            //'allowed_params' => [],
        ],
    ],
];
