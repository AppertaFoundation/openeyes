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

class CommonOphthalmicDisorderWidgetBehaviourSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $expected_disorders = [
            $this->commonOphthalmicDisorderFactoryForUniqueTerm()
                ->withInstitution($this->app_context->getSelectedInstitution()->id)
                ->forDisplayOrder(2)
                ->create([
                    'subspecialty_id' => $this->app_context->getSelectedFirm()->getSubspecialtyID()
                ]),
            $this->commonOphthalmicDisorderFactoryForUniqueTerm()
                ->withInstitution($this->app_context->getSelectedInstitution()->id)
                ->forDisplayOrder(0)
                ->create([
                    'subspecialty_id' => $this->app_context->getSelectedFirm()->getSubspecialtyID()
                ]),
            // all institutions
            $this->commonOphthalmicDisorderFactoryForUniqueTerm()
                ->forDisplayOrder(1)
                ->create([
                    'subspecialty_id' => $this->app_context->getSelectedFirm()->getSubspecialtyID()
                ])
        ];


        $unexpected_disorders = [
            // other institution
            $this->commonOphthalmicDisorderFactoryForUniqueTerm()
                ->withInstitution($this->getOtherInstitution()->id)
                ->create([
                    'subspecialty_id' => $this->app_context->getSelectedFirm()->getSubspecialtyID()
                ]),
            // other subspecialty
            $this->commonOphthalmicDisorderFactoryForUniqueTerm()
                ->withInstitution($this->app_context->getSelectedInstitution()->id)
                ->create([
                    'subspecialty_id' => $this->getOtherSubspecialty()
                ]),
            ];

        return [
            'expected_disorders' => $this->mapDisorders($expected_disorders),
            'sorted_expected_disorder_ids' => [$expected_disorders[1]->disorder_id, $expected_disorders[2]->disorder_id, $expected_disorders[0]->disorder_id],
            'unexpected_disorders' => $this->mapDisorders($unexpected_disorders)
        ];
    }

    protected function commonOphthalmicDisorderFactoryForUniqueTerm()
    {
        return \CommonOphthalmicDisorder::factory()
            ->state([
                'disorder_id' => \Disorder::factory()
                    ->state(function ($attributes) {
                        return ['term' => $attributes['term'] . time()];
                    })->create(['specialty_id' => '109'])
                ]);
    }

    protected function mapDisorders(array $models): array
    {
        return array_map(
            function ($model) {
                return [
                    'id' => $model->disorder_id,
                    'name' => $model->disorder->term
                ];
            },
            $models
        );
    }

    protected function getOtherInstitution()
    {
        return \Institution::model()->find('id <> :id', [':id' => $this->app_context->getSelectedInstitution()->id])
            ?? \Institution::factory()->create();
    }

    protected function getOtherSubspecialty()
    {
        return \Subspecialty::model()->find('id <> :id', [':id' => $this->app_context->getSelectedFirm()->getSubspecialtyID()])
            ?? \Subspecialty::factory()->create();
    }
}
