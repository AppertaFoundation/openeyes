<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphTrOperationbooking_ScheduleOperation_PatientUnavailableTest extends CDbTestCase
{
    public $fixtures = array(
            'reasons' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason',
    );

    public static function setUpBeforeClass()
    {
        date_default_timezone_set('UTC');
    }

    public function testStartDateAfterEndDate()
    {
        $test = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
        $test->start_date = '2014-05-03';
        $test->end_date = '2014-04-03';
        $test->reason_id = 1;
        $this->assertFalse($test->validate());
    }

    public function testStartDateEqualEndDate()
    {
        $test = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
        $test->start_date = '2014-04-03';
        $test->end_date = '2014-04-03';
        $test->reason_id = 1;
        $this->assertTrue($test->validate());
    }

    public function testStartDateBeforeEndDate()
    {
        $test = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
        $test->start_date = '2014-03-03';
        $test->end_date = '2014-04-03';
        $test->reason_id = 1;
        $this->assertTrue($test->validate());
    }

    public function testReasonRequired()
    {
        $test = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
        $test->start_date = '2014-04-03';
        $test->end_date = '2014-04-03';
        $this->assertFalse($test->validate());
    }

    public function testReasonMustBeActiveForNewRecord()
    {
        $test = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
        $test->start_date = '2014-04-03';
        $test->end_date = '2014-04-03';
        $test->reason_id = $this->reasons('inactive_reason')->id;

        $this->assertFalse($test->validate());
    }

    public function testReasonInactiveForRecordUpdate()
    {
        $test = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
        $test->start_date = '2014-04-03';
        $test->end_date = '2014-04-03';
        $test->reason_id = $this->reasons('inactive_reason')->id;
        // force the scenario as means we don't have to actually save anything in the db for this test
        $test->scenario = 'update';

        $this->assertTrue($test->validate());
    }
}
