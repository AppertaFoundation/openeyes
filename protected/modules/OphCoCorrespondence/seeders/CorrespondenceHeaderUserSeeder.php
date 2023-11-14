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

namespace OEModule\OphCoCorrespondence\seeders;

use OE\seeders\BaseSeeder;
use Patient;
use OE\factories\models\EventFactory;
use OE\seeders\resources\SeededEventResource;

/**
* CorrespondenceHeaderUserSeeder is a seeder for generating data used solely in the correspondence\correspondence_header_user.cy.js
*/
class CorrespondenceHeaderUserSeeder extends BaseSeeder
{
    /**
    * Return data is:
    * - user - array with elements username, password, fullName and title.
    * - patient_id
    * - patient_contact
    * @return array
    */
    public function __invoke(): array
    {
        // assign current institution
        $current_institution = $this->app_context->getSelectedInstitution();

        // seed user 1
        $user_password = $this->getApp()->dataGenerator->faker()->word() . '_password';
        $user_1 = \User::factory()
            ->withLocalAuthForInstitution($current_institution, $user_password)
            ->withAuthItems(['Edit', 'User', 'View clinical', 'Print'])
            ->create();

        // seed user 2
        $user_2 = \User::factory()
            ->withLocalAuthForInstitution($current_institution, $user_password)
            ->withAuthItems(['Edit', 'User', 'View clinical', 'Print'])
            ->create();

        $patient = Patient::factory()->create();

        $existing_letter_header = \SettingInstallation::model()->findByAttributes(['key' => 'letter_header']);
        if (isset($existing_letter_header)) {
            $setting_installation = $existing_letter_header;
        } else {
            $setting_installation = new \SettingInstallation();
            $setting_installation->key = 'letter_header';
        }

        $setting_installation->value = '<p><span contenteditable="false" data-substitution="user_title"><span>Mr</span></span> <span contenteditable="false" data-substitution="user_name"><span>Admin Admin</span></span></p>';
        $setting_installation->save();

        return [
            'user' =>  [
                [
                    'username' => $user_1->authentications[0]->username,
                    'password' => $user_password,
                    'fullName' => $user_1->getFullName(),
                    'title' => $user_1->title
                ],
                [
                    'username' => $user_2->authentications[0]->username,
                    'password' => $user_password,
                    'fullName' => $user_2->getFullName(),
                    'title' => $user_2->title
                ]
            ],
            'patient_id' => $patient->id,
            'patient_contact' => $patient->contact
        ];
    }
}
