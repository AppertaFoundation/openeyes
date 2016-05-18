<?php

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
class AppointmentTest extends PHPUnit_Framework_TestCase
{
    public function getWhenProvider()
    {
        return array(
            array(
                '2016-05-04 12:13:12', '15:10:00', '2016-05-04 15:10:00',
            ),
            array(
                'asdad', 'asdsaf', null
            )
        );
    }

    /**
     * @dataProvider getWhenProvider
     *
     * @param $appointment_date
     * @param $appointment_time
     * @param $expected
     */
    public function test_getWhen($appointment_date, $appointment_time, $expected)
    {
        $app = $this->getMockBuilder("OEModule\\PASAPI\\resources\\Appointment")
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $app->AppointmentDate = $appointment_date;
        $app->AppointmentTime = $appointment_time;

        if (is_null($expected)) {
            $this->setExpectedException("Exception");
        }
        $when = $app->getWhen();

        if ($expected)
            $this->assertEquals($expected, $when->format('Y-m-d H:i:s'));
    }
}
