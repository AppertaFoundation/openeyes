<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphDrPrescription\seeders;

use http\Exception\RuntimeException;
use OE\seeders\BaseSeeder;
use ReferenceData;

class PharmacyWorklistSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $create_user = $this->getSeederAttribute('create_user', false);

        return [
            'institutions' =>  \Institution::model()->getList(),
            'sites_by_institution' => $this->getSites(),
            'firms_by_institution' => $this->getFirms(),
            'dispense_conditions_by_institution' => $this->getDispenseConditions(),
            'dispense_locations_by_institution' => $this->getDispenseLocations(),
            'logged_in_user_name' => \User::model()->findByPk(\Yii::app()->user->id)->getFullNameAndTitle(),
            'secondary_signatories_by_institution' => $this->getSecondarySignatories(),
            'test_user' => ($create_user && $create_user !== 'false') ? $this->createUser() : null,
        ];
    }

    private function getSites(): array
    {
        $collection = new \ModelCollection(\Site::model()->findAll());
        return $collection->groupBy('institution_id');
    }

    private function getFirms(): array
    {
        $collection = new \ModelCollection(\Firm::model()->findAll());
        return $collection->groupBy('institution_id');
    }

    private function getDispenseConditions(): array
    {
        $list = [];
        $conditions = \OphDrPrescription_DispenseCondition_Institution::model()->findAll();

        foreach ($conditions as $condition) {
            $list[$condition->id] = $list[$condition->id] ?? [];
            $dispense_condition = $condition->dispense_condition;
            $list[$condition->id][] = $dispense_condition;
        }

       return $list;
    }
    private function getDispenseLocations(): array
    {
        $list = [];
        $locations = \OphDrPrescription_DispenseLocation_Institution::model()->findAll();

        foreach ($locations as $location) {
            $list[$location->id] = $list[$location->id] ?? [];
            $dispense_condition = $location->dispense_location;
            $list[$location->id][] = $dispense_condition;
        }

       return $list;
    }

    private function getSecondarySignatories(): array
    {
        $signatories = \SecondarySignatory::model()->findAll();

        $list = [0 => []];

        foreach ($signatories as $signatory) {
            if ($signatory->institution_id) {
                $list[$signatory->institution_id][] = $signatory;
            } else {
                $list[0][] = $signatory;
            }
        }

        return $list;
    }

    private function createUser()
    {
        $institution = $this->app_context->getSelectedInstitution();
        $password = $this->getApp()->dataGenerator->faker()->password(8, 16, true, true);
        $user = \User::factory()
            ->withLocalAuthForInstitution($institution, $password)
            ->withAuthItems(['Edit', 'User', 'View clinical'])
            ->create();
        $username = $user->authentications[0]->username;

        return ['username' => $username, 'password' => $password, 'institution_id' => $institution->id];
    }
}
