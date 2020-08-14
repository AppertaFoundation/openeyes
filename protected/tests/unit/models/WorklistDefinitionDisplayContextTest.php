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
class WorklistDefinitionDisplayContextTest extends ActiveRecordTestCase
{
    public function getModel()
    {
        return WorklistDefinitionDisplayContext::model();
    }

    public function checkSiteProvider()
    {
        return array(
            array(array('site_id' => 5), array('id' => 5), true),
            array(array('site_id' => null), array('id' => 5), true),
            array(array('site_id' => 7), array('id' => 5), false),
        );
    }

    /**
     * @covers WorklistDefinitionDisplayContext
     * @dataProvider checkSiteProvider
     *
     * @param $context_attrs
     * @param $site_attrs
     * @param $expected
     * @throws ReflectionException
     */
    public function test_checkSite($context_attrs, $site_attrs, $expected)
    {
        $context = new WorklistDefinitionDisplayContext();
        foreach ($context_attrs as $k => $v) {
            $context->$k = $v;
        }

        $site = ComponentStubGenerator::generate('Site', $site_attrs);

        $this->assertEquals($expected, $context->checkSite($site));
    }

    public function checkFirmProvider()
    {
        return array(
            array(array('subspecialty_id' => 5, 'firm_id' => 3), array('id' => 2, 'subspecialty_id' => 5), false),
            array(array('subspecialty_id' => 5, 'firm_id' => null), array('id' => 2, 'subspecialty_id' => 5), true),
            array(array('subspecialty_id' => 5, 'firm_id' => null), array('id' => 2, 'subspecialty_id' => 7), false),
            array(array('subspecialty_id' => null, 'firm_id' => 3), array('id' => 2, 'subspecialty_id' => 5), false),
            array(array('subspecialty_id' => null, 'firm_id' => 3), array('id' => 3, 'subspecialty_id' => 5), true),
        );
    }

    /**
     * @covers WorklistDefinitionDisplayContext
     * @dataProvider checkFirmProvider
     *
     * @param $context_attrs
     * @param $firm_attrs
     * @param $expected
     * @throws ReflectionException
     */
    public function test_checkFirm($context_attrs, $firm_attrs, $expected)
    {
        $context = new WorklistDefinitionDisplayContext();
        foreach ($context_attrs as $k => $v) {
            $context->$k = $v;
        }

        $firm = $this->getMockBuilder('Firm')
            ->disableOriginalConstructor()
            ->setMethods(array('getSubspecialty'))
            ->getMock();
        $firm->id = $firm_attrs['id'];

        $subspecialty = ComponentStubGenerator::generate('Subspecialty', array('id' => $firm_attrs['subspecialty_id']));

        $firm->expects($this->any())
            ->method('getSubspecialty')
            ->will($this->returnValue($subspecialty));

        $this->assertEquals($expected, $context->checkFirm($firm));
    }
}
