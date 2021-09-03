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
        'username' => 'JoeBloggs',
        'first_name' => 'Joe',
        'last_name' => 'Bloggs',
        'title' => 'Mr',
        'qualifications' => 'dummy',
        'role' => 'user',
        'has_selected_firms' => 0,
        'email' => 'joe@bloggs.com',
        'active' => 1,
        'salt' => 'qWQJaOT4Kz',
        'password' => '4a3de11333d5814d90270c27116f1bdc', // pw: secret,
        'global_firm_rights' => 1,
                                                 'contact_id' => 1,
    ),
    'user2' => array(
        'username' => 'JaneBloggs',
        'first_name' => 'Jane',
        'last_name' => 'Bloggs',
        'title' => 'Mrs',
        'qualifications' => 'dummy',
        'role' => 'user',
        'has_selected_firms' => 0,
        'email' => 'jane@bloggs.com',
        'active' => 1,
        'salt' => '4d36ed1c4a',
        'password' => '3f3819bcd2ed9d433e2dc26c5da82ae9', // pw: password
        'global_firm_rights' => 0,
                                                 'contact_id' => 2,
    ),
    'user3' => array(
        'username' => 'icabod',
        'first_name' => 'icabod',
        'last_name' => 'crane',
        'title' => 'Mr',
        'qualifications' => 'dummy',
        'role' => 'user',
        'has_selected_firms' => 0,
        'email' => 'icabod@icabod.com',
        'active' => 0,
        'salt' => '4d36f32441',
        'password' => '19187c5d5985482d352a9d6ffa1d6759', // pw: 12345
        'global_firm_rights' => 0,
                                                 'contact_id' => 3,
    ),
    'admin' => array(
        'username' => 'admin',
        'first_name' => 'Admin',
        'last_name' => 'User',
        'title' => 'Mr',
        'qualifications' => 'dummy',
        'role' => 'admin',
        'has_selected_firms' => 0,
        'email' => 'admin@mail.com',
        'active' => 1,
        'salt' => 'FbYJis0YG3',
        'password' => '44e327c6e513ecd64d050e29678bf8a6', // pw: 54321
        'global_firm_rights' => 0,
    ),
    'ssouser' => array(
        'username' => 'ssouser',
        'first_name' => 'User',
        'last_name' => 'SSO',
        'title' => 'Mr',
        'qualifications' => 'test',
        'role' => 'user',
        'has_selected_firms' => 0,
        'email' => 'sso@testuser.com',
        'active' => 1,
        'global_firm_rights' => 1,
    ),
);
