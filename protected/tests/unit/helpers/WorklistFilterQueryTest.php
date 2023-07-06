<?php
/**
 * OpenEyes.
 *
 * Copyright OpenEyes Foundation, 2023
 *
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

class WorklistFilterQueryTest extends OEDbTestCase
{
    use \WithFaker;

    /** @test */
    public function clear_last_used_filter_from_session_correctly_empties_session_variables()
    {
        $dummy_data = [
            'current_worklist_filter_type' => $this->faker->word(),
            'current_worklist_filter_id' => $this->faker->randomNumber(3),
            'current_worklist_filter_quick' => $this->faker->word()
        ];

        foreach ($dummy_data as $key => $value) {
            Yii::app()->session[$key] = $value;
        }

        // check session vars has value
        $this->assertEquals($dummy_data['current_worklist_filter_type'], Yii::app()->session['current_worklist_filter_type']);
        $this->assertEquals($dummy_data['current_worklist_filter_id'], Yii::app()->session['current_worklist_filter_id']);
        $this->assertEquals($dummy_data['current_worklist_filter_quick'], Yii::app()->session['current_worklist_filter_quick']);

        WorklistFilterQuery::clearLastUsedFilterFromSession();

        // check session vars are empty
        $this->assertEmpty(Yii::app()->session['current_worklist_filter_type']);
        $this->assertEmpty(Yii::app()->session['current_worklist_filter_id']);
        $this->assertEmpty(Yii::app()->session['current_worklist_filter_quick']);
    }
}
