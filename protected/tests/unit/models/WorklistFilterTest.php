<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

 /*
  * @covers WorklistFilter
  * @group sample-data
 */
class WorklistFilterTest extends ActiveRecordTestCase
{
    use WithTransactions;

    public function getModel()
    {
        return WorklistFilter::model();
    }

    public function setUp(): void
    {
        parent::setUp();

        // clean house
        WorklistFilter::model()->deleteAll();
    }

    /**
     * @test
     */
    public function default_scope_correctly_orders_data()
    {
        $unordered_dates = ['2000-01-01 00:00:00', '1978-09-26 00:00:00', '2020-12-31 00:00:00'];
        shuffle($unordered_dates);

        foreach ($unordered_dates as $date) {
            WorklistFilter::factory()->create(['last_modified_date' => $date]);
        }

        $previousDate = new DateTime();
        foreach (WorklistFilter::model()->findAll() as $filter) {
            $this->assertLessThan($previousDate, $filter->last_modified_date);
            $previousDate = $filter->last_modified_date;
        }
    }
}
