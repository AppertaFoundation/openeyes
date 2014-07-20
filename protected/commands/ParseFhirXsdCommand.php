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


/**
 * Parse the FHIR schema in XSD format and turn it into JSON data
 */
class ParseFhirXsdCommand extends CConsoleCommand
{
	public function getOptionHelp()
	{
		return array('<schema path>');
	}

	public function run($args)
	{
		if (count($args) != 1) {
			$this->usageError("Please supply the path to fhir-single.xsd");
		}

		$output_dir = Yii::app()->basePath . "/components/fhir_schema";
		system("mkdir -p " . escapeshellarg($output_dir));

		$doc = new DOMDocument;
		$doc->load($args[0]);

		$xpath = new DOMXPath($doc);
		$xpath->registerNamespace("xs", "http://www.w3.org/2001/XMLSchema");

		$types = array();

		foreach ($xpath->query('xs:complexType') as $complexType) {
			$type = $complexType->getAttribute("name");
			$types[$type] = array();

			$base = $xpath->evaluate('string(.//xs:extension/@base)', $complexType);
			if ($base && isset($types[$base])) {
				$types[$type] = $types[$base];
			}

			foreach ($xpath->query('.//*[@maxOccurs]', $complexType) as $item) {
				$plural = ($item->getAttribute("maxOccurs") != "1");

				if ($item->tagName == 'xs:element') {
					$elements = array($item);
				} else {
					$elements = $xpath->query('.//xs:element', $item);
				}

				foreach ($elements as $element) {
					$el_name = $element->getAttribute("name") ?: $element->getAttribute("ref");
					$el_type = $element->getAttribute("type") ?: $element->getAttribute("ref");

					$types[$type][$el_name] = array(
						'type' => $el_type,
						'plural' => $plural,
					);
				}
			}

			file_put_contents("$output_dir/{$type}.json", json_encode($types[$type], JSON_FORCE_OBJECT));
		}
	}
}
