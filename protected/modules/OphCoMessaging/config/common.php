<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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

use OEModule\OESysEvent\events\UserSavedSystemEvent;
use OEModule\OphCoMessaging\listeners\CreatePersonalMailboxForUser;

return [
    'components' => [
        'event' => [
            'observers' => [
                [
                    'system_event' => UserSavedSystemEvent::class,
                    'listener' => CreatePersonalMailboxForUser::class
                ]
            ]
        ]
    ],
    'params' => [
        'dashboard_items' => [
            [
                'module' => 'OphCoMessaging',
                // default action is the 'renderDashboard' if 'actions' array is  not set
                'actions' => [
                    'getMessages',
                 ],
                'js' => 'dashboard.js', // assets/js
                'position' => 5,
            ],
        ],
        'admin_structure' => [
            'Message' => [
                'Message sub type settings' => [
                    'module' => 'OphCoMessaging',
                    'uri' => '/OphCoMessaging/MessageSubTypesSettings',
                    'restricted' => ['admin'],
                ],
                'Shared mailboxes' => [
                    'module' => 'OphCoMessaging',
                    'uri' => '/OphCoMessaging/SharedMailboxSettings',
                    'restricted' => ['admin'],
                ],
            ],
        ],
    ]
];
