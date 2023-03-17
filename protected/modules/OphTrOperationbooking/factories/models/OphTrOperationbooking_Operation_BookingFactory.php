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

use OE\factories\ModelFactory;

class OphTrOperationbooking_Operation_BookingFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'element_id' => Element_OphTrOperationbooking_Operation::factory(),
            'session_id' => OphTrOperationbooking_Operation_Session::factory()
        ];
    }

    protected function getExpandedAttributes(array $definition, $canCreate = true): array
    {
        $definition = parent::getExpandedAttributes($definition, $canCreate);
        $session = OphTrOperationbooking_Operation_Session::model()->findByPk($definition['session_id']);
        if ($session) {
            foreach (
                [
                    'default_admission_time' => 'admission_time',
                    'date' => 'session_date',
                    'start_time' => 'session_start_time',
                    'end_time' => 'session_end_time',
                    'theatre_id' => 'session_theatre_id'
                ] as $session_attr => $booking_attr
            ) {
                // if the booking atttributes have not been provided, they default
                // to the session that has been associated with the booking
                $definition[$booking_attr] ??= $session->$session_attr;
            }

            $definition['ward_id'] ??= $this->resolveWardIdFromSession($session, $canCreate);
        }
        return $definition;
    }

    /**
     * Uses the given session to either create or get existing ward at the same site.
     *
     * Returns the primary key as a string (default Yii behaviour)
     *
     * @param OphTrOperationbooking_Operation_Session $sesion
     * @param boolean $canCreate
     * @return string
     */
    private function resolveWardIdFromSession(OphTrOperationbooking_Operation_Session $session, bool $canCreate = true): string
    {
        $factory = OphTrOperationbooking_Operation_Ward::factory()
            ->state([
                'site_id' => $session->theatre->site_id
            ]);

        if ($canCreate) {
            return $factory->create()->getPrimaryKey();
        }

        return $factory->useExisting()->make()->getPrimaryKey();
    }
}
