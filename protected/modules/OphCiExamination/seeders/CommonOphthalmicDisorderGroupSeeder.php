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

use CommonOphthalmicDisorder;
use CommonOphthalmicDisorderGroup;
use Institution;
use OE\factories\models\CommonOphthalmicDisorderGroupFactory;
use OE\seeders\BaseSeeder;
use OE\seeders\resources\GenericModelResource;
use OE\seeders\resources\SeededPatientResource;
use Patient;

class CommonOphthalmicDisorderGroupSeeder extends BaseSeeder
{
    protected ?string $unique_postfix = null;

    public function __invoke(): array
    {
        $this->unique_postfix = $this->getSeederAttribute('unique_postfix', (string) time());

        $groups = [
            $this->getGroupFactory()
                ->forDisplayOrder(2)
                ->create(),
            $this->getGroupFactory()
                ->forDisplayOrder(1)
                ->create(),
            $this->getGroupFactory()
                ->forDisplayOrder(3)
                ->create(),
                $this->getGroupFactory(true)
                ->forDisplayOrder(3)
                ->create()
        ];

        $group1_disorder = CommonOphthalmicDisorder::factory()
            ->withInstitution($this->app_context->getSelectedInstitution())
            ->forGroup($groups[0])
            ->forSubspecialty($this->app_context->getSelectedFirm()->getSubspecialty())
            ->create();

        $group2_disorder = CommonOphthalmicDisorder::factory()
            ->withInstitution($this->app_context->getSelectedInstitution())
            ->forGroup($groups[1])
            ->forSubspecialty($this->app_context->getSelectedFirm()->getSubspecialty())
            ->create();

        $ungrouped_disorder = CommonOphthalmicDisorder::factory()
            ->withInstitution($this->app_context->getSelectedInstitution())
            ->forSubspecialty($this->app_context->getSelectedFirm()->getSubspecialty())
            ->create();


        return [
            'patient' => SeededPatientResource::from(Patient::factory()->create())->toArray(),
            'expected_groups' => [
                [
                    'group' => $this->mapGroup($groups[0]),
                    'disorders' => [$this->mapDisorder($group1_disorder)]
                ],
                [
                    'group' => $this->mapGroup($groups[1]),
                    'disorders' => [$this->mapDisorder($group2_disorder)]
                ],
            ],
            'expected_disorders' => [$this->mapDisorder($ungrouped_disorder)],
            'unexpected_groups' => [$this->mapGroup($groups[2]), $this->mapGroup($groups[3])]
        ];
    }

    private function getGroupFactory(bool $other_institution = false): CommonOphthalmicDisorderGroupFactory
    {
        return CommonOphthalmicDisorderGroup::factory()
            ->withInstitution($other_institution ? $this->getOtherInstitution() : $this->app_context->getSelectedInstitution())
            ->withUniquePostfix($this->unique_postfix)
            ->withDBUniqueAttribute('name');
    }

    private function mapGroup(CommonOphthalmicDisorderGroup $group)
    {
        return GenericModelResource::from($group)->exclude(['institution', 'subspecialty'])->toArray();
    }

    private function mapDisorder(CommonOphthalmicDisorder $disorder)
    {
        return GenericModelResource::from($disorder)
            ->exclude(['finding', 'subspecialty', 'group', 'institution'])
            ->toArray();
    }

    private function getOtherInstitution()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('id <> :id');
        $criteria->params[':id'] = $this->app_context->getSelectedInstitution()->id;
        return Institution::model()->find($criteria);
    }
}
