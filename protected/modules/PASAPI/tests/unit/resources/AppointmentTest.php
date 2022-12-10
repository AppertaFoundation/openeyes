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

/**
 * @group sample-data
 */
class AppointmentTest extends \PHPUnit_Framework_TestCase
{
    public function getWhenProvider()
    {
        return array(
            array(
                null, '2016-05-04 12:13:12', '15:10', '2016-05-04 15:10:00',
            ),
            array(
                null, 'asdad', 'asdsaf', null,
            ),
            array(
                '2016-04-12 13:10', null, null, '2016-04-12 13:10:00',
            ),
            array(
                '2016-04-12 13:10', '2016-05-10', null, '2016-05-10 13:10:00',
            ),
            array(
                '2016-04-12 13:10', null, '10:30', '2016-04-12 10:30:00',
            ),
            array(
                '2016-04-12 13:10', '2016-10-12', '10:30', '2016-10-12 10:30:00',
            ),
        );
    }

    /**
     * @dataProvider getWhenProvider
     *
     * @param $appointment_date
     * @param $appointment_time
     * @param $expected
     */
    public function test_getWhen($default_when, $appointment_date, $appointment_time, $expected)
    {
        $app = $this->getMockBuilder('OEModule\\PASAPI\\resources\\Appointment')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        if (!is_null($default_when)) {
            $app->setDefaultWhen(\DateTime::createFromFormat('Y-m-d H:i', $default_when));
        }
        if (!is_null($appointment_date)) {
            $app->AppointmentDate = $appointment_date;
        }
        if (!is_null($appointment_time)) {
            $app->AppointmentTime = $appointment_time;
        }

        if (is_null($expected)) {
            $this->expectException('Exception');
        }
        $when = $app->getWhen();

        if ($expected) {
            $this->assertEquals($expected, $when->format('Y-m-d H:i:s'));
        }
    }

    public function getMappingsArrayProvider()
    {
        return array(
            array(
                array('key1', 'value1', 'key2', 'value2'),
                array(
                    'key1' => 'value1',
                    'key2' => 'value2',
                ),
            ),
            // duplicate key overrides, should be prevented by validation
            array(
                array('key1', 'value1', 'key1', 'value2'),
                array(
                    'key1' => 'value2',
                ),
            ),
        );
    }

    /**
     * @dataProvider getMappingsArrayProvider
     *
     * @param $items
     * @param $expected
     */
    public function test_getMappingsArray($items, $expected)
    {
        $mapping_items = array();
        for ($i = 0; $i < count($items); $i += 2) {
            $mi = $this->getMockBuilder('OEModule\\PASAPI\\resources\\AppointmentMapping')
                ->disableOriginalConstructor()
                ->setMethods(null)
                ->getMock();
            $mi->Key = $items[$i];
            $mi->Value = $items[$i + 1];
            $mapping_items[] = $mi;
        }

        $a = $this->getMockBuilder('OEModule\\PASAPI\\resources\\Appointment')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $a->AppointmentMappingItems = $mapping_items;

        $this->assertEquals($expected, $a->getMappingsArray());
    }
}
