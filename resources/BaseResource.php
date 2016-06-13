<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\PASAPI\resources;

use OEModule\PASAPI\models\XpathRemap;

abstract class BaseResource
{
    protected static $resource_type;
    protected $version;
    protected $schema;
    private $_audit_data;

    public $warnings = array();
    public $errors = array();

    /**
     * Property that will prevent a model being created if its set to true.
     *
     * @var bool
     */
    public $update_only = false;

    public function __construct($version)
    {
        if (!$version) {
            throw new \Exception('Schema version required to create resource');
        }
        $this->version = $version;
        $this->schema = static::getSchema($version);

        if (!$this->schema) {
            throw new \Exception('Schema not found for resource '.static::$resource_type);
        }
    }

    /**
     * Get the schema for the resource type based on the given version.
     *
     * @param $version
     *
     * @return mixed
     */
    protected static function getSchema($version)
    {
        $type = static::$resource_type;

        return json_decode(file_get_contents(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'components', 'schemas', $version, "{$type}.json"))), true);
    }

    /**
     * Convenience function to create a resource instance with error messages.
     *
     * @param $errors array
     *
     * @return static
     */
    protected static function errorInit($version, $errors)
    {
        $obj = new static($version);

        foreach ($errors as $error) {
            $obj->addError($error);
        }

        return $obj;
    }

    /**
     * Create an instance from an XML string.
     *
     * @param $version
     * @param $xml
     *
     * @return null|BaseResource
     *
     * @throws \Exception
     */
    public static function fromXml($version, $xml)
    {
        $doc = new \DOMDocument();

        if (!$xml) {
            return static::errorInit($version, array('Missing Resource Body'));
        }
        libxml_use_internal_errors(true);
        if (!$doc->loadXML($xml)) {
            $errors = array();
            foreach (libxml_get_errors() as $err) {
                $errors[] = $err->message;
            }
            $obj = static::errorInit($version, $errors);
            libxml_clear_errors();

            return $obj;
        }

        static::remapValues($doc, XpathRemap::model()->findAllByXpath('/'.static::$resource_type));

        $obj = static::fromXmlDom($version, $doc->documentElement);
        $obj->addAuditData('input', \CHtml::encode($xml));

        return $obj;
    }

    /**
     * instantiates a resource with the given XML Document.
     *
     * @param $version
     * @param $element \DOMElement
     *
     * @return static
     *
     * @throws \Exception
     */
    public static function fromXmlDom($version, \DOMElement $element)
    {
        if ($element->tagName != static::$resource_type) {
            return static::errorInit($version, array("Mismatched root tag {$element->tagName} for resource type ".static::$resource_type));
        }

        $obj = new static($version);

        $obj->parseXml($element);

        return $obj;
    }

    /**
     * Update the given doc nodes as per the provided remaps.
     *
     * @param $doc
     * @param XpathRemap[] $remaps
     */
    public static function remapValues($doc, $remaps = array())
    {
        if (count($remaps)) {
            // no point in instantiating DOMXPath if nothing to remap
            $xdoc = new \DOMXPath($doc);
            foreach ($remaps as $remap) {
                if ($el = $xdoc->query($remap->xpath)) {
                    $lookup = array();
                    foreach ($remap->values as $val) {
                        $lookup[$val->input] = $val->output;
                    }
                    // track index for item removal
                    $remove_list = array();

                    for ($i = 0; $i < $el->length; ++$i) {
                        $current = $el->item($i)->textContent;
                        if (array_key_exists($current, $lookup)) {
                            if (is_null($lookup[$current])) {
                                $remove_list[] = $i;
                            } else {
                                $el->item($i)->nodeValue = $lookup[$current];
                            }
                        }
                    }
                    foreach ($remove_list as $remove) {
                        $el->item($remove)->parentNode->removeChild($el->item($remove));
                    }
                }
            }
        }
    }

    /**
     * Parses XML to define resource attributes.
     *
     * @param \DOMElement $root
     *
     * @throws \Exception
     */
    public function parseXml(\DOMElement $root)
    {
        $schema = $this->schema;

        if ($root->hasAttribute('updateOnly')) {
            $update_only = $root->hasAttribute('updateOnly');
            if (in_array(strtolower($update_only), array('1', 'true'))) {
                $this->update_only = true;
            }
        }

        foreach ($root->childNodes as $child) {
            if (!$child instanceof \DOMElement) {
                continue;
            }
            $local_name = preg_replace('/^.*:/', '', $child->tagName);
            if (!isset($schema[$local_name])) {
                throw new \Exception("Unrecognised tag {$local_name}");
            }

            switch ($schema[$local_name]['type']) {
                case 'list':
                    $this->{$local_name} = array();
                    foreach ($child->childNodes as $list_item) {
                        if (!$list_item instanceof \DOMElement) {
                            continue;
                        }
                        $cls = __NAMESPACE__.'\\'.$schema[$local_name]['resource'];
                        $this->{$local_name}[] = $cls::fromXmlDom($this->version, $list_item);
                    }
                    break;
                case 'date':
                    if (!strlen($child->textContent)) {
                        break;
                    }

                    if ($date = \DateTime::createFromFormat('Y-m-d', $child->textContent)) {
                        $this->{$local_name} = $date->format('Y-m-d H:i:s');
                    } else {
                        throw new \Exception("invalid date format for {$local_name}");
                    }
                    break;
                default:
                    $this->{$local_name} = $child->textContent;
            }
        }
    }

    public function getAssignedProperty($name)
    {
        return property_exists($this, $name) ? $this->$name : null;
    }

    /**
     * Base validator of resource from schema definition.
     *
     * @return bool
     */
    public function validate()
    {
        foreach ($this->schema as $tag => $defn) {
            if (@$defn['required']) {
                if (!property_exists($this, $tag)) {
                    $this->addError("{$tag} is required");
                }
            }
        }

        return count($this->errors) === 0;
    }

    /**
     * Convenience wrapper for handling model validation errors.
     *
     * @param $errors
     */
    protected function addModelErrors($errors)
    {
        foreach ($errors as $fld => $field_errors) {
            foreach ($field_errors as $err) {
                $this->addError("{$fld}: {$err}");
            }
        }
    }

    /**
     * Error logger.
     *
     * @param $msg
     */
    protected function addError($msg)
    {
        $this->errors[] = $msg;
    }

    /**
     * Warning logger.
     *
     * @param $msg
     */
    protected function addWarning($msg)
    {
        $this->warnings[] = $msg;
    }

    /**
     * Simple wrapper to retrieve a type for auditing.
     *
     * @return string
     */
    public function getAuditTarget()
    {
        return strtolower(static::$resource_type);
    }

    /**
     * Add to audit data property.
     *
     * @param $key
     * @param $value
     */
    public function addAuditData($key, $value)
    {
        if (isset($this->_audit_data[$key])) {
            $this->_audit_data[$key] = array($this->_audit_data[$key]);
            $this->_audit_data[$key][] = $value;
        } else {
            $this->_audit_data[$key] = $value;
        }
    }

    /**
     * Returns the json encoded audit data for logging.
     *
     * @return string
     */
    public function getAuditData()
    {
        if (!isset($this->_audit_data['resource_id'])) {
            $this->_audit_data['resource_id'] = $this->id;
        }

        return json_encode($this->_audit_data);
    }

    /**
     * Wrapper for auditing calls on resource changes.
     *
     * @param $audit_type
     * @param null  $data
     * @param null  $msg
     * @param array $properties
     *
     * @throws \Exception
     */
    public function audit($audit_type, $data = null, $msg = null, $properties = array())
    {
        if ($data) {
            $data = array_merge($this->getAuditData(), $data);
        } else {
            $data = $this->getAuditData();
        }

        \Audit::add($this->getAuditTarget(), $audit_type, $data, null, $properties);
    }
}
