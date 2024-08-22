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

use Institution;

use OEModule\OphCiExamination\models\OphCiExamination_Instrument;

class IOPInstrumentsSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $current_institution = $this->app_context->getSelectedInstitution();
        $other_institution = Institution::factory()->create();

        $instrument_for_current = OphCiExamination_Instrument::factory()
                                ->forInstitutions([$current_institution])
                                ->create();

        $instrument_for_other = OphCiExamination_Instrument::factory()
                                ->forInstitutions([$other_institution])
                                ->create();

        $instrument_for_both = OphCiExamination_Instrument::factory()
                                ->forInstitutions([$current_institution, $other_institution])
                                ->create();

        $instrument_for_all = OphCiExamination_Instrument::factory()->create();

        return array_combine(
            ['instrumentForCurrent', 'instrumentForOther', 'instrumentForBoth', 'instrumentForAll'],
            $this->mapModels([$instrument_for_current, $instrument_for_other, $instrument_for_both, $instrument_for_all])
        );
    }

    protected function mapModels(array $models): array
    {
        return array_map(
            function ($model) {
                return [
                    'id' => $model->getPrimaryKey(),
                    'name' => $model->name
                ];
            },
            $models
        );
    }
}
