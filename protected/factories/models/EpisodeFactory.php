<?php

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\traits\HasFirm;
use Patient;

class EpisodeFactory extends ModelFactory
{
    use HasFirm;

    public function definition(): array
    {
        return [
            'patient_id' => ModelFactory::factoryFor(Patient::class),
            'firm_id' => ModelFactory::factoryFor(Firm::class)->useExisting()
        ];
    }

    public function forPatient(Patient $patient): self
    {
        return $this->state(function () use ($patient) {
            return [
                'patient_id' => $patient->id
            ];
        });
    }
    public function withPrincipalDiagnosis($disorder_id = null, $eye_id = null)
    {
        return $this->state(function () use ($disorder_id, $eye_id) {
            return [
                'eye_id' => $eye_id ?? ModelFactory::factoryFor(Eye::class)->useExisting(),
                'disorder_id' => $disorder_id ?? ModelFactory::factoryFor(Disorder::class)
            ];
        });
    }
}
