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

namespace OEModule\OphDrPGDPSD\seeders;

use OE\seeders\resources\SeededEventResource;
use OE\seeders\BaseSeeder;

use OEModule\OphDrPGDPSD\models\OphDrPGDPSD_PGDPSD;

use OEModule\OphDrPGDPSD\models\OphDrPGDPSD_AssignedUser;
use OEModule\OphDrPGDPSD\models\OphDrPGDPSD_PGDPSDMeds;

/**
* Seeder for creating a prescriber user with no admin permissions and adding a PGD PSD Setting
*/
class AddPGDPSDSettingSeeder extends BaseSeeder
{

    protected ?string $admin_firm_id = null;

    public function __invoke(): array
    {
        $admin_firm_id = $this->getSeederAttribute('admin_firm_id', 297);
        $current_institution = \Institution::model()->getCurrent();
        $user = \User::factory()->withAuthItems(['Prescribe', 'User', 'Edit', 'View clinical'])->withLocalAuthForInstitution($current_institution)->create(['last_firm_id' => $admin_firm_id]);
        $user_auth = $user->authentications[0];
        $pgd_psd = OphDrPGDPSD_PGDPSD::factory()->forInstitution($current_institution)->psd()->create();
        $pgd_psd_assigned_user = OphDrPGDPSD_AssignedUser::factory()->forPGDPSD($pgd_psd)->forUser($user)->create();
        $pgd_psd_meds = OphDrPGDPSD_PGDPSDMeds::factory()->forPGDPSD($pgd_psd)->create();

        $patient = \Patient::factory()->create();
        return [
            'pgdPsd' =>  $pgd_psd,
            'patientId' => $patient->id,
            'user' => ['username' => $user_auth->username, 'password' => 'password']
        ];
    }
}
