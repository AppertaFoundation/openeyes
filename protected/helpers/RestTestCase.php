<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class RestTestCase extends OEDbTestCase
{
    protected static $schema = null;
    protected static $namespaces = array();

    protected $client;

    protected $response;
    protected $doc = null;

    /**
     * Flag as to whether error responses should be captured or the exception thrown.
     *
     * @var bool
     */
    protected $capture_error_responses = false;
    protected $expected_response_code = 200;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = new Client(
            [
                'base_uri' => Yii::app()->params['rest_test_base_url'],
                'auth' => ['api', 'password'],
                'headers' => [
                    'Accept' => 'application/xml',
                ]
            ]
        );


    }

    protected function setExpectedHttpError($code)
    {
        if (!$this->capture_error_responses) {
            $this->expectException(BadResponseException::class, "[status code] {$code}");
        }
        $this->expected_response_code = $code;
    }

    protected function get($url, $headers = null)
    {
        return $this->request('GET', $url, null, $headers);
    }

    protected function post($url, $body, $headers = null)
    {
        return $this->request('POST', $url, $body, $headers);
    }

    protected function put($url, $body, $headers = null)
    {
        return $this->request('PUT', $url, $body, $headers);
    }

    protected function delete($url, $headers = null)
    {
        return $this->request('DELETE', $url, null, $headers);
    }

    protected function request($method, $url, $body = null, $headers = null)
    {
        if ($body instanceof DOMDocument) {
            $body = $body->saveXML();
        }

        try {
            $this->response = $this->client->request(
                $method,
                $url,
                [
                    RequestOptions::HEADERS => $headers,
                    RequestOptions::BODY => $body
                ]
            );
        } catch (ClientException $e) {
            if (!$this->capture_error_responses) {
                throw $e;
            }
            $this->response = $e->getResponse();
        } catch (ServerException $e) {
            if (!$this->capture_error_responses) {
                throw $e;
            }
            $this->response = $e->getResponse();
        }

        if ((strpos($this->response->getHeader('content-type')[0], 'xml') !== false) && !empty((string) $this->response->getBody())) {
            $this->doc = new DOMDocument();
            $this->doc->loadXML($this->response->getBody());
            if (static::$schema) {
                $this->assertTrue($this->doc->schemaValidate(Yii::app()->getBasePath().'/'.static::$schema));
            }
        }

        $this->assertEquals(
            $this->expected_response_code,
            $this->response->getStatusCode(),
            "Incorrect status code " . $this->response->getStatusCode() . " has body response:" . $this->response->getBody()
        );
    }

    protected function assertXmlEquals($xml)
    {
        if (is_string($xml)) {
            $doc = new DOMDocument();
            $doc->loadXML($xml);
        } else {
            $doc = $xml;
        }
        $this->assertEquals($doc, $this->doc);
    }

    protected function assertResponseCode($code)
    {
        $this->assertEquals($code, $this->response->getStatusCode());
    }

    protected function xPathQuery($path)
    {
        return $this->getXPath()->query($path);
    }

    protected function xPathEval($path)
    {
        return $this->getXPath()->evaluate($path);
    }

    protected function assertXPathFound($path)
    {
        $this->assertGreaterThan(0, $this->xpathQuery($path)->length);
    }

    protected function assertXPathCount($count, $path)
    {
        $this->assertEquals($count, $this->xPathQuery($path)->length);
    }

    protected function assertXPathEquals($expected, $path)
    {
        $this->assertEquals($expected, $this->xPathEval($path));
    }

    protected function assertXPathRegExp($pattern, $path)
    {
        $this->assertMatchesRegularExpression($pattern, $this->xPathEval($path));
    }

    protected function assertUrlEquals($expected, $actual)
    {
        $expected = parse_url($expected);
        if (isset($expected['query'])) {
            parse_str($expected['query'], $expected['query']);
        }

        $actual = parse_url($actual);
        if (isset($actual['query'])) {
            parse_str($actual['query'], $actual['query']);
        }

        $this->assertEquals($expected, $actual);
    }

    private function getXPath()
    {
        $xpath = new DOMXPath($this->doc);
        foreach (static::$namespaces as $prefix => $namespace) {
            $xpath->registerNamespace($prefix, $namespace);
        }

        return $xpath;
    }
}
