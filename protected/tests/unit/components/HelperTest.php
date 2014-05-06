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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'HelperTestNS.php';

class HelperTest extends CTestCase
{
	static private $tz;

	static public function setUpBeforeClass()
	{
		self::$tz = date_default_timezone_get();
		date_default_timezone_set('UTC');
	}

	static public function tearDownAfterClass()
	{
		date_default_timezone_set(self::$tz);
	}

	public function mysqlDate2JsTimestampDataProvider()
	{
		return array(
			array(null, null),
			array('1969-12-31 23:59:59', -1000),
			array('1970-01-01 00:00:00', 0),
			array('2013-12-18 10:14:33', 1387361673000),
			array('9999-12-31 23:59:59', 253402300799000),
		);
	}

	/**
	 * @dataProvider mysqlDate2JsTimestampDataProvider
	 */
	public function testMysqlDateToJsTimestamp($input, $output)
	{
		$this->assertEquals($output, Helper::mysqlDate2JsTimestamp($input));
	}

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


	public function testExtractValues()
	{
		$objects = array(
			array('disorder' => array('term' => 'term1')),
			array('disorder' => array('term' => 'term2')),
			array('disorder' => array()),
			array(),
			ComponentStubGenerator::generate(
				'SecondaryDiagnosis',
				array('disorder' => ComponentStubGenerator::generate('Disorder', array('term' => 'term3')))
			),
			ComponentStubGenerator::generate(
				'SecondaryDiagnosis',
				array('disorder' => ComponentStubGenerator::generate('Disorder', array('term' => 'term4')))
			),
			ComponentStubGenerator::generate(
				'SecondaryDiagnosis',
				array('disorder' => ComponentStubGenerator::generate('Disorder', array('term' => null)))
			),
			ComponentStubGenerator::generate(
				'SecondaryDiagnosis',
				array()
			),
		);

		$expected = array(
			'term1',
			'term2',
			'termDefault',
			'termDefault',
			'term3',
			'term4',
			'termDefault',
			'termDefault',
		);

		$this->assertEquals($expected, Helper::extractValues($objects, 'disorder.term', 'termDefault'));
	}

	public function testFormatList_Empty()
	{
		$this->assertEquals('', Helper::formatList(array()));
	}

	public function testFormatList_One()
	{
		$this->assertEquals('foo', Helper::formatList(array('foo')));
	}

	public function testFormatList_Two()
	{
		$this->assertEquals('foo and bar', Helper::formatList(array('foo', 'bar')));
	}

	public function testFormatList_Three()
	{
		$this->assertEquals('foo, bar and baz', Helper::formatList(array('foo', 'bar', 'baz')));
	}


	public function testgetNSShortname()
	{
		$test = new HelperTestNS\models\NamespacedElement();

		$this->assertEquals('NamespacedElement', Helper::getNSShortname($test));
	}

	public function getDateForAgeProvider()
	{
		return array(
				array('2015-05-12', '2004-05-12', 11),
				array('2015-05-12', '2004-05-12', 11, '2016-04-11'),
				array(null, '2004-05-12', 11, '2009-04-11'),
		);
	}

	/**
	 * @dataProvider getDateForAgeProvider
	 */
	public function testgetDateForAge($expected, $dob, $age, $date_of_death = null)
	{
		$this->assertEquals($expected, Helper::getDateForAge($dob, $age, $date_of_death));
	}
}
