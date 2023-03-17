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
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\ModelFactory;

class OphCoCorrespondence_SignatureFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'element_id' => ModelFactory::factoryFor(Element_OphCoCorrespondence_Esign::class),
            'signatory_name' => $this->faker->name(),
            'timestamp' => time(),
            'signed_user_id' => null,
            'secretary' => 0
        ];
    }

    public function asConsultant($signatory_user): self
    {
        return $this->state(function () use ($signatory_user) {
            return [
                'type' => OphCoCorrespondence_Signature::TYPE_LOGGEDIN_USER,
                'signatory_role' => 'Consultant',
                'signed_user_id' => $signatory_user->id,
                'signature_file_id' => $signatory_user->signature_file_id
            ];
        });
    }

    public function forFirm(\Firm $firm): self
    {

        return $this
            ->state(
                function (array $attributes) use ($firm) {
                    if ($attributes['element_id'] instanceof ModelFactory) {
                        $attributes['element_id']->forFirm($firm);
                    }
                    return $attributes;
                })
            ->afterCreating(
                function (OphCoCorrespondence_Signature $signature) use ($firm) {
                    if ($signature->element->event->firm_id != $firm->id) {
                        $signature->element->event->firm_id = $firm->id;
                        $signature->element->event->save();
                    }
                }
            );
    }
}
