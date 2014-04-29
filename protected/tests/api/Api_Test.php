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

class Api_Test extends FhirTestCase
{
	public function testNotAcceptable()
	{
		$this->setExpectedHttpError(406);
		$this->get('Patient/17885', array('Accept' => 'foo/bar'));
	}

	public function testDefaultXml()
	{
		$this->get('Patient/17885', array('Accept' => '*/*'));
		$this->assertNotNull($this->doc);
	}

	public function testFormatParam()
	{
		$this->get('Patient/17885?_format=json', array('Accept' => ''));
		$this->assertEquals(ApiController::JSON_MIMETYPE, $this->response->getContentType());
	}

	public function testAcceptHeader()
	{
		$this->get('Patient/17885', array('Accept' => 'application/json'));
		$this->assertEquals(ApiController::JSON_MIMETYPE, $this->response->getContentType());
	}

	public function instanceLevelPostInvalid()
	{
		$this->setExpectedHttpError(400);
		$this->post('Patient/1');
	}

	public function testCreateByPutInvalid()
	{
		$this->setExpectedHttpError(405);  // from spec: http://hl7.org/implement/standards/fhir/http.html#update
		$this->put('Patient/foo-10000', '');
	}

	public function testTypeLevelPutInvalid()
	{
		$this->setExpectedHttpError(400);
		$this->put('Patient', '');
	}

	public function testReadInvalidResourceType()
	{
		$this->setExpectedHttpError(404);
		$this->get('Avocado/avo-1234');
	}

	public function testReadInvalidPrefix()
	{
		$this->setExpectedHttpError(404);
		$this->get('Patient/avo-1234');
	}

	public function testReadNotFound()
	{
		$this->setExpectedHttpError(404);
		$this->get('Patient/1');
	}

	public function testReadHeaders()
	{
		$this->get('Patient/17885');
		$this->assertRegexp('|^' . preg_quote($this->client->getBaseUrl(), '|') . '/Patient/17885/_history/\d+$|', $this->response->getContentLocation());
		$this->assertInstanceOf('DateTime', DateTime::createFromFormat(DATE_RFC1123, $this->response->getLastModified()));
		$this->assertRegexp('/^"\d+"$/', $this->response->getETag());
		$this->assertEquals(
			'http://openeyes.org.uk/fhir/' . Yii::app()->version->coreVersion . '/profile/Patient; scheme=http://hl7.org/fhir/tag/profile',
			$this->response->getHeader('Category')
		);
	}

	public function testReadNotModified()
	{
		$this->get('Patient/17885');
		$this->get('Patient/17885', array('If-Modified-Since' => $this->response->getLastModified()));
		$this->assertEquals(304, $this->response->getStatusCode());
		$this->assertEquals(0, $this->response->getContentLength());
	}

	public function testReadModified()
	{
		$this->get('Patient/17885', array('If-Modified-Since' => date(DATE_RFC1123, 0)));
		$this->assertEquals(200, $this->response->getStatusCode());
		$this->assertNotNull($this->doc);
	}

	public function testReadETagMatch()
	{
		$this->get('Patient/17885');
		$this->get('Patient/17885', array('If-None-Match' => $this->response->getETag()));
		$this->assertEquals(304, $this->response->getStatusCode());
		$this->assertEquals(0, $this->response->getContentLength());
	}

	public function testReadETagMatchWildcard()
	{
		$this->get('Patient/17885');
		$this->get('Patient/17885', array('If-None-Match' => '*'));
		$this->assertEquals(304, $this->response->getStatusCode());
		$this->assertEquals(0, $this->response->getContentLength());
	}

	public function testReadETagMatchList()
	{
		$this->get('Patient/17885');
		$this->get('Patient/17885', array('If-None-Match' => "\"0\", {$this->response->getETag()}, \"1\""));
		$this->assertEquals(304, $this->response->getStatusCode());
		$this->assertEquals(0, $this->response->getContentLength());
	}

	public function testReadEtagNoMatch()
	{
		$this->get('Patient/17885', array('If-None-Match' => '"0"'));
		$this->assertEquals(200, $this->response->getStatusCode());
		$this->assertNotNull($this->doc);
	}

	public function testReadEtagNoMatchList()
	{
		$this->get('Patient/17885', array('If-None-Match' => '"0", "1", "2"'));
		$this->assertEquals(200, $this->response->getStatusCode());
		$this->assertNotNull($this->doc);
	}

	public function testReadEtagMatchNotModified()
	{
		$this->get('Patient/17885');
		$this->get('Patient/17885', array('If-None-Match' => $this->response->getETag(), 'If-Modified-Since' => $this->response->getLastModified()));
		$this->assertEquals(304, $this->response->getStatusCode());
		$this->assertEquals(0, $this->response->getContentLength());
	}

