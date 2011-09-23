<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class m110606_142844_insert_sites extends CDbMigration
{
	public $sites = array(
		'Northwick Park',
		'Potters Bar',
		'Watford',
		'Bedford',
		'Ealing',
		'St Georges\'',
		'Bridge Lane',
		'QMHR',
		'Teddington',
		'Moorfields East',
		'St Ann\'s'
	);

	public function up()
	{
		foreach ($this->sites as $site) {
			$this->insert('site', array(
				'name' => $site
			));
		}
	}

	public function down()
	{
		foreach ($this->sites as $site) {
			$this->delete('site', 'name=:name',
				array(':name' => $site));
		}
	}
}
