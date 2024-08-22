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

namespace OEModule\OphCiExamination\tests\feature;

use OEModule\OphCiExamination\components\ExaminationHelper;

/**
 * A safety test that validates all the examination elements can be loaded dynamically
 * @group sample-data
 * @group examination
 */
class ElementLoadingTest extends \OEDbTestCase
{
    use \HasEventTypeElementAssertions;
    use \MocksSession;
    use \MakesApplicationRequests;
    use \WithTransactions;

    public function elementClassListProvider()
    {
        $exclude_list = ExaminationHelper::elementFilterList();

        // TODO: enable this once the mock user works with ESignPINFieldMedication
        $exclude_list = array_merge($exclude_list, [
            \OEModule\OphCiExamination\models\MedicationManagement::class
        ]);

        /* This assumes that Yii and its database connection are available at the time PHPUnit is setting up the tests.
         * Given the current set up of the unit testing, this is the case.
         *
         * It only looks at existing data and does not modify the database.
         * For the existing data it assumes the element_type table is filled with the correct class name entries for
         * elements in the Examination event.
         */
        $class_name_list = \Yii::app()->db->createCommand()
                         ->select('element_type.class_name')
                         ->from('element_type')
                         ->join('event_type', 'element_type.event_type_id = event_type.id')
                         ->where('event_type.class_name = "OphCiExamination"')
                         ->queryColumn();

        return array_map(static function ($class_name) {
            return [$class_name];
        }, array_diff($class_name_list, $exclude_list));
    }

    /**
     * @test
     * @dataProvider elementClassListProvider
     */
    public function element_class_can_be_loaded($class_name)
    {
        list($user, $institution) = $this->createUserWithInstitution();

        $element_type = \ElementType::factory()->useExisting(['class_name' => $class_name])->create();
        $patient = \Patient::factory()->create();
        $episode = \Episode::factory()->create(['patient_id' => $patient]);

        $this->mockCurrentContext($episode->firm, null, $institution);

        $this->actingAs($user, $institution)
            ->get("/OphCiExamination/Default/ElementForm?id={$element_type->id}&patient_id={$patient->id}")
            ->assertSuccessful("$class_name element could not be loaded for examination event.");
    }
}
