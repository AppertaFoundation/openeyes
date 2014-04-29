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

class Api_GpTest extends FhirTestCase
{
	public function testRead()
	{
		$this->get('Practitioner/gp-1');
		$this->assertXPathEquals('Practitioner', 'local-name()');
		$this->assertXPathEquals('MNOP', 'string(./fhir:identifier/fhir:system[@value="http://www.datadictionary.nhs.uk/data_dictionary/attributes/g/general_medical_practitioner_ppd_code_de.asp"]/../fhir:value/@value)');
		$this->assertXPathEquals('Dr', 'string(./fhir:name/fhir:prefix/@value)');
		$this->assertXPathEquals('James', 'string(./fhir:name/fhir:given/@value)');
		$this->assertXPathEquals('Kildare', 'string(./fhir:name/fhir:family/@value)');
		$this->assertXPathEquals('0444 444 4444', 'string(./fhir:telecom/fhir:system[@value="phone"]/../fhir:value/@value)');
	}

	public function testUpdate()
	{
		$source = file_get_contents(__DIR__ . '/files/Gp.xml');
		$this->put('Practitioner/gp-3', $source);
		$this->get('Practitioner/gp-3');
		$this->assertXmlEquals($source);
	}

	public function testDelete()
	{
		$source = file_get_contents(__DIR__ . '/files/Gp.xml');
		$this->post('Practitioner', $source, array('Category' => services\Gp::getOeFhirProfile() . "; scheme=http://hl7.org/fhir/tag/profile"));
		$this->delete(preg_replace('|/_history/.*$|', '', $this->response->getLocation()));
		$this->assertResponseCode(204);
	}

	public function testCreate()
	{
		$source = file_get_contents(__DIR__ . '/files/Gp.xml');
		$this->post('Practitioner', $source, array('Category' => services\Gp::getOeFhirProfile() . "; scheme=http://hl7.org/fhir/tag/profile"));
		$this->get($this->response->getLocation());
		$this->assertXmlEquals($source);
	}

	public function testSearchByIdFound()
	{
		$this->get('Practitioner?_id=gp-1&_profile=' . urlencode(services\Gp::getOeFhirProfile()));
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathEquals($this->client->getBaseUrl(), 'string(./atom:link[@rel="base"]/@href)');
		$this->assertUrlEquals(
			$this->client->getBaseUrl() . '/Practitioner?_id=gp-1&_profile=' . urlencode(services\Gp::getOeFhirProfile()),
			$this->xPathEval('string(./atom:link[@rel="self"]/@href)')
		);
		$this->assertXPathCount(1, './atom:entry');
		$this->assertXPathEquals($this->client->getBaseUrl() . '/Practitioner/gp-1', 'string(./atom:entry/atom:id/text())');
		$this->assertXPathEquals('Practitioner', 'local-name(./atom:entry/atom:content/*)');
	}

	public function testSearchByIdNotFound()
	{
		$this->get('Practitioner?_id=gp-666666&_profile=' . urlencode(services\Gp::getOeFhirProfile()));
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathEquals($this->client->getBaseUrl(), 'string(./atom:link[@rel="base"]/@href)');
		$this->assertUrlEquals(
			$this->client->getBaseUrl() . '/Practitioner?_id=gp-666666&_profile=' . urlencode(services\Gp::getOeFhirProfile()),
			$this->xPathEval('string(./atom:link[@rel="self"]/@href)')
		);
		$this->assertXPathCount(0, './atom:entry');
	}

	public function testSearchByGnc()
	{
		$this->get('Practitioner?identifier=QRST&_profile=' . urlencode(services\Gp::getOeFhirProfile()));
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathEquals($this->client->getBaseUrl(), 'string(./atom:link[@rel="base"]/@href)');
		$this->assertUrlEquals(
			$this->client->getBaseUrl() . '/Practitioner?identifier=QRST&_profile=' . urlencode(services\Gp::getOeFhirProfile()),
			$this->xPathEval('string(./atom:link[@rel="self"]/@href)')
		);
		$this->assertXPathCount(1, './atom:entry');
		$this->assertXPathEquals($this->client->getBaseUrl() . '/Practitioner/gp-2', 'string(./atom:entry/atom:id/text())');
		$this->assertXPathEquals('Practitioner', 'local-name(./atom:entry/atom:content/*)');
	}
}
