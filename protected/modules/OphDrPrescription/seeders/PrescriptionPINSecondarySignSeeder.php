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

namespace OEModule\OphDrPrescription\seeders;

use OE\seeders\BaseSeeder;
use ReferenceData;

class PrescriptionPINSecondarySignSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $secondary_signatories =  $this->getSeederAttribute('secondary_signatories', []);
        $delete_existing = $this->getSeederAttribute('delete_existing', false);
        $institution_remote_id = $this->getSeederAttribute('institution_remote_id');
        $institution = \Institution::model()->findByAttributes([ 'remote_id' => $institution_remote_id ]);

        if ($delete_existing && $delete_existing !== 'false') {
            \SecondarySignatory::model()->deleteAll();
        }

        foreach ($secondary_signatories as $index => $signatory_name) {
            $signatory = new \SecondarySignatory();
            $signatory->name = $signatory_name;
            $signatory->display_order = ++$index;
            $signatory->active = 1;

            if ($institution) {
                $signatory->institution_id = $institution->id;
            }

            $signatory->save();
        }

        return [
            'secondary_signatories' =>  \Element_OphDrPrescription_Esign::model()->getSecondarySignatures($institution),
            'logged_in_user_name' => \User::model()->findByPk(\Yii::app()->user->id)->getFullNameAndTitle(),
            'institution_id' => $institution->id ?? null,
        ];
    }
}
