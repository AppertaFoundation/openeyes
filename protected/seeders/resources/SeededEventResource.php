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

namespace OE\seeders\resources;

class SeededEventResource extends SeededResource
{
    // flag to determine whether to return all data or just summary data
    protected bool $summary = false;
    // flag to indicate whether event elements should be returned - ignored in summary mode
    protected bool $with_elements = false;

    public static function from(\Event $event): self
    {
        return new SeededEventResource($event);
    }

    public function inFull(bool $with_elements = true): self
    {
        $this->summary = false;
        $this->with_elements = $with_elements;

        return $this;
    }

    public function inSummary(): self
    {
        $this->summary = true;
        return $this;
    }

    public function withoutElements(): self
    {
        $this->with_elements = false;

        return $this;
    }

    public function withElements(): self
    {
        $this->with_elements = true;

        return $this;
    }

    public function toArray(): array
    {
        return $this->summary ? $this->toSummaryArray() : $this->toFullArray();
    }

    protected function toFullArray(): array
    {
        $data = [
            'id' => $this->instance->id,
            'urls' => $this->urlsArray(),
            'patient' => SeededPatientResource::from($this->instance->episode->patient)->toArray()
        ];

        if ($this->with_elements) {
            $data['elements'] = array_map(
                function ($element) {
                    return [
                        'element_type' => $element->getElementTypeName(),
                        'attributes' => GenericModelResource::from($element)->exclude(['event'])->toArray()
                    ];
                },
                $this->instance->getElements()
            );
        }

        return $data;
    }

    protected function toSummaryArray(): array
    {
        return array_merge(
            [
                'patient_id' => $this->instance->episode->patient->id,
            ],
            [
                'urls' => $this->urlsArray()
            ]
        );
    }

    protected function urlsArray(): array
    {
        return [
            'view' => \Yii::app()->createUrl('/' . $this->instance->eventType->class_name . '/Default/view/?id=' . $this->instance->id),
            'edit' => \Yii::app()->createUrl('/' . $this->instance->eventType->class_name . '/Default/update/?id=' . $this->instance->id)
        ];
    }
}
