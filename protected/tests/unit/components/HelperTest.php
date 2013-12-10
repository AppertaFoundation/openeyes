<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class HelperTest extends CTestCase
{
	public function getAgeDataProvider()
	{
		return array(
			array('Unknown', null),
			array(49, date('Y-m-d', strtotime('-50 years +1 day'))),
			array(50, date('Y-m-d', strtotime('-50 years'))),
			array(50, date('Y-m-d', strtotime('-50 years -1 day'))),
			array(49, '1925-06-01', '1975-01-01'),
			array(50, '1925-06-01', '1975-06-01'),
			array(50, '1925-06-01', '1975-12-01'),
			array(74, '1925-06-01', null, '2000-01-01'),
			array(75, '1925-06-01', null, '2000-06-01'),
			array(75, '1925-06-01', null, '2000-12-01'),
			array(49, '1925-06-01', '1975-01-01', '2000-01-01'),
			array(50, '1925-06-01', '1975-06-01', '2000-06-01'),
			array(50, '1925-06-01', '1975-12-01', '2000-12-01'),
			array(49, '1925-06-01', '2000-01-01', '1975-01-01'),
			array(50, '1925-06-01', '2000-06-01', '1975-06-01'),
			array(50, '1925-06-01', '2000-12-01', '1975-12-01'),
		);
	}

	/**
	 * @dataProvider getAgeDataProvider
	 */
	public function testGetAge($expected, $dob, $date_of_death = null, $check_date = null)
	{
		$this->assertEquals($expected, Helper::getAge($dob, $date_of_death, $check_date));
	}
}
