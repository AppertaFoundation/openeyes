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

class FhirMarshal extends CApplicationComponent
{
	public $schemas = array();

	/**
	 * Fetch the schema for the specified FHIR type as a PHP array
	 *
	 * @param string $type
	 * @return array
	 */
	public function getSchema($type)
	{
		if (!isset($this->schemas[$type])) {
			$this->schemas[$type] = json_decode(file_get_contents(__DIR__ . "/fhir_schema/{$type}.json"), true);
		}
		return $this->schemas[$type];
	}

	/**
	 * Check whether a FHIR type is part of the standard or a custom OE type
	 *
	 * @param string $type
	 * @return boolean
	 */
	public function isStandardType($type)
	{
		return file_exists(__DIR__ . "/fhir_schema/{$type}.json");
	}

	/**
	 * @param string $text
	 * @return StdClass|null
	 */
	public function parseJson($text)
	{
		return json_decode($text);
	}

	/**
	 * @param string $text
	 * @return StdClass|null
	 */
	public function parseXml($text)
	{
		$doc = new DOMDocument;
		if (!$doc->loadXML($text)) return null;

		$root = $doc->documentElement;
		if ($root->tagName == 'feed') {
			$obj = $this->parseXmlBundle($doc, $root);
			$obj->resourceType = "Bundle";
		} else {
			$obj = $this->parseXmlRecursive($doc, $root, $root->tagName, $this->getSchema($root->tagName));
			$obj->resourceType = $root->tagName;
		}

		return $obj;
	}

	private function parseXmlRecursive(DOMDocument $doc, DOMElement $element, $type, array $schema)
	{
		$obj = new StdClass;

		if ($element->hasAttribute("id")) {
			$obj->id = $element->getAttribute("id");
		}

		switch ($type) {
			case 'Binary':
				$obj->contentType = $element->getAttribute('contentType');
				$obj->content = $element->textContent;
				return $obj;
				break;
			case 'Extension':
				$obj->url = $element->getAttribute('url');
				break;
		}

		foreach ($element->childNodes as $child) {
			if (!$child instanceof DOMElement) continue;

			$name = $child->tagName;

			if ($type == 'Narrative' && $name == 'div') {
				$obj->div = $doc->saveXML($child);
				continue;
			}

			$child_type = $schema[$name]['type'];
			$child_schema = $this->getSchema($child_type);

			if ($type == 'Resource.Inline') {
				$obj = $this->parseXmlRecursive($doc, $child, $child_type, $child_schema);
				$obj->resourceType = $child_type;
				return $obj;
			}

			$is_primitive = (count($child_schema) == 1);  // the only sub-element a primitive type can have is extension

			if ($is_primitive) {  // of course primitive types require far more complex processing
				if ($child->hasAttribute("value")) {
					$value = $child->getAttribute("value");
					switch($child_type) {
						case 'integer':
							$value = (int) $value;
							break;
						case 'boolean':
							$value = ($value == 'false') ? false : (bool) $value;
							break;
					}
				} else {
					$value = null;
				}

				if ($child->hasAttribute("id") || $child->hasChildNodes()) {
					$extra = $this->parseXmlRecursive($doc, $child, $child_type, $child_schema);
				} else {
					$extra = null;
				}
			} else {
				$value = $this->parseXmlRecursive($doc, $child, $child_type, $child_schema);
			}

			if ($schema[$name]['plural']) {
				$obj->{$name}[] = $value;
				if ($is_primitive) $obj->{"_$name"}[] = $extra;
			} else {
				if (!is_null($value)) $obj->$name = $value;
				if ($is_primitive && $extra) $obj->{"_$name"} = $extra;
			}
		}

		// Tidy up - remove any unnecessary _properties we've added
		foreach ($obj as $name => $value) {
			if ($name[0] == '_' && is_array($value) && !array_filter($value)) {
				unset($obj->$name);
			}
		}

		return $obj;
	}

	public function parseXmlBundle(DOMDocument $doc, DOMElement $element)
	{
		$obj = new StdClass;

		foreach ($element->childNodes as $child) {
			if (!$child instanceof DOMElement) continue;

			switch ($child->tagName) {
				case 'link':
					$link = new StdClass;
					foreach ($child->attributes as $name => $value) {
						$link->$name = $value;
					}
					$obj->link[] = $link;
					break;
				case 'title':
				case 'id':
				case 'updated':
				case 'name':
				case 'uri':
				case 'div':
					$obj->{$child->tagName} = $child->textContent;
					break;
				case 'author':
				case 'content':
				case 'summary':
					$obj->{$child->tagName} = $this->parseXmlBundle($doc, $child);
					break;
				case 'entry':
					$obj->entry[] = $this->parseXmlBundle($doc, $child);
					break;
				case 'Signature':
					$obj->signature = $doc->saveXml($child);
					break;
				default:
					$obj = $this->parseXmlRecursive($doc, $child, $child->tagName, $this->getSchema($child->tagName));
					$obj->resourceType = $child->tagName;
					return $obj;
					break;
			}
		}

		return $obj;
	}

