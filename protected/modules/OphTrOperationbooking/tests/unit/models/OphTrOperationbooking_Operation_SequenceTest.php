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
class OphTrOperationbooking_Operation_SequenceTest extends ActiveRecordTestCase
{
    public $fixtures = array(
        'wards' => 'OphTrOperationbooking_Operation_Ward',
        'theatres' => 'OphTrOperationbooking_Operation_Theatre',
    );

    public function getModel()
    {
        return OphTrOperationbooking_Operation_Sequence::model();
    }

    protected array $columns_to_skip = [
        'default_admission_time'
    ];

    public static function setUpBeforeClass()
    {
        date_default_timezone_set('UTC');
    }

    public function setUp()
    {
        $this->operationSequence = new OphTrOperationbooking_Operation_Sequence();
        parent::setUp();
    }

    public function testCompareStartdateWithWeekday()
    {
        $att = array(
            'consultant' => '1', 'paediatric' => '0', 'anaesthetist' => '0',
            'general_anaesthetic' => '0', 'last_generate_date' => '1901-01-01 00:00:00',
            'last_modified_user_id' => '1', 'last_modified_date' => '1901-01-01 00:00:00',
            'created_user_id' => '1',    'created_date' => '1901-01-01 00:00:00',
            'deleted' => 0,    'firm_id' => '', 'theatre_id' => '1',
            'start_date' => '20 Mar 2014',    'end_date' => null,    'weekday' => '3',
            'start_time' => '01:00',    'end_time' => '02:00',    'default_admission_time' => '',
            'interval_id' => '2', 'week_selection' => null,    'id' => null,
        );
        $this->operationSequence->attributes = $att;

        $this->operationSequence->save();
        $errors = $this->operationSequence->getErrors();
        $this->assertGreaterThan(0, count($errors));
        $this->assertTrue(array_key_exists('start_date', $errors));
        $this->assertGreaterThan(0, $errors['start_date']);
        $this->assertEquals($errors['start_date'][0], 'Start date and weekday must be on the same day of the week');

        $opSequence = new OphTrOperationbooking_Operation_Sequence();
        $att['start_date'] = '19 Mar 2014';
        $opSequence->attributes = $att;
        $opSequence->save();
        $errors = $opSequence->getErrors();
        $this->assertFalse(array_key_exists('start_date', $errors));
    }
}
