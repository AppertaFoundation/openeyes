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

namespace OEModule\OphCoMessaging\seeders;

use OE\seeders\BaseSeeder;

/**
* CreateMessageSeeder is a seeder for generating data used solely in the 'create message via patient record' test (messaging\create.cy.js)
*/
class CreateMessageSeeder extends BaseSeeder
{
    /**
    * Returns the data required to create a message via patient record.
    * Return data is:
    * - user - array with elements username, password, fullName and messageText
    * @return array
    */
    public function __invoke(): array
    {
        // TO DO: how to find current institution within seeder
        $current_institution = $this->app_context->getSelectedInstitution();

        // seed user
        $user_password = $this->getApp()->dataGenerator->faker()->word() . '_password';
        $user = \User::factory()
            ->withLocalAuthForInstitution($current_institution, $user_password)
            ->withAuthItems(['User'])
            ->create();
        $user_authentication = $user->authentications[0];
        $user_fullname = $user->getFullNameAndTitle();

        return [
            'user' =>  ['username' => $user_authentication->username,
                        'password' => $user_password,
                        'fullName' => $user_fullname,
                        'messageText' => 'Hello ' . $user_fullname,
            ]
        ];
    }
}
