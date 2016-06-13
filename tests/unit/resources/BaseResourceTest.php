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

namespace OEModule\PASAPI\tests\unit\resources;

use OEModule\PASAPI\models\RemapValue;
use OEModule\PASAPI\models\XpathRemap;
use OEModule\PASAPI\resources\BaseResource;

class BaseResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function xpathremapping_provider()
    {
        return array(
            array(
                '<patient><test>S</test></patient>',
                array(array('xpath' => '/patient/test', 'maps' => array('S' => 'CHANGE'))),
                '<patient><test>CHANGE</test></patient>',
            ),
            array(
                '<patient><test>WE</test><test>R</test><test>S</test></patient>',
                array(array('xpath' => '/patient/test',
                    'maps' => array(
                        'R' => null,
                        'S' => '',
                        ), )),
                '<patient><test>WE</test><test /></patient>',
            ),
            array(
                '<patient><wrap><test>WE</test><test>R</test><test>S</test></wrap></patient>',
                array(array('xpath' => '/patient/test',
                    'maps' => array(
                        'R' => null,
                        'S' => '',
                    ), )),
                '<patient><wrap><test>WE</test><test>R</test><test>S</test></wrap></patient>',
            ),
        );
    }

    public function generateMappings($struct)
    {
        $maps = array();
        foreach ($struct['maps'] as $input => $output) {
            $m = new RemapValue();
            $m->attributes = array('input' => $input, 'output' => $output);
            $maps[] = $m;
        }
        $remap = new XpathRemap();
        $remap->xpath = $struct['xpath'];
        $remap->values = $maps;

        return $remap;
    }

    /**
     * @dataProvider xpathremapping_provider
     *
     * @param $xml
     * @param $maps
     * @param $expected
     */
    public function test_Xpathremapping($xml, $maps, $expected)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $remaps = array();
        foreach ($maps as $m) {
            $remaps[] = $this->generateMappings($m);
        }

        BaseResource::remapValues($doc, $remaps);

        $this->assertXmlStringEqualsXmlString($expected, $doc->saveXML());
    }
}
