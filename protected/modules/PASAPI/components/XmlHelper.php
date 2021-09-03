<?php
namespace OEModule\PASAPI\components;

use DOMDocument;
use XMLReader;

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class XmlHelper
{
    private $_xml;

    private $_xml_reader;

    public $errors = array();

    public function __construct($xml = null)
    {
        libxml_use_internal_errors(true);

        $this->_xml_reader = new XMLReader();

        if ($xml){
            $this->xml($xml);
        }
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Load XML string
     * @param $xml
     * @return bool
     */
    public function xml($xml)
    {
        $this->_xml = $xml;
        if ( !$this->isXMLContentValid($xml) || $this->_xml_reader->xml($xml) === false){
            return false;
        }

        return true;
    }

    public function isValid()
    {
        // $this->_xml_reader->isValid() is only check the current node not the whole doc
        return $this->isXMLContentValid($this->_xml);
    }

    public function isXMLContentValid($xml_content, $version = '1.0', $encoding = 'utf-8')
    {
        if (trim($xml_content) == '') {
            return false;
        }

        $doc = new DOMDocument($version, $encoding);
        $doc->loadXML($xml_content);

        foreach (libxml_get_errors() as $error) {
            $this->addError($error);
        }

        libxml_clear_errors();

        return empty($this->getErrors());
    }

    /**
     * Returns XMLReader object
     * @return XMLReader
     */
    public function getHandler()
    {
        return $this->_xml_reader;
    }

    /**
     * Returns the count of the given node name
     * @param string $node_name
     * @return int count of nodes specified in param
     */
    public function countNodes($node_name)
    {
        $count = 0;

        if ( !$this->isXMLContentValid($this->_xml) ){
            return $count;
        }

        $reader = new XMLReader();
        $reader->xml( $this->_xml );

        // move to the first <patient /> node
        while ($reader->read() && $reader->name !== $node_name);

        while ($reader->name === $node_name){
            $count++;

            $reader->next($node_name);
        }

        return $count;
    }
}