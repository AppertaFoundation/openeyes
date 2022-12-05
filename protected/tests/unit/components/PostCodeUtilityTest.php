<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class PostCodeUtilityTest extends CTestCase
{
    protected PostCodeUtility $postcodeUtility;

    public function setUp(): void
    {
        parent::setUp();
        $this->postcodeUtility = new PostCodeUtility();
    }

    /**
     * @covers PostCodeUtility
     */
    public function testPostCodeUtility()
    {
        $this->assertIsArray($this->postcodeUtility->towns);
        $this->assertIsArray($this->postcodeUtility->counties);
    }

    /**
     * @covers PostCodeUtility
     */
    public function testParsePostCode()
    {
        $this->assertFalse($this->postcodeUtility->parsePostCode('notApostcode'));
        $result = $this->postcodeUtility->parsePostCode('HP11 2LE');
        $this->assertIsArray($result);
        $result = $this->postcodeUtility->parsePostCode('HP112LE');
        $this->assertIsArray($result);
    }

    /**
     * @covers PostCodeUtility
     */
    public function testTownForOuterPostcode()
    {
        $result = $this->postcodeUtility->townForOuterPostCode('Nw10');
        $this->assertIsString($result);
        $result = $this->postcodeUtility->townForOuterPostCode('nW10');
        $this->assertIsString($result);
        $this->assertGreaterThan(0, strlen($result));
    }

    /**
     * @covers PostCodeUtility
     */
    public function testTownForOuterWrongPostcode()
    {
        $result = $this->postcodeUtility->townForOuterPostCode('ssss');
        $this->assertNull($result);
    }

    /**
     * @covers PostCodeUtility
     */
    public function testCountyForOuterPostCode()
    {
        $result = $this->postcodeUtility->countyForOuterPostCode('Nw10');
        $this->assertIsString($result);
        $this->assertGreaterThan(0, strlen($result));
    }

    /**
     * @covers PostCodeUtility
     */
    public function testCountyForOuterWrongPostCode()
    {
        $result = $this->postcodeUtility->countyForOuterPostCode('ssss');
        $this->assertNull($result);
    }

    /**
     * @covers PostCodeUtility
     */
    public function testIsTown()
    {
        $this->assertTrue($this->postcodeUtility->isTown('Glasgow'));
        $this->assertTrue($this->postcodeUtility->isTown('glasgow'));
        $this->assertFalse($this->postcodeUtility->isTown('Carrapipi'));
    }

    /**
     * @covers PostCodeUtility
     */
    public function testIsCounty()
    {
        $this->assertTrue($this->postcodeUtility->isCounty('Wiltshire'));
        $this->assertTrue($this->postcodeUtility->isCounty('wiltshire'));
        $this->assertFalse($this->postcodeUtility->isCounty('Carrapipi'));
    }

    public function tearDown(): void
    {
        unset($this->postcodeUtility);
    }
}
