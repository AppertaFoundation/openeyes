<?php
use OE\factories\models\EventFactory;
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
  */
class EventTemplateTest extends OEDbTestCase
{
    use HasModelAssertions;
    use WithTransactions;

    /** @test */
    public function detail_record_returns_related_model_when_pks_are_not_synced()
    {
        // force
        $other_event_template = EventTemplate::factory()
            ->create([
                'event_type_id' => EventType::model()->find('class_name = :cls', [':cls' => 'OphCiExamination']),
                'source_event_id' => EventFactory::forModule('OphCiExamination')
            ]);

        $op_note_template = OphTrOperationnote_Template::factory()->create();

        $event_template = EventTemplate::model()->findByPk($op_note_template->event_template_id);

        $this->assertModelIs($op_note_template, $event_template->getDetailRecord());
    }
}
