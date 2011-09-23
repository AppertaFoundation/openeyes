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

class m110826_124603_alter_user_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('user', 'code', 'varchar(255) DEFAULT NULL');
		$this->dropColumn('user', 'password');
		$this->dropColumn('user', 'salt');
                $this->addColumn('user', 'password', 'varchar(40) COLLATE utf8_bin DEFAULT NULL');
                $this->addColumn('user', 'salt', 'varchar(10) COLLATE utf8_bin DEFAULT NULL');

		$this->update('user', array('password' => 'd45409ef1eaa57f5041bf3a1b510097b', 'salt' => 'FbYJis0YG3'));
	}

	public function down()
	{
		$this->dropColumn('user', 'code');
                $this->dropColumn('user', 'password');
                $this->dropColumn('user', 'salt');
                $this->addColumn('user', 'password', 'varchar(40) COLLATE utf8_bin NOT NULL');
                $this->addColumn('user', 'salt', 'varchar(10) COLLATE utf8_bin NOT NULL');

		$this->update('user', array('password' => 'd45409ef1eaa57f5041bf3a1b510097b', 'salt' => 'FbYJis0YG3'));
	}
}