	public function testReadEtagMatchModified()
	{
		$this->get('Patient/17885');
		$this->get('Patient/17885', array('If-None-Match' => $this->response->getETag(), 'If-Modified-Since' => date(DATE_RFC1123, 0)));
		$this->assertEquals(200, $this->response->getStatusCode());
		$this->assertNotNull($this->doc);
	}

	public function testReadEtagNoMatchNotModified()
	{
		$this->get('Patient/17885');
		$this->get('Patient/17885', array('If-None-Match' => '"0"', 'If-Modified-Since' => $this->response->getLastModified()));
		$this->assertEquals(200, $this->response->getStatusCode());
		$this->assertNotNull($this->doc);
	}

	public function testReadEtagNoMatchModified()
	{
		$this->get('Patient/17885', array('If-None-Match' => '"0"', 'If-Modified-Since' => date(DATE_RFC1123, 0)));
		$this->assertEquals(200, $this->response->getStatusCode());
		$this->assertNotNull($this->doc);
	}

	public function testVReadWithCurrentVersion()
	{
		$this->get('Patient/17885');
		$doc = clone($this->doc);
		$this->get($this->response->getContentLocation());
		$this->assertEquals($doc, $this->doc);
	}

	public function testVReadWithPreviousVersion()
	{
		$this->setExpectedHttpError(405);
		$this->get('Patient/17885/_history/0');
	}

	public function testUpdateHeaders()
	{
		$this->get('Patient/17885');
		$this->put('Patient/17885', $this->doc);

		$this->assertRegexp('|^' . preg_quote($this->client->getBaseUrl(), '|') . '/Patient/17885/_history/\d+$|', $this->response->getLocation());
		$this->assertRegexp('|^' . preg_quote($this->client->getBaseUrl(), '|') . '/Patient/17885/_history/\d+$|', $this->response->getContentLocation());
		$this->assertInstanceOf('DateTime', DateTime::createFromFormat(DATE_RFC1123, $this->response->getLastModified()));
		$this->assertRegexp('/^"\d+"$/', $this->response->getETag());
	}

	public function testUpdateContentLocationCheckFail()
	{
		$this->get('Patient/17885');
		$this->setExpectedHttpError(409);
		$this->put('Patient/17885', $this->doc, array('Content-Location' => "{$this->client->getBaseUrl()}/Patient/17885/_history/0"));
	}

	public function testUpdateContentLocationCheckSucceed()
	{
		$this->get('Patient/17885');
		$this->put('Patient/17885', $this->doc, array('Content-Location' => $this->response->getContentLocation()));
		$this->assertEquals(200, $this->response->getStatusCode());
	}

	public function testUpdateUnmodifiedCheckFail()
	{
		$this->get('Patient/17885');
		$this->setExpectedHttpError(412);
		$this->put('Patient/17885', $this->doc, array('If-Unmodified-Since' => date(DATE_RFC1123, 0)));
	}

	public function testUpdateUnmodifiedCheckSucceed()
	{
		$this->get('Patient/17885');
		$this->put('Patient/17885', $this->doc, array('If-Unmodified-Since' => $this->response->getLastModified()));
		$this->assertEquals(200, $this->response->getStatusCode());
	}

	public function testUpdateIfMatchCheckFail()
	{
		$this->get('Patient/17885');
		$this->setExpectedHttpError(412);
		$this->put('Patient/17885', $this->doc, array('If-Match' => '0'));
	}

	public function testUpdateIfMatchCheckSucceed()
	{
		$this->get('Patient/17885');
		$this->put('Patient/17885', $this->doc, array('If-Match' => $this->response->getETag()));
		$this->assertEquals(200, $this->response->getStatusCode());
	}

	public function testSearchWithPost()
	{
		$this->post('Patient/_search', '_id=1');
		$this->assertXPathEquals('feed', 'local-name()');
	}

	public function testSearchCount()
	{
		$this->get('Patient?_count=1');
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathCount(1, './atom:entry');
	}

	public function testSearchInvalidResourceType()
	{
		$this->setExpectedHttpError(404);
		$this->get('Avocado');
	}

	public function testSearchRequiredProfileNotSupplied()
	{
		$this->setExpectedHttpError(422);
		$this->get('Practitioner');
	}

	public function testSearchProfileDoesntMatch()
	{
		$this->get('Patient?_profile=' . urlencode('http://example.com/profile/Patient'));
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathCount(0, './atom:entry');
	}

	public function testSearchRequiredProfileDoesntMatch()
	{
		$this->get('Practitioner?_profile=' . urlencode('http://example.com/profile/Practitioner'));
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathCount(0, './atom:entry');
	}

