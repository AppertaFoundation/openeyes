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

namespace OEModule\OphCoMessaging\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\traits\MapsDisplayOrderForFactory;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;

class OphCoMessaging_Message_MessageTypeFactory extends ModelFactory
{
    use MapsDisplayOrderForFactory;

    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'reply_required' => false,
        ];
    }

    public function replyRequired(): self
    {
        return $this->state([
            'reply_required' => true
        ]);
    }

    public function replyNotRequired(): self
    {
        return $this->state([
            'reply_required' => false
        ]);
    }

    public function withEventSubtype($event_subtype = null): self
    {
        if (!$event_subtype) {
            $event_subtype = \EventSubtype::factory();
        }

        return $this->state([
            'event_subtype' => $event_subtype
        ]);
    }
}
