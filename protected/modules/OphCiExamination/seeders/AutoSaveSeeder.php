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

namespace OEModule\OphCiExamination\seeders;

use OE\seeders\BaseSeeder;
use OEModule\OphCiExamination\models\SystemicDiagnoses;

class AutoSaveSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $core_api = new \CoreAPI();

        list($original_diagnosis, $draft_diagnosis) = \Disorder::factory()->useExisting()->count(2)->create();

        $original = SystemicDiagnoses::factory()->withDiagnoses([$original_diagnosis])->create();

        $event = $original->event;
        $encoded_data = json_encode(SystemicDiagnoses::factory()->withDiagnoses([$draft_diagnosis])->makeAsFormData(['event_id' => null]));

        $draft = \EventDraft::factory()
               ->forEvent($event)
               ->create(['data' => $encoded_data]);

        $patient = $draft->episode->patient;

        $draft_test_values = [
            'systemic-diagnoses-entry-disorder-term' => $draft_diagnosis->term
        ];

        return [
            'patient_url' => $core_api->generatePatientLandingPageLink($patient),
            'draft_id' => $draft->id,
            'draft_test_values' => $draft_test_values,
            'draft_update_url' => '/OphCiExamination/Default/update?id=' . $event->id . '&draft_id=' . $draft->id,
        ];
    }
}
