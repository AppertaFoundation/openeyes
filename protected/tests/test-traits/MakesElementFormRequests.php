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

use ElementType;
use User;
use Patient;
use Symfony\Component\DomCrawler\Crawler;

trait MakesElementFormRequests
{
    use MakesApplicationRequests;

    public function getElementForm(ElementType $element_type, ?Patient $patient = null, ?User $user = null): Crawler
    {
        $patient ??= Patient::factory()->create();

        $user ??= User::factory()->withAuthItems([
            'User',
            'Edit',
            'View clinical'
        ])->create();

        $url = '/' . $element_type->event_type->class_name . '/Default/ElementForm?' . http_build_query([
            'id' => $element_type->id,
            'patient_id' => $patient->id,
            'event_id' => '',
            'previous_id' => '',
        ]);

        return $this->actingAs($user)->get($url)
            ->assertSuccessful()
            ->crawl();
    }

    public function extractElementDirtyValue($model, $response)
    {
        $element_dirty_field = $response->filter('input[name="[element_dirty]' . \CHtml::modelName($model) . '"]');

        $this->assertEquals($element_dirty_field->count(), 1);

        return $element_dirty_field->extract(['value'])[0];
    }
}
