<?php

namespace OEModule\CypressHelper\resources;

class SeededEventResource extends SeededResource
{
    private $instance = null;

    public static function from(\Event $event): self
    {
        $resource = new SeededEventResource();

        $resource->instance = $event;

        return $resource;
    }

    public function toArray(): array
    {
        return [
            'patient_id' => $this->instance->episode->patient->id,
            'view_url' => \Yii::app()->createUrl('/' . $this->instance->eventType->class_name . '/Default/view/' . $this->instance->id),
            'edit_url' => \Yii::app()->createUrl('/' . $this->instance->eventType->class_name . '/Default/update/' . $this->instance->id),
        ];
    }
}
