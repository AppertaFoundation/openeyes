<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$config = [
    'params' => [
        'admin_menu' => array(
            'OphCoCvi' => [
                'Clinical Disorder Section' => ['uri' => '/OphCoCvi/admin/default/clinicalDisorderSection', 'restricted' => array('admin')],
                'Clinical Disorder' => ['uri' => '/OphCoCvi/admin/default/clinicalDisorders', 'restricted' => array('admin')],
                'Patient Factor' => ['uri' => '/OphCoCvi/admin/default/patientFactor', 'restricted' => array('admin')],
                'Employment Status' => ['uri' => '/OphCoCvi/admin/default/employmentStatus', 'restricted' => array('admin')],
                'Contact Urgency' => ['uri' => '/OphCoCvi/admin/default/contactUrgency', 'restricted' => array('admin')],
                'Field of Vision' => ['uri' => '/OphCoCvi/admin/default/fieldOfVision', 'restricted' => array('admin')],
                'Low Vision Status' => ['uri' => '/OphCoCvi/admin/default/lowVisionStatus', 'restricted' => array('admin')],
                'Preferred Info Format' => ['uri' => '/OphCoCvi/admin/default/preferredInfoFormat', 'restricted' => array('admin')],
                'Local Authorities' => ['uri' => '/OphCoCvi/admin/localAuthorities/list', 'restricted' => array('admin')],
            ]
        ),
        'menu_bar_items' => array(
            'cvi' => array(
                'title' => 'CVI',
                'position' => 7,
                'restricted' => array(array('OprnCreateCvi', 'user_id')),
                'uri' => '/OphCoCvi/Default/list',
            ),
            'la' => array(
                'title' => 'LA Admin',
                'uri' => '/OphCoCvi/admin/LocalAuthorities/list',
                'position' => 8,
                'restricted' => array(array('OprnCreateCvi', 'user_id')),
            ),
        ),
    ],

    'components' => [
        'urlManager' => [
            'rules' => [
                'OphCoCvi/admin/<controller:\w+>/<action:\w+>' => '/OphCoCvi/CviAdmin/<controller>/<action>',
                'OphCoCvi/admin/<controller:\w+>/<action:\w+>/<id:\d+>' => '/OphCoCvi/CviAdmin/<controller>/<action>',
            ]
        ]
    ]
];

return $config;
