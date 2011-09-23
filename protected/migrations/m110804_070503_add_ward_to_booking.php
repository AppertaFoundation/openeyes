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

class m110804_070503_add_ward_to_booking extends CDbMigration
{
	public function up()
	{
		$this->addColumn('booking', 'ward_id', 'integer(11) unsigned DEFAULT "0"');

//		$this->addForeignKey('ward_id', 'booking', 'ward_id', 'ward', 'id');
	}

	public function down()
	{
//		$this->dropForeignKey('ward_id', 'booking');

		$this->dropColumn('booking', 'ward_id');
	}
}
