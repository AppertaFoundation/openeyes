<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class PostCodeUtilityTest extends CTestCase
{
	protected $postcodeUtility;

	public function setUp(){
		$this->postcodeUtility = new PostCodeUtility();
	}

	public function testPostCodeUtility()
	{
		$this->assertInternalType('array', $this->postcodeUtility->towns);
		$this->assertInternalType('array', $this->postcodeUtility->counties);
	}

	public function testParsePostCode()
	{
		$this->assertFalse( $this->postcodeUtility->parsePostCode('notApostcode'));
		$result = $this->postcodeUtility->parsePostCode('HP11 2LE');
		$this->assertInternalType('array',$result);
		$result = $this->postcodeUtility->parsePostCode('HP112LE');
		$this->assertInternalType('array',$result);
	}

	public function testTownForOuterPostcode(){
		$result = $this->postcodeUtility->townForOuterPostCode('Nw10');
		$this->assertInternalType('string', $result);
		$result = $this->postcodeUtility->townForOuterPostCode('nW10');
		$this->assertInternalType('string', $result);
		$this->assertGreaterThan(0 , strlen($result));
	}

	public function testTownForOuterWrongPostcode(){
		$result = $this->postcodeUtility->townForOuterPostCode('ssss');
		$this->assertNull( $result);
	}

	public function testCountyForOuterPostCode(){
		$result = $this->postcodeUtility->countyForOuterPostCode('Nw10');
		$this->assertInternalType('string', $result);
		$this->assertGreaterThan(0 , strlen($result));
	}

	public function testCountyForOuterWrongPostCode(){
		$result = $this->postcodeUtility->countyForOuterPostCode('ssss');
		$this->assertNull( $result);
	}

	public function testIsTown(){
		$this->assertTrue( $this->postcodeUtility->isTown('Glasgow'));
		$this->assertTrue( $this->postcodeUtility->isTown('glasgow'));
		$this->assertFalse( $this->postcodeUtility->isTown('Carrapipi'));
	}

	public function testIsCounty(){
		$this->assertTrue( $this->postcodeUtility->isCounty('Wiltshire'));
		$this->assertTrue( $this->postcodeUtility->isCounty('wiltshire'));
		$this->assertFalse( $this->postcodeUtility->isCounty('Carrapipi'));
	}

	public function tearDown(){
		unset($this->postcodeUtility);
	}

}

