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

class Api_PracticeTest extends FhirTestCase
{
	public function testRead()
	{
		$this->get('Organization/prac-1');
		$this->assertXPathEquals('Organization', 'local-name()');
		$this->assertXPathEquals('F001', 'string(./fhir:identifier/fhir:system[@value="http://www.datadictionary.nhs.uk/data_dictionary/attributes/o/org/organisation_code_de.asp"]/../fhir:value/@value)');
		$this->assertXPathEquals('0111 111 1111', 'string(./fhir:telecom/fhir:system[@value="phone"]/../fhir:value/@value)');
		$this->assertXPathFound('./fhir:address/fhir:line[@value="83, Wintour Lane"]');
		$this->assertXPathFound('./fhir:address/fhir:line[@value="Northop"]');
		$this->assertXPathEquals('Heald Green', 'string(./fhir:address/fhir:city/@value)');
		$this->assertXPathEquals('Lothian', 'string(./fhir:address/fhir:state/@value)');
		$this->assertXPathEquals('GH14 2DH', 'string(./fhir:address/fhir:zip/@value)');
		$this->assertXPathEquals('United Kingdom', 'string(./fhir:address/fhir:country/@value)');
	}

	public function testUpdate()
	{
		$source = file_get_contents(__DIR__ . '/files/Practice.xml');
		$this->put('Organization/prac-3', $source);
		$this->get('Organization/prac-3');
		$this->assertXmlEquals($source);
	}

	public function testDelete()
	{
		$source = file_get_contents(__DIR__ . '/files/Practice.xml');
		$this->post('Organization', $source, array('Category' => services\Practice::getOeFhirProfile() . "; scheme=http://hl7.org/fhir/tag/profile"));
		$this->delete(preg_replace('|/_history/.*$|', '', $this->response->getLocation()));
		$this->assertResponseCode(204);
	}

	public function testCreate()
	{
		$source = file_get_contents(__DIR__ . '/files/Practice.xml');
		$this->post('Organization', $source, array('Category' => services\Practice::getOeFhirProfile() . "; scheme=http://hl7.org/fhir/tag/profile"));
		$this->get($this->response->getLocation());
		$this->assertXmlEquals($source);
	}

	public function testSearchByOrgCode()
	{
		$this->get('Organization?identifier=F002&_profile=' . urlencode(services\Practice::getOeFhirProfile()));
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathEquals($this->client->getBaseUrl(), 'string(./atom:link[@rel="base"]/@href)');
		$this->assertUrlEquals(
			$this->client->getBaseUrl() . '/Organization?identifier=F002&_profile=' . urlencode(services\Practice::getOeFhirProfile()),
			$this->xPathEval('string(./atom:link[@rel="self"]/@href)')
		);
		$this->assertXPathCount(1, './atom:entry');
		$this->assertXPathEquals($this->client->getBaseUrl() . '/Organization/prac-2', 'string(./atom:entry/atom:id/text())');
		$this->assertXPathEquals('Organization', 'local-name(./atom:entry/atom:content/*)');
	}
}
