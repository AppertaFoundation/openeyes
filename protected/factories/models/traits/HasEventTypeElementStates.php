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

namespace OE\factories\models\traits;

use Patient;
use OE\factories\ModelFactory;

trait HasEventTypeElementStates
{
    public function forPatient(Patient $patient)
    {
        return $this->state(function ($attributes) use ($patient) {
            if ($attributes['event_id'] instanceof ModelFactory) {
                $attributes['event_id'] = $attributes['event_id']->forPatient($patient);
            } else {
                $attributes['event_id']->episode->patient_id = $patient;
            }
            return [
                'event_id' => $attributes['event_id']
            ];
        });
    }

    public function onEventDate($date)
    {
        return $this->state(function ($attributes) use ($date) {
            if ($attributes['event_id'] instanceof ModelFactory) {
                $attributes['event_id'] = $attributes['event_id']->onEventDate($date);
            } else {
                $attributes['event_id']->event_date = $date;
            }

            return [
                'event_id' => $attributes['event_id']
            ];
        });
    }
}
