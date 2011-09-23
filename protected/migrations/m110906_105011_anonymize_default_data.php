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

class m110906_105011_anonymize_default_data extends CDbMigration
{
	public function up()
	{
		$this->update('firm', array('name' => 'Smith Firm', 'pas_code' => 'SMAB'), 'id = :id',
			array(':id' => 1));
		$this->update('firm', array('name' => 'Jones Firm', 'pas_code' => 'JONE'), 'id = :id',
			array(':id' => 2));
		$this->update('firm', array('name' => 'Elliott Firm', 'pas_code' => 'ELTT'), 'id = :id',
			array(':id' => 3));
		$this->update('firm', array('name' => 'Murray Firm', 'pas_code' => 'MURR'), 'id = :id',
			array(':id' => 4));
		$this->update('firm', array('name' => 'Rodgers Firm', 'pas_code' => 'RODG'), 'id = :id',
			array(':id' => 5));

		$this->update('user', array('first_name' => 'Bob', 'last_name' => 'Andrews'), 'username = :user',
			array(':user' => 'admin'));
	}

	public function down()
	{
		$this->update('firm', array('name' => 'Aylward Firm', 'pas_code' => 'AEAB'), 'id = :id',
			array(':id' => 1));
		$this->update('firm', array('name' => 'Collin Firm', 'pas_code' => 'ADCR'), 'id = :id',
			array(':id' => 2));
		$this->update('firm', array('name' => 'Bessant Firm', 'pas_code' => 'CADB'), 'id = :id',
			array(':id' => 3));
		$this->update('firm', array('name' => 'Allan Firm', 'pas_code' => 'EXAB'), 'id = :id',
			array(':id' => 4));
		$this->update('firm', array('name' => 'Egan Firm', 'pas_code' => 'MREC'), 'id = :id',
			array(':id' => 5));

		$this->update('user', array('first_name' => 'admin', 'last_name' => 'admin'), 'username = :user',
			array(':user' => 'admin'));
	}
}
