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

namespace OEModule\OphGeneric\seeders;

use ElementType;
use EventSubtypeElementEntry;
use OEModule\OphGeneric\models\Attachment;
use OEModule\OphGeneric\models\Comments;
use OEModule\OphGeneric\models\DeviceInformation;
use OEModule\OphGeneric\models\HFA;
use EventSubTypeItem;
use OE\factories\models\EventFactory;
use OELog;

class VisualFieldsSeeder
{
    protected \DataContext $data_context;
    protected string $event_subtype_pk = 'Visual Field Images';

    public function __construct(\DataContext $data_context)
    {
        $this->data_context = $data_context;
    }

    public function __invoke()
    {
        $this->resetManualEventSubtypes();

        $automated_event_element_types = [
            Attachment::class,
            Comments::class,
            DeviceInformation::class,
            HFA::class
        ];

        $manual_event_elements_types = [
            HFA::class,
            Comments::class
        ];

        $event_subtype = \EventSubtype::factory()->useExisting([
            'event_subtype' => $this->event_subtype_pk
        ])->create();

        // generate automated Visual Fields Image event
        $automated_event = EventFactory::forModule('OphGeneric')
            ->withSubType($event_subtype)
            ->withElements($automated_event_element_types)
            ->create();

        $manual_event_element_type_entries = [];
        foreach ($manual_event_elements_types as $manual_event_elements_type) {
            $manual_event_element_type_entries[] = EventSubtypeElementEntry::factory()->create([
                    'element_type_id' => ElementType::factory()->useExisting([
                        'class_name' => $manual_event_elements_type
                    ]),
                    'event_subtype' => $this->event_subtype_pk
                ]);
        }

        // setup manual config for same event subtype with limited elements
        $event_subtype->manual_entry = 1;
        $event_subtype->element_type_entries = $manual_event_element_type_entries;
        $event_subtype->save();

        // create manual event for manual subtype to validate edit behaviour
        $manual_event = EventFactory::forModule('OphGeneric')
            ->withSubType($event_subtype)
            ->withElements($manual_event_elements_types)
            ->create();

        return [
            'automated_event' => [
                'id' => $automated_event->id,
                'element_names' => $this->elementClassToName($automated_event_element_types)
            ],
            'manual_event' => [
                'id' => $manual_event->id,
                'element_names' => $this->elementClassToName($manual_event_elements_types)
            ]
        ];
    }

    protected function resetManualEventSubtypes()
    {
        // SQL that does that thing.
        $event_subtype = \EventSubtype::factory()->useExisting([
            'event_subtype' => $this->event_subtype_pk
        ])->create();
        $event_subtype->manual_entry = 0;
        $event_subtype->element_type_entries = [];
        $event_subtype->save();
    }

    protected function elementClassToName($elements)
    {
        return array_map(function ($element) {
            return substr($element, strrpos($element, '\\') + 1);
        }, $elements);
    }
}
