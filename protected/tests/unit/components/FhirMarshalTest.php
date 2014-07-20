<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class FhirMarshalTest extends PHPUnit_Framework_TestCase
{
	static private $use_errors;

	static public function setUpBeforeClass()
	{
		self::$use_errors = libxml_use_internal_errors(true);
	}

	static public function tearDownAfterClass()
	{
		libxml_use_internal_errors(self::$use_errors);
	}

	private $marshal;

	public function setUp()
	{
		$this->marshal = new FhirMarshal;
	}

	public function testIsStandardType_True()
	{
		$this->assertTrue($this->marshal->isStandardType('Patient'));
	}

	public function testIsStandardType_False()
	{
		$this->assertFalse($this->marshal->isStandardType('Armadillo'));
	}

	public function xmlDataProvider()
	{
		$data = array();

		foreach (glob(__DIR__ . '/' . __CLASS__ . '/*.xml') as $xml_path) {
			preg_match('|([^/]+)\.xml$|', $xml_path, $m);
			$name = $m[1];

			$json_path = __DIR__ . '/' . __CLASS__ . "/{$name}.json";

			$data[] = array(
				$name,
				file_get_contents($xml_path),
				file_get_contents($json_path),
			);
		}

		return $data;
	}

	/**
	 * @dataProvider xmlDataProvider
	 */
	public function testXmlToJson($name, $xml, $json)
	{
		$expected = $this->marshal->parseJson($json);
		$this->assertEquals($expected, $this->marshal->parseXml($xml));
	}

	public function testParseXml_Malformed()
	{
		$this->assertEquals(null, $this->marshal->parseXml('>'));
	}

	/**
	 * @dataProvider xmlDataProvider
	 */
	public function testJsonToXml($name, $xml, $json)
	{
		$actual = $this->marshal->renderXml($this->marshal->parseJson($json));
		$this->assertXmlStringEqualsXmlString($xml, $actual);
	}

	public function testRenderXml_NonContiguousArrays()
	{
		$res = (object)array(
			"resourceType" => "Foo",
			"foo" => array(
				0 => "zero",
				1 => "one",
				5 => "five",
			),
			"_foo" => array(
				1 => (object)array("id" => "bar"),
				2 => (object)array("id" => "baz"),
				4 => (object)array("id" => "qux"),
			),
		);

		$expected = '<?xml version="1.0" encoding="utf-8"?><Foo xmlns="http://hl7.org/fhir"><foo value="zero" id="bar"/><foo value="one" id="baz"/><foo value="five" id="qux"/></Foo>';

		$this->assertXmlStringEqualsXmlString($expected, $this->marshal->renderXml($res));
	}
}