	public function testSearchProfileDoesMatch()
	{
		$this->get('Patient?_count=1&_profile=' . urlencode(services\Patient::getOeFhirProfile()));
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathCount(1, './atom:entry');
	}

	public function testSearchRequiredProfileDoesMatch()
	{
		$this->get('Practitioner?_count=1&_profile=' . urlencode(services\Gp::getOeFhirProfile()));
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathCount(1, './atom:entry');
	}

	public function testSearchByIdInvalidIdFormat()
	{
		$this->get('Organization?_id=foobar-1&_profile=' . urlencode(services\Practice::getOeFhirProfile()));
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathCount(0, './atom:entry');
	}

	public function testSearchByIdProfileDoesntMatchIdPrefix()
	{
		$this->get('Organization?_id=cb-1&_profile=' . urlencode(services\Practice::getOeFhirProfile()));
		$this->assertXPathEquals('feed', 'local-name()');
		$this->assertXPathCount(0, './atom:entry');
	}

	public function testCantSearchByIdDirectly()
	{
		$this->get('Practitioner?id=1&_profile=' . urlencode(services\Gp::getOeFhirProfile()));
		$this->assertXPathEquals($this->client->getBaseUrl() . '/Practitioner?_profile=' . urlencode(services\Gp::getOeFhirProfile()), 'string(./atom:link[@rel="self"]/@href)');
		$this->assertXPathFound('./atom:entry/atom:id[text()!="' . $this->client->getBaseUrl() . '/Practitioner/gp-1"]');
	}

	public function testSearchUrls()
	{
		$url = 'Practitioner?_profile=' . urlencode(services\Gp::getOeFhirProfile());
		$this->get($url);
		$this->assertXPathEquals($this->client->getBaseUrl(), 'string(./atom:link[@rel="base"]/@href)');
		$this->assertXPathEquals("{$this->client->getBaseUrl()}/{$url}", 'string(./atom:link[@rel="self"]/@href)');
		$this->assertXPathRegexp('|^' . preg_quote($this->client->getBaseUrl()) . '/Practitioner/gp-\d+$|', 'string(./atom:entry[1]/atom:id/text())');
		$this->assertXPathRegexp('|^' . preg_quote($this->client->getBaseUrl()) . '/Practitioner/gp-\d+/_history/\d+$|', 'string(./atom:entry[1]/atom:link[@rel="self"]/@href)');
	}

	public function testSearchCategories()
	{
		$url = 'Practitioner?_profile=' . urlencode(services\Gp::getOeFhirProfile());
		$this->get($url);
		$this->assertXPathEquals(
			'http://openeyes.org.uk/fhir/' . Yii::app()->version->coreVersion . '/profile/Practitioner/Gp',
			'string(./atom:entry[1]/atom:category[@scheme="http://hl7.org/fhir/tag/profile"]/@term)'
		);
	}

	public function testCreateHeaders()
	{
		$source = file_get_contents(__DIR__ . '/files/Patient.xml');
		$this->post('Patient', $source);
		$this->assertRegexp('|^' . preg_quote($this->client->getBaseUrl(), '|') . '/Patient/\d+/_history/\d+$|', $this->response->getLocation());
		$this->assertRegexp('/^"\d+"$/', $this->response->getETag());
	}

	public function testCreateUnknownResourceType()
	{
		$this->setExpectedHttpError(404);
		$this->post('Walrus', file_get_contents(__DIR__ . '/files/Gp.xml'));
	}

	public function testCreateRequiredProfileNotSupplied()
	{
		$this->setExpectedHttpError(422);
		$this->post('Practitioner', file_get_contents(__DIR__ . '/files/Gp.xml'));
	}

	public function testCreateRequiredProfileDoesntMatch()
	{
		$this->setExpectedHttpError(422);
		$this->post('Practitioner', file_get_contents(__DIR__ . '/files/Gp.xml'), array('Category' => 'http://example.com/profile/Practitioner; scheme=http://hl7.org/fhir/tag/profile'));
	}

	public function testCreateNonRequiredProfileDoesntMatch()
	{
		// This is ok, they can claim to support any profile they like
		$this->post('Patient', file_get_contents(__DIR__ . '/files/Patient.xml'), array('Category' => 'http://example.com/profile/Patient; scheme=http://hl7.org/fhir/tag/profile'));
	}

	public function testCreateWrongResourceType()
	{
		$this->setExpectedHttpError(422);
		$this->post('Patient', file_get_contents(__DIR__ . '/files/Gp.xml'));
	}

	public function testConformanceWithGet()
	{
		$this->get('metadata');
	}

	public function testConformanceWithOptions()
	{
		$this->request('OPTIONS', '');
	}
}
