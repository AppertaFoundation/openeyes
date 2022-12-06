<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PASAPI\tests\unit\resources;

use OEModule\PASAPI\models\RemapValue;
use OEModule\PASAPI\models\XpathRemap;
use OEModule\PASAPI\resources\BaseResource;

/**
 * @group sample-data
 */
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

    public function test_validate_checks_child_resources()
    {
        $test = $this->getMockBuilder('\\OEModule\\PASAPI\\resources\\BaseResource')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMockForAbstractClass();

        $rc = new \ReflectionClass($test);
        $p = $rc->getProperty('schema');
        $p->setAccessible(true);
        $p->setValue($test, array(
            'sampleResource' => array(
                'resource' => 'sampleResource',
            ),
        ));

        $test->sampleResource = $this->getMockBuilder('\\OEModule\\PASAPI\\resources\\BaseResource')
            ->disableOriginalConstructor()
            ->setMethods(array('validate'))
            ->getMockForAbstractClass();

        $test->sampleResource->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(false));

        $test->sampleResource->errors = ['test error'];

        $this->assertFalse($test->validate());
        $this->assertEquals(array('sampleResource error: test error'), $test->errors);
    }
}
