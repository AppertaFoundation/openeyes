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
 * @group event-templates
 * @group opnote-templates
 */
class OphTrOperationnote_TemplateTest extends OEDbTestCase
{
    use HasModelAssertions;
    use WithTransactions;

    /** @test */
    public function procedure_set_scope_supports_single_set_request()
    {
        $procedure_set = ProcedureSet::factory()->create();

        $opnote_templates = OphTrOperationnote_Template::factory()->count(2)->create([
            'proc_set_id' => $procedure_set->id
        ]);

        // create another to ensure it's excluded
        OphTrOperationnote_Template::factory()->create();

        $this->assertModelArraysMatch(
            $opnote_templates,
            OphTrOperationnote_Template::model()->forProcedureSet($procedure_set)->findAll()
        );
    }

    /** @test */
    public function procedure_set_scope_supports_multiple_sets_request()
    {
        $procedure_sets = ProcedureSet::factory()->count(2)->create();

        $opnote_templates = [
            OphTrOperationnote_Template::factory()->create([
                'proc_set_id' => $procedure_sets[0]->id
            ]),
            OphTrOperationnote_Template::factory()->create([
                'proc_set_id' => $procedure_sets[1]->id
            ])
        ];

        // create another to ensure it's excluded
        OphTrOperationnote_Template::factory()->create();

        $this->assertModelArraysMatch(
            $opnote_templates,
            OphTrOperationnote_Template::model()->forProcedureSet($procedure_sets)->findAll()
        );
    }
}