	/**
	 * @param StdClass $resource
	 * @return string
	 */
	public function renderJson(StdClass $resource)
	{
		return json_encode($resource);
	}

	/**
	 * @param StdClass $resource
	 * @return string
	 */
	public function renderXml(StdClass $resource)
	{
		$doc = new DOMDocument("1.0", "utf-8");

		if ($resource->resourceType == "Bundle") {  // stupid special case
			$root = $doc->createElementNs("http://www.w3.org/2005/Atom", "feed");
			$doc->appendChild($root);
			$this->renderXmlBundle($resource, $doc, $root);
		} else {
			$this->renderXmlRecursive($resource, $doc, $doc);
		}

		if (YII_DEBUG) $doc->formatOutput = true;
		return $doc->saveXML();
	}

	private function renderXmlRecursive(StdClass $data, DOMDocument $doc, DOMNode $parent)
	{
		if (isset($data->resourceType)) {
			$is_resource = true;

			$root = $doc->createElement($data->resourceType);
			$root->setAttribute("xmlns", "http://hl7.org/fhir");
			$parent->appendChild($root);
			$parent = $root;

			if (isset($data->text)) {
				$text = $doc->createElement('text');
				$status = $doc->createElement('status');
				$status->setAttribute('value', $data->text->status);
				$div = $doc->createDocumentFragment();
				$div->appendXML($data->text->div);
				$text->appendChild($status);
				$text->appendChild($div);
				$parent->appendChild($text);
				$text->removeAttributeNS('http://www.w3.org/1999/xhtml', 'default');  // I don't know why libxml does this
			}
		} else {
			$is_resource = false;
		}

		foreach ($data as $name => $value) {
			if ($is_resource && in_array($name, array('resourceType', 'text'))) continue;

			if ($name == 'id') {
				$parent->setAttribute('id', $value);
				continue;
			}

			if ($parent->tagName == 'extension' && $name == 'url') {
				$parent->setAttribute('url', $value);
				continue;
			}

			if ($parent->tagName == 'Binary') {
				switch ($name) {
					case 'contentType':
						$parent->setAttribute('contentType', $value);
						break;
					case 'content':
						$parent->appendChild($doc->createTextNode($value));
						break;
				}
				continue;
			}

			if ($name[0] == '_') {
				$name = substr($name, 1);
				if (isset($data->$name)) continue;
				$extra = $value;
				$value = is_array($value) ? array() : null;
			} else {
				if (isset($data->{"_$name"})) {
					$extra = $data->{"_$name"};
				} else {
					$extra = is_array($value) ? array() : null;
				}
			}

			$values = is_array($value) ? array_values($value) : array($value);
			$extras = is_array($extra) ? array_values($extra) : array($extra);

			$n = max(count($values), count($extras));

			for ($i = 0; $i < $n; $i++) {
				$value = isset($values[$i]) ? $values[$i] : null;
				$extra = isset($extras[$i]) ? $extras[$i] : null;

				$el = $doc->createElement($name);
				$parent->appendChild($el);

				if (is_object($value)) {
					$this->renderXmlRecursive($value, $doc, $el);
				} else if (!is_null($value)) {
					switch (gettype($value)) {
						case 'boolean':
							$value = $value ? 'true' : 'false';
							break;
					}
					$el->setAttribute("value", $value);
				}

				if ($extra) $this->renderXmlRecursive($extra, $doc, $el);
			}
		}
	}

	private function renderXmlBundle(StdClass $data, DOMDocument $doc, DOMNode $parent)
	{
		foreach ($data as $name => $value) {
			if ($name == "resourceType") continue;

			$values = is_array($value) ? $value : array($value);

			foreach ($values as $value) {
				$el = $doc->createElement($name);
				switch ($name) {
					case 'link': // value contains attributes
					case 'category':
						foreach ($value as $attrName => $attrValue) {
							$el->setAttribute($attrName, $attrValue);
						}
						break;
					case 'title': // value is text content
					case 'id':
					case 'updated':
						$el->appendChild($doc->createTextNode($value));
						$parent->appendChild($el);
						break;
					case 'entry': // recur
						$this->renderXmlBundle($value, $doc, $el);
						break;
					case 'content': // yeah, let's get out of here!
						$el->setAttribute("type", "text/xml");
						$this->renderXmlRecursive($value, $doc, $el);
						break;
					default:
						throw new Exception("Unexpected item name found in Bundle: '{$name}'");
				}
				$parent->appendChild($el);
			}
		}
	}
}
