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

namespace OEModule\OphInBiometry\factories;

use Element_OphInBiometry_IolRefValues;
use Eye;
use OE\factories\models\EventFactory;
use OEModule\OphCiExamination\models\interfaces\SidedData;

class OphInBiometryFactory extends EventFactory
{
    public function definition(): array
    {
        return array_merge(
            parent::definition(),
            [
                'event_type_id' => $this->getEventTypeByName('Biometry')
            ]
        );
    }

    public function withIolRefValues($states = []) {

        return $this->afterCreating(function (\Event $event)  {

            foreach ([SidedData::LEFT, SidedData::RIGHT] as $eye_id) {
                Element_OphInBiometry_IolRefValues::factory()->create([
                    'event_id' => $event->id,
                    'eye_id' => $eye_id,
                    'iol_ref_values_' . strtolower(Eye::methodPostFix($eye_id)) =>
                    '{"REF":[-2.96,-2.59,-2.23,-1.87,-1.52,-1.17,-0.83],"IOL":[25.0,24.5,24.0,23.5,23.0,22.5,22.0]}'
                ]);
            }


        });
    }
}
