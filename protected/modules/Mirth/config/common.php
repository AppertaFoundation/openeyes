<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$mirth_username = getenv('MIRTH_DB_USER');

if ($mirth_username) {
    return array(
        'params' => array(
            'admin_structure' => array(
                'System' => array(
                    'Mirth Connect Logs' => '/Mirth/admin/list',
                ),
            ),
            'mirth_connectionString' => 'mysql:host='.(getenv('MIRTH_DB_HOST') ? getenv('MIRTH_DB_HOST') : getenv('DATABASE_HOST')).';port='.(getenv('MIRTH_DB_PORT') ? getenv('MIRTH_DB_PORT') : getenv('DATABASE_PORT')).';dbname='.(getenv('MIRTH_DB_NAME') ? getenv('MIRTH_DB_NAME') : 'mirthdb'),
            'mirth_username' => (getenv('MIRTH_DB_USER') ? getenv('MIRTH_DB_USER') : 'mirthconnect'),
            'mirth_password' => (getenv('MIRTH_DB_PASSWORD') ? getenv('MIRTH_DB_PASSWORD') : 'mirthconnect'),
        )
    );
} else {
    return array();
}
