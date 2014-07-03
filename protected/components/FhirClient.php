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

require_once('Zend/Http/Client.php');

/**
 * Low-level FHIR client
 */
class FhirClient extends CApplicationComponent
{
	public $servers;
	public $http_client;

	public function init()
	{
		$this->http_client = new Zend_Http_Client;
		$this->http_client->setHeaders('Accept', 'application/xml');
	}

	/**
	 * @param string $url
	 * @param string $method
	 * @param string $body
	 * @return FhirResponse
	 */
	public function request($url, $method = 'GET', $body = null)
	{
		$this->http_client->setUri($url);
		$this->http_client->setMethod($method);
		if ($body) {
			$this->http_client->setRawData($body, 'application/xml+fhir; charset=utf-8');
		}
		$response = $this->http_client->request();
		$this->http_client->resetParameters();

		if (($body = $response->getBody())) {
			$use_errors = libxml_use_internal_errors(true);

			$value = Yii::app()->fhirMarshal->parseXml($body);

			$errors = libxml_get_errors();
			libxml_use_internal_errors($use_errors);

			if ($errors) {
				throw new Exception("Error parsing XML response from {$method} to {$url}: " . print_r($errors, true));
			}
		} else {
			$value = null;
		}

		return new FhirResponse($response->getStatus(), $value);
	}
}
