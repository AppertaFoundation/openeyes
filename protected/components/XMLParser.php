<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2018
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class XMLParser
{
    private $xmlArray = array();

    public function __construct( $xml )
    {
        $this->parse($xml);
    }

    private function parse($contents, $getAttributes=1, $priority = 'tag'){
        if (!$contents) return array();

        if (!function_exists('xml_parser_create')) {
            //print "'xml_parser_create()' function not found!";
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xmlValues);
        xml_parser_free($parser);

        if (!$xmlValues) return;//Hmm...

        //Initializations
        $xmlArray = array();

        $current = &$xmlArray; //Refference

        //Go through the tags.
        $repeatedTagIndex = array();//Multiple tags with same name will be turned into an array
        foreach($xmlValues as $data) {
            unset($attributes,$value);//Remove existing values, or there will be trouble

            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data);//We could use the array by itself, but this cooler.

            $result = array();
            $attributesData = array();

            if (isset($value)) {
                if ($priority == 'tag') $result = $value;
                else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if (isset($attributes) and $getAttributes) {
                foreach($attributes as $attr => $val) {
                    if ($priority == 'tag') $attributesData[$attr] = $val;
                    else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if ($type == "open") {//The starting of the tag '<tag>'
                $parent[$level-1] = &$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if ($attributesData) $current[$tag. '_attr'] = $attributesData;
                    $repeatedTagIndex[$tag.'_'.$level] = 1;

                    $current = &$current[$tag];

                } else { //There was another element with the same tag name

                    if (isset($current[$tag][0])) {//If there is a 0th element it is already an array
                        $current[$tag][$repeatedTagIndex[$tag.'_'.$level]] = $result;
                        $repeatedTagIndex[$tag.'_'.$level]++;
                    } else {//This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                        $repeatedTagIndex[$tag.'_'.$level] = 2;

                        if (isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }

                    }
                    $lastItemIndex = $repeatedTagIndex[$tag.'_'.$level]-1;
                    $current = &$current[$tag][$lastItemIndex];
                }

            } elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if (!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeatedTagIndex[$tag.'_'.$level] = 1;
                    if ($priority == 'tag' and $attributesData) $current[$tag. '_attr'] = $attributesData;

                } else { //If taken, put all things inside a list(array)
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeatedTagIndex[$tag.'_'.$level]] = $result;

                        if ($priority == 'tag' and $getAttributes and $attributesData) {
                            $current[$tag][$repeatedTagIndex[$tag.'_'.$level] . '_attr'] = $attributesData;
                        }
                        $repeatedTagIndex[$tag.'_'.$level]++;

                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                        $repeatedTagIndex[$tag.'_'.$level] = 1;
                        if ($priority == 'tag' and $getAttributes) {
                            if (isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                            if ($attributesData) {
                                $current[$tag][$repeatedTagIndex[$tag.'_'.$level] . '_attr'] = $attributesData;
                            }
                        }
                        $repeatedTagIndex[$tag.'_'.$level]++; //0 and 1 index is already taken
                    }
                }

            } elseif ($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level-1];
            }
        }

        $this->xmlArray = $xmlArray;
    }

    public function getOutput()
    {
        return $this->xmlArray;
    }

}