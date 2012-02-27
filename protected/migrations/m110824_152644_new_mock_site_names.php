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

class m110824_152644_new_mock_site_names extends CDbMigration
{
	public function up()
	{
		$this->update('site', array('name' => 'Hospital 1', 'short_name' => 'Hospital 1', 'address1' => '1 Road Street'), "id=1");

                $this->insert('site', array(
                        'name' => 'Hospital 2',
			'code' => 'A2',
			'short_name' => 'Hospital 2',
			'address1' => '1 Medical Mews',
			'address2' => 'Hospitalshire',
			'address3' => '',
			'postcode' => 'SW1A 1DG',
			'fax' => '020 7876 5432',
			'telephone' => '020 7234 5678'
                ));

                $this->insert('site', array(
                        'name' => 'Hospital 3',
                        'code' => 'A3',                   
                        'short_name' => 'Hospital 3',
                        'address1' => 'Hospital House',
                        'address2' => 'Hospital Building',
                        'address3' => '1 Hospital Street',
                        'postcode' => 'W1 1AA',
                        'fax' => '020 7765 4322',
                        'telephone' => '020 7345 6789'
                ));
        }

        public function down()
        {
                $this->delete('site', 'name=:name', array(':name' => 'Hospital 2'));
                $this->delete('site', 'name=:name', array(':name' => 'Hospital 3'));

                $this->update('site', array('name' => 'Example site long name', 'short_name' => 'Example site', 'address1' => '1 road street'), "id=1");
        }
}
