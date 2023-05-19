<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 * @group correspondence
 * @group e-signatures
 */
class Element_OphCoCorrespondence_EsignTest extends ModelTestCase
{
    use WithTransactions;
    use WithFaker;

    protected $element_cls = Element_OphCoCorrespondence_Esign::class;

    /** @test */
    public function primary_role_is_before_seondary_role_in_ordered_signatures()
    {
        $esign_element = Element_OphCoCorrespondence_Esign::factory()->create();

        $secondary_signature = OphCoCorrespondence_Signature::factory()
                             ->create([
                                 'element_id' => $esign_element,
                                 'signed_user_id' => User::factory(),
                                 'signatory_role' => Element_OphCoCorrespondence_Esign::SECONDARY_ROLE
                             ]);

        $primary_signature = OphCoCorrespondence_Signature::factory()
                           ->create([
                               'element_id' => $esign_element,
                               'signed_user_id' => User::factory(),
                               'signatory_role' => Element_OphCoCorrespondence_Esign::PRIMARY_ROLE
                           ]);

        $expected_signatures_order = [$primary_signature, $secondary_signature];
        $result_signatures = $esign_element->orderedSignatures;

        $this->assertModelArraysMatchOrdered($expected_signatures_order, $result_signatures);
    }

    /** @test */
    public function other_roles_placed_after_primary_or_secondory_roles_in_ordered_signatures()
    {
        $esign_element = Element_OphCoCorrespondence_Esign::factory()->create();
        $other_signature = OphCoCorrespondence_Signature::factory()
                             ->create([
                                 'element_id' => $esign_element,
                                 'signed_user_id' => User::factory(),
                                 'signatory_role' => implode(" ", $this->faker->words(3))
                             ]);

        $main_role = $this->faker->randomElement([
            Element_OphCoCorrespondence_Esign::PRIMARY_ROLE,
            Element_OphCoCorrespondence_Esign::SECONDARY_ROLE
        ]);

        $main_signature = OphCoCorrespondence_Signature::factory()
                           ->create([
                               'element_id' => $esign_element,
                               'signed_user_id' => User::factory(),
                               'signatory_role' => $main_role
                           ]);

        $expected_signatures_order = [$main_signature, $other_signature];
        $result_signatures = $esign_element->orderedSignatures;

        $this->assertModelArraysMatchOrdered($expected_signatures_order, $result_signatures);
    }
}
