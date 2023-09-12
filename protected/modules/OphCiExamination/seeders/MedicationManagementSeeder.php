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

/**
* MedicationManagementSeeder is a seeder for generating data used solely in the Medication Management test suite (examination\elements\medication-management.cy.js)
*/
class MedicationManagementSeeder extends BaseSeeder
{
    /**
    * Returns the data required to verify medication management functionality.
    * Return data is:
    * - user (with prescribe privilege) - array with elements username and password
    * - drug1 - first common ophthalmic drug with route 'Eye'
    * - drug2 - second common ophthalmic drug with route 'Eye'
    * @return array
    */
    public function __invoke(): array
    {
        // assign current institution, site, firm and subspeciality
        $current_institution = $this->app_context->getSelectedInstitution();
        $current_site = $this->app_context->getSelectedSite();
        $current_firm = $this->app_context->getSelectedFirm();
        $current_subspeciality = $current_firm->serviceSubspecialtyAssignment->subspecialty;

        // seed a user with prescribe privilege
        $user_password = $this->getApp()->dataGenerator->faker()->word() . '_password';
        $user = \User::factory()
            ->withLocalAuthForInstitution($current_institution, $user_password)
            ->withAuthItems(['Edit', 'Prescribe', 'User', 'View clinical'])
            ->create();
        $user_authentication = $user->authentications[0];

        // retrieve common ophthalmic drugs with route 'Eye' (use the same method to fetch the medications as the adder dialog within the Prescription event)
        $common_ophthalmic = \Medication::model()->listBySubspecialtyWithCommonMedications($current_subspeciality->id, true, $current_site->id, true);
        $common_eye_ophthalmic = array_values(array_filter($common_ophthalmic, function ($medication) {
            return $medication['route'] === 'Eye';
        }));

        return [
            'user' => ['username' => $user_authentication->username,
                       'password' => $user_password
            ],
            'drug1' => $common_eye_ophthalmic[0],
            'drug2' => $common_eye_ophthalmic[1]
        ];
    }
}
