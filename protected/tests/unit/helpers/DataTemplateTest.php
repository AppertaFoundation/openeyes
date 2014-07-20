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

class DataTemplateTest extends PHPUnit_Framework_TestCase
{
	public function generateDataProvider()
	{
		return $this->getTemplates('generate-match');
	}

	public function matchDataProvider()
	{
		return array_merge($this->getTemplates('generate-match'), $this->getTemplates('match-only'));
	}

	public function matchFailureDataProvider()
	{
		return $this->getTemplates('match-failure');
	}

	private function getTemplates($dir)
	{
		$data = array();

		foreach (glob(__DIR__ . '/' . __CLASS__ . "/{$dir}/*.template.json") as $template_path) {
			preg_match('|([^/]+)\.template\.json$|', $template_path, $m);
			$name = $m[1];

			$structure_path = str_replace('.template.json', '.structure.json', $template_path);
			$values_path = str_replace('.template.json', '.values.json', $template_path);

			$data[] = array(
				$name,
				DataTemplate::fromJsonFile($template_path),
				json_decode(file_get_contents($structure_path)),
				file_exists($values_path) ? json_decode(file_get_contents($values_path), true) : array(),
			);
		}

		return $data;
	}

	/**
	 * @dataProvider generateDataProvider
	 */
	public function testGenerate($name, $template, $structure, $values)
	{
		$this->assertEquals($structure, $template->generate($values));
	}

	/**
	 * @dataProvider matchDataProvider
	 */
	public function testMatch($name, $template, $structure, $values)
	{
		$this->assertEquals($values, $template->match($structure));
	}

	/**
	 * @dataProvider matchFailureDataProvider
	 */
	public function testMatchFailure($name, $template, $structure)
	{
		$this->assertNull($template->match($structure));
	}
}
