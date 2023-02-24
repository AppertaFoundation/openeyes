<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\ModelFactory;
use \OphCoCvi_Signature;

class OphCoCvi_SignatureFactory extends ModelFactory
{

    public function definition(): array
    {
        return [
            'signatory_name' => $this->faker->name(),
            'timestamp' => time(),
            'signed_user_id' => null,
            'status' => 1,
        ];
    }

    public function asConsultant($element_id, $signatory_user): self
    {
        return $this->state(function () use ($element_id, $signatory_user) {
            return [
                'element_id' => $element_id,
                'type' => OphCoCvi_Signature::TYPE_LOGGEDIN_USER,
                'signatory_role' => 'Consultant',
                'signed_user_id' => $signatory_user->id,
                'signature_file_id' => $signatory_user->signature_file_id
            ];
        });
    }

    public function asPatient($element_id, $user): self
    {
        return $this->state(function () use ($element_id, $user) {
            return [
                'element_id' => $element_id,
                'type' => OphCoCvi_Signature::TYPE_PATIENT,
                'signatory_role' => 'Patient',
                'signed_user_id' => null,
                'signature_file_id' => $user->signature_file_id
            ];
        });
    }
}
