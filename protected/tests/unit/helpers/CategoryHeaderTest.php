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

class CategoryHeaderTest extends PHPUnit_Framework_TestCase
{
	public function parseDataProvider()
	{
		return array(
			// Examples taken from the RFC
			array('dog', array('' => array('dog'))),
			array(
				'dog; label="Canine"; scheme="http://purl.org/net/animals"',
				array('http://purl.org/net/animals' => array('dog')),
			),
			array(
				'dog; label="Canine"; scheme="http://purl.org/net/animals", lowchen; label*=UTF-8\'de\'L%c3%b6wchen; scheme="http://purl.org/net/animals/dogs"',
				array(
					'http://purl.org/net/animals' => array('dog'),
					'http://purl.org/net/animals/dogs' => array('lowchen'),
				),
			),
			// Edge cases
			array(
				'dog; label="Canine"; scheme=http://purl.org/net/animals',
				array('http://purl.org/net/animals' => array('dog')),
			),
			array(
				'dog; label="Canine, Doge"; scheme="http://purl.org/net/animals"',
				array('http://purl.org/net/animals' => array('dog')),
			),
		);
	}

	/**
	 * @dataProvider parseDataProvider
	 */
	public function testParse($header, $categories)
	{
		$cats = CategoryHeader::parse($header);
		foreach ($categories as $scheme => $category) {
			$this->assertEquals($category, $cats->get($scheme));
		}
	}
}
