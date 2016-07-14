<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

return array(
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
    ),
    'params' => array(
        'admin_menu' => array(
            'Value Remaps' => '/PASAPI/admin/viewXpathRemaps',
        ),
    ),
);
