<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 * @group operation-booking
 */
class OphTrOperationbooking_Operation_SessionWithSampleDataTest extends \OEDbTestCase
{
    use HasModelAssertions;
    use WithTransactions;

    /** @test */
    public function booking_is_returned_when_ward_id_is_null()
    {
        $booking = OphTrOperationbooking_Operation_Booking::factory()->create();

        $session = $booking->session;

        $active_bookings = $session->getActiveBookingsForWard();

        $this->assertCount(1, $active_bookings);
        $this->assertModelIs($booking, $active_bookings[0]);
    }

    /** @test */
    public function booking_is_returned_when_all_is_passed_for_ward_id()
    {
        $booking = OphTrOperationbooking_Operation_Booking::factory()->create();

        $session = $booking->session;

        $active_bookings = $session->getActiveBookingsForWard('All');

        $this->assertCount(1, $active_bookings);
        $this->assertModelIs($booking, $active_bookings[0]);
    }

    /** @test */
    public function booking_is_not_returned_when_different_ward_id_is_passed()
    {
        $booking = OphTrOperationbooking_Operation_Booking::factory()->create();
        $other_ward = OphTrOperationbooking_Operation_Ward::factory()->create([
            'site_id' => $booking->theatre->site_id
        ]);

        $session = $booking->session;

        $active_bookings = $session->getActiveBookingsForWard($other_ward->id);
        $this->assertEmpty($active_bookings);
    }
}
