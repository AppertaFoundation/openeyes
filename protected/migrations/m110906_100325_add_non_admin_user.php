<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class m110906_100325_add_non_admin_user extends CDbMigration
{
	public function up()
	{
		$this->insert('user', array(
			'username' => 'user',
			'first_name' => 'Joe',
			'last_name' => 'Bloggs',
			'email' => 'user@example.com',
			'active' => true,
			'password' => '3653a08a907906f6fc462b81aba2aa87', // user
			'salt' => 'NM7449bLqX',
			'global_firm_rights' => false,
			'title' => 'Mr',
			'qualifications' => '',
			'role' => 'user role',
		));
	}

	public function down()
	{
		$this->delete('user', 'username = :name', array(':name' => 'user'));
	}
}
