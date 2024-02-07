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

namespace OEModule\OphCoCvi\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OEModule\OphCoCvi\models\Element_OphCoCvi_Esign;
use OphCoCvi_Signature;

class Element_OphCoCvi_EsignFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCoCvi')
        ];
    }

    public function withSignatureType(int $signature_type, string $signature_role, ?int $signature_file_id = null): self
    {
        return $this->afterCreating(function (Element_OphCoCvi_Esign $esign_element) use ($signature_type, $signature_role, $signature_file_id) {
            OphCoCvi_Signature::factory()
                ->forElement($esign_element)
                ->asTypeAndRole($signature_type, $signature_role, $signature_file_id)
                ->create();
        });
    }
}
