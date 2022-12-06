<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 */
class BaseDeliveryCommandTest extends OEDbTestCase
{
    use WithFaker;
    use WithTransactions;
    use MocksSession;

    /** @test */
    public function filename_for_hosnum()
    {
        [$event, $output_id] = $this->createEventAndIdForFormat('{patient.hos_num}');

        $this->assertEquals($event->episode->patient->getHos(), BaseDeliveryCommand::getFileName($event, $output_id));
    }

    /** @test */
    public function filename_for_event_data()
    {
        [$event, $output_id] = $this->createEventAndIdForFormat('{event.id}{event.last_modified_date}');

        $this->assertEquals($event->id . date('Ymd_His', strtotime($event->last_modified_date)), BaseDeliveryCommand::getFileName($event, $output_id));
    }

    /** @test */
    public function filename_output_id_and_date()
    {
        [$event, $output_id] = $this->createEventAndIdForFormat('{document_output.id}{date}');

        $this->assertEquals($output_id . date('YmdHis'), BaseDeliveryCommand::getFileName($event, $output_id));
    }

    /** @test */
    public function filename_prefix_ignored_when_not_in_format()
    {
        [$event, $output_id] = $this->createEventAndIdForFormat('{document_output.id}');

        $this->assertEquals($output_id, BaseDeliveryCommand::getFileName($event, $output_id));
    }

    /** @test */
    public function filename_prefix_included_when_in_format()
    {
        [$event, $output_id] = $this->createEventAndIdForFormat('{prefix}{document_output.id}');
        $prefix = $this->faker->word();
        $this->assertEquals($prefix . '_' . $output_id, BaseDeliveryCommand::getFileName($event, $output_id, $prefix));
    }

    /** @test */
    public function filename_gp_id()
    {
        [$event, $output_id] = $this->createEventAndIdForFormat('{gp.nat_id}');

        $patient = $event->episode->patient;
        $gp = Gp::factory()->create(['nat_id' => 'foobar']);
        $patient->gp_id = $gp->id;

        $this->assertEquals('foobar', BaseDeliveryCommand::getFileName($event, $output_id));
    }

    protected function createEventAndIdForFormat($format)
    {
        $output_id = $this->faker->randomNumber(4);
        $event = Event::factory()->create();
        $this->mockCurrentInstitution($event->institution);
        Yii::app()->params['docman_filename_format'] = $format;

        return [$event, $output_id];
    }
}
