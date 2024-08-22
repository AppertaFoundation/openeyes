<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return array(
    'user1' => array(
        'first_name' => 'Joe',
        'last_name' => 'Bloggs',
        'title' => 'Mr',
        'role' => 'user',
        'has_selected_firms' => 0,
        'email' => 'joe@bloggs.com',
        'global_firm_rights' => 1,
        'contact_id' => 1,
    ),
    'user2' => array(
        'first_name' => 'Jane',
        'last_name' => 'Bloggs',
        'title' => 'Mrs',
        'role' => 'user',
        'has_selected_firms' => 0,
        'email' => 'jane@bloggs.com',
        'global_firm_rights' => 0,
        'contact_id' => 2,
    ),
    'user3' => array(
        'first_name' => 'icabod',
        'last_name' => 'crane',
        'title' => 'Mr',
        'role' => 'user',
        'has_selected_firms' => 0,
        'email' => 'icabod@icabod.com',
        'global_firm_rights' => 0,
        'contact_id' => 3,
    ),
    'admin' => array(
        'first_name' => 'Admin',
        'last_name' => 'User',
        'title' => 'Mr',
        'role' => 'admin',
        'has_selected_firms' => 0,
        'email' => 'admin@mail.com',
        'global_firm_rights' => 0,
    ),
    'ssouser' => array(
        'username' => 'ssouser',
        'first_name' => 'User',
        'last_name' => 'SSO',
        'title' => 'Mr',
        'role' => 'user',
        'has_selected_firms' => 0,
        'email' => 'sso@testuser.com',
        'active' => 1,
        'global_firm_rights' => 1,
    ),
);
