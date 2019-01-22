<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return array(
    'import' => array(
        'application.modules.PASAPI.components.*',
    ),
    'components' => array(
        'request' => array(
            'noCsrfValidationRoutes' => array(
                'PASAPI/',
            ),
        ),
        'urlManager' => array(
            'rules' => array(
                // add a rule so that letters can be used in the external id for the resource
                array('PASAPI/V1/update', 'pattern' => 'PASAPI/<controller:\w+>/(<resource_type:\w+>?/<id:\w+>)?', 'verb' => 'PUT'),
                array('PASAPI/V1/delete', 'pattern' => 'PASAPI/<controller:\w+>/(<resource_type:\w+>?/<id:\w+>)?', 'verb' => 'DELETE'),
            ),
        ),

        'event' => array(
            'observers' => array(
                'patient_search_criteria' => array(
                    'search_pas' => array(
                        'class' => 'OEModule\PASAPI\components\PasApiObserver',
                        'method' => 'search',
                    ),
                ),
            ),
        ),
    ),

    'aliases' => array(
        'PASAPIAdmin' => 'OEModule.PASAPI.modules.PASAPIAdmin',
    ),

    'modules' => ['PASAPIAdmin'],


    'params' => array(

        'pasapi' => array(
            'enabled' => false,
            'url' => 'http://localhost:4200',
            'curl_timeout' => 10, //sec

            // comment this out to use the params['curl_proxy']
            // use 'false' to bypass any proxies
            'proxy' => false,

            // set the caching time in seconds - don't query the PAS for data that had been cached within the last X minutes
            // set cache_time to null (never stale) to never update the object from PAS
            // set cache_time to 0 (always stale) to update the object from PAS every time
            'cache_time' => 300, //sec
        ),
    ),
);
