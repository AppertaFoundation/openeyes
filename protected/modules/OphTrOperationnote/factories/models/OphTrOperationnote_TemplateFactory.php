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

/**
 * This factory is a simplified implementation for template creation - it does not currently provide a systematic method
 * for generating valid op note data for the possible elements that could be recorded. Instead, the `template_data` attribute
 * for the template should be provided as json encoded string.
 *
 * In the future, it would be useful to link the template data with the generated source data.
 */
class OphTrOperationnote_TemplateFactory extends ModelFactory
{
    protected static $event_type_id;

    public static function getEventTypeId()
    {
        if (is_null(static::$event_type_id)) {
            static::$event_type_id = EventType::model()->find('class_name = :cls_name', [':cls_name' => 'OphTrOperationnote'])->id;
        }

        return static::$event_type_id;
    }

    public function definition(): array
    {
        return [
            'event_template_id' => EventTemplate::factory()->state(['event_type_id' => self::getEventTypeId()]),
            'proc_set_id' => ProcedureSet::factory(),
            'template_data' => '[]'
        ];
    }

    public function make(array $attributes = [], bool $canCreate = false)
    {
        $this->afterMaking(function (OphTrOperationnote_Template $template) {
            // allows structured data to be passed in to the factory for saving
            if (is_array($template->template_data)) {
                $template->template_data = json_encode($template->template_data);
            }
        });

        return parent::make($attributes, $canCreate);
    }

    /**
     * Define the user that should be assigned to the template
     *
     * @param mixed $user
     * @return OphTrOperationnote_TemplateFactory
     */
    public function forUser($user): self
    {
        return $this->state(function ($attributes) use ($user) {
            if ($attributes['event_template_id'] instanceof ModelFactory) {
                return [
                    'event_template_id' => $attributes['event_template_id']->forUser($user)
                ];
            }
        });
    }

    public function withProcedures($procedures): self
    {
        return $this->state([
            'proc_set_id' => ProcedureSet::factory()->withProcedures($procedures)
        ]);
    }
}
