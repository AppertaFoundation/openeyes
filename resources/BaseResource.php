<?php namespace OEModule\PASAPI\resources;

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

abstract class BaseResource
{

    static protected $resource_type;

    public $warnings = array();
    public $errors = array();

    static protected function getSchema($version)
    {
        $type = static::$resource_type;

        return json_decode(file_get_contents(implode(DIRECTORY_SEPARATOR, array(__DIR__, "..", "components", "schemas", $version, "{$type}.json"))), true);
    }

    /**
     * @param $errors array
     * @return static
     */
    static protected function errorInit($errors)
    {
        $obj = new static();
        \OELog::log(var_dump($errors));
        foreach ($errors as $error)
            $obj->addError($error);

        return $obj;
    }

    /**
     * @param $version
     * @param $xml
     * @return null|BaseResource
     * @throws \Exception
     */
    static public function fromXml($version, $xml)
    {
        $doc = new \DOMDocument();

        if (!$xml) return static::errorInit(array("Missing Resource Body"));
        libxml_use_internal_errors(true);
        if (!$doc->loadXML($xml)) {
            $errors = array();
            foreach (libxml_get_errors() as $err) {
                $errors[] = $err->message;
            }
            $obj = static::errorInit($errors);
            libxml_clear_errors();
            return $obj;
        }

        return static::fromXmlDom($version, $doc->documentElement);
    }

    static public function fromXmlDom($version, $element)
    {
        if ($element->tagName != static::$resource_type) {
            throw new \Exception("Mismatched root tag {$element->tagName} for resource type " . static::$resource_type);
        }

        $obj = new static();

        $obj->parseXml($version, $element);

        return $obj;
    }

    public function parseXml($version, $root)
    {
        $schema = static::getSchema($version);

        if (!$schema)
            throw new \Exception("Schema not found for resource " . static::$resource_type);

        foreach ($root->childNodes as $child) {
            if (!$child instanceof \DOMElement) continue;
            $local_name = preg_replace('/^.*:/', '', $child->tagName);
            if (!isset($schema[$local_name])) throw new \Exception("Unrecognised tag {$local_name}");

            switch ($schema[$local_name]['type'])
            {
                case 'list':
                    $this->{$local_name} = array();
                    foreach ($child->childNodes as $list_item) {
                        if (!$list_item instanceof \DOMElement) continue;
                        $cls = __NAMESPACE__ . "\\" . $schema[$local_name]['resource'];
                        $this->{$local_name}[] = $cls::fromXmlDom($version, $list_item);
                    }
                    break;
                case 'date':
                    if (!strlen($child->textContent))
                        break;

                    if ($date = \DateTime::createFromFormat('Y-m-d', $child->textContent)) {
                        $this->{$local_name} = $date->format('Y-m-d H:i:s');
                    }
                    else {
                        throw new \Exception("invalid date format for {$local_name}");
                    }
                    break;
                default:
                    $this->{$local_name} = $child->textContent;
            }
        }
    }

    protected function addError($msg)
    {
        $this->errors[] = $msg;
    }

    protected function addWarning($msg)
    {
        $this->warnings[] = $msg;
    }

}