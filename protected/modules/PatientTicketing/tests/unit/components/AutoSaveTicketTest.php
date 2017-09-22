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
use \OEModule\PatientTicketing\components\AutoSaveTicket;

class AutoSaveTicketTest extends PHPUnit_Framework_TestCase
{
    public function testAutoSaveTicketKey()
    {
        $patient_id = 123;
        $queue_id = 1;

        $key = AutoSaveTicket::getAutoSaveKey($patient_id, $queue_id);

        $this->assertEquals($key, 'pt_123_1');
    }

    public function testAutoSaveTicket()
    {
        $patient_id = 123;
        $queue_id = 1;
        $data = array('key' => 'value');

        AutoSaveTicket::saveFormData($patient_id, $queue_id, $data);

        $this->assertEquals($data, AutoSaveTicket::getFormData($patient_id, $queue_id));
    }

    public function testAutoSaveClear()
    {
        $first_patient_id = 1;
        $second_patient_id = 2;

        $first_queue_id = 1;
        $second_queue_id = 1;

        $data = array('key' => 'value');

        AutoSaveTicket::saveFormData($first_patient_id, $first_queue_id, $data);
        AutoSaveTicket::saveFormData($second_patient_id, $second_queue_id, $data);

        $this->assertNotNull(AutoSaveTicket::getFormData($first_patient_id, $first_queue_id));
        $this->assertNotNull(AutoSaveTicket::getFormData($first_patient_id, $second_queue_id));

        AutoSaveTicket::clear();

        $this->assertNull(AutoSaveTicket::getFormData($first_patient_id, $first_queue_id));
        $this->assertNull(AutoSaveTicket::getFormData($first_patient_id, $second_queue_id));
    }
}
