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

namespace OEModule\OphCiExamination\tests\unit\controllers;

use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\Allergies;
use OEModule\OphCiExamination\models\AllergyEntry;
use OEModule\OphCiExamination\models\OphCiExaminationAllergyReaction;
use OEModule\OphCiExamination\models\OphCiExaminationAllergy;

/**
 * Class DefaultControllerAllergiesTest
 *
 * @covers OEModule\OphCiExamination\controllers\DefaultController
 * @covers OEModule\OphCiExamination\models\Allergies
 * @group sample-data
 * @group examination
 * @group allergies
 */
class DefaultControllerAllergiesTest extends BaseDefaultControllerTest
{
    use \HasModelAssertions;
    use \WithTransactions;

    /** @test */
    public function ensure_reactions_are_saved_correctly_for_entries()
    {
        $reactions = ModelFactory::factoryFor(OphCiExaminationAllergyReaction::class)->count(2)->create();
        $allergy = ModelFactory::factoryFor(OphCiExaminationAllergy::class)->create();

        $entry_data = [
            'allergy_id' => $allergy->id,
            'has_allergy' => AllergyEntry::$PRESENT,
            'reactions' => array_map(function ($reaction) {
                return $reaction->id;
            }, $reactions)
        ];

        $saved_element = $this->createElementWithDataWithController(['entries' => [$entry_data]]);

        $this->assertCount(1, $saved_element->entries);
        $this->assertEquals($allergy->id, $saved_element->entries[0]->allergy_id);
        $this->assertModelArraysMatch($reactions, $saved_element->entries[0]->reactions);
    }

    /** @test */
    public function entry_reaction_change_is_stored_correctly_for_an_entry()
    {
        $allergies_element = ModelFactory::factoryFor(Allergies::class)->create();
        $reactions = ModelFactory::factoryFor(OphCiExaminationAllergyReaction::class)->count(2)->create();
        $allergies = ModelFactory::factoryFor(OphCiExaminationAllergy::class)->count(2)->create();
        $entry = ModelFactory::factoryFor(AllergyEntry::class)->withReaction($reactions[0])->create([
            'element_id' => $allergies_element->id,
            'allergy_id' => $allergies[0]->id
        ]);

        $form_data = [
            'entries' => [
                [
                    'id' => $entry->id,
                    'allergy_id' => $allergies[1]->id,
                    'has_allergy' => AllergyEntry::$PRESENT,
                    'reactions' => [$reactions[1]->id]
                ]
            ]
        ];

        $this->updateElementWithDataWithController($allergies_element, $form_data);

        $entry->refresh();

        $this->assertModelArraysMatch([$reactions[1]], $entry->reactions);
        $this->assertEquals($entry->allergy_id, $allergies[1]->id);
    }

    /** @test */
    public function allergy_entries_are_removed_when_none_are_posted_during_edit()
    {
        $allergies_element = ModelFactory::factoryFor(Allergies::class)->withEntries(2)->create();

        $form_data = [
            'no_allergies' => true
        ];

        $this->assertCount(2, $allergies_element->entries);

        $this->updateElementWithDataWithController($allergies_element, $form_data);

        $allergies_element->refresh();

        $this->assertEmpty($allergies_element->entries);
        $this->assertNotNull($allergies_element->no_allergies_date);
    }

    /**
     * Wrapper for full request cycle to mimic POST-ing the given data
     * to the controller.
     *
     * @param $data
     * @return mixed
     */
    protected function createElementWithDataWithController($data)
    {
        $model_name = \CHtml::modelName(Allergies::class);
        $_POST[$model_name] = $data;

        $event_id = $this->performCreateRequestForRandomPatient();

        return Allergies::model()->findByAttributes(['event_id' => $event_id]);
    }
}
