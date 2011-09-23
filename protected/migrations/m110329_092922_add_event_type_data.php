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

class m110329_092922_add_event_type_data extends CDbMigration
{
    public function up()
    {
		$this->insert('event_type', array(
			'name' => 'example',
			'first_in_episode_possible' => 0
		));
    }

    public function down()
    {
		$this->delete('event_type', 'name = :name AND first_in_episode_possible = :fie',
			array(':name' => 'example', ':fie' => 0)
		);
    }
}
