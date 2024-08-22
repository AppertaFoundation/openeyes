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
use User;

class AutoSaveSeeder extends BaseSeeder
{
    protected ?string $initial_firm_id = null;

    public function __invoke(): array
    {
        $core_api = new \CoreAPI();

        $intial_firm_id = $this->getSeederAttribute('initial_firm_id');

        //A user for testing context change when visiting draft
        $context_change_user = User::factory()
            ->withLocalAuthForInstitution($this->app_context->getSelectedInstitution())
            ->withAuthItems([
                'User',
                'Edit',
                'View clinical'
            ])
            ->create();
        $context_change_user_auth = $context_change_user->authentications[0];

        $initial_firm_subspecialty_assignment_id = $this->getApp()->db->createCommand()
            ->select('service_subspecialty_assignment_id')
            ->from('firm')
            ->where('id = :firm_id', [':firm_id' => $intial_firm_id])
            ->queryScalar();

        $draft_episode_firm = $this->getApp()->db->createCommand()
            ->select('id, service_subspecialty_assignment_id')
            ->from('firm')
            ->where('service_subspecialty_assignment_id != :subspecialty_assignment_id', [':subspecialty_assignment_id' => $initial_firm_subspecialty_assignment_id])
            ->andWhere('can_own_an_episode=1')
            ->queryRow();

        $draft_event_firm_id = $this->getApp()->db->createCommand()
            ->select('id')
            ->from('firm')
            ->where('service_subspecialty_assignment_id = :subspecialty_assignment_id', [':subspecialty_assignment_id' => $draft_episode_firm['service_subspecialty_assignment_id']])
            ->andWhere('can_own_an_episode=0')
            ->limit(1)
            ->queryScalar();

        list($original_diagnosis, $draft_diagnosis) = \Disorder::factory()->useExisting()->count(2)->create();

        $original = SystemicDiagnoses::factory()->withDiagnoses([$original_diagnosis])->create();

        $event = $original->event;
        $event->firm_id = $draft_event_firm_id;
        $event->save();
        $event->refresh();
        $encoded_data = json_encode(SystemicDiagnoses::factory()->withDiagnoses([$draft_diagnosis])->makeAsFormData(['event_id' => null]));

        $draft = \EventDraft::factory()
               ->forEvent($event)
               ->forUser($context_change_user)
               ->create([
                    'data' => $encoded_data,
                    'created_user_id' => $context_change_user->id
                ]);

        $draft->episode->firm_id = $draft_episode_firm['id'];
        $draft->episode->save();
        $draft->episode->refresh();

        $patient = $draft->episode->patient;

        $draft_test_values = [
            'systemic-diagnoses-entry-disorder-term' => $draft_diagnosis->term
        ];

        return [
            'patient_url' => $core_api->generatePatientLandingPageLink($patient),
            'draft_id' => $draft->id,
            'draft_test_values' => $draft_test_values,
            'draft_update_url' => '/PatientEvent/loadDraft?draft_id=' . $draft->id,
            'draft_context_name' => $draft->event->firm->name,
            'draft_subspecialty_id' => $draft->episode->firm->serviceSubspecialtyAssignment->subspecialty_id,
            'context_change_user' => ['username' => $context_change_user_auth->username, 'password' => 'password']
        ];
    }
}
