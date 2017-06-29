<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class XmlHelper
{
    private $_xml;

    private $_xml_reader = null;

    public function __construct($xml = null)
    {
        $this->_xml_reader = new XMLReader();

        if($xml){
            $this->_xml_reader->xml($xml);
        }
    }

    /**
     * Load XML string
     * @param $xml
     */
    public function xml($xml)
    {
        $this->_xml = $xml;
        $this->_xml_reader->xml($xml);
    }

    /**
     * Returns XMLReader object
     * @return null|XMLReader
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