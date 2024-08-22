<?php

namespace OEModule\OphCoCvi\factories;

use OE\factories\models\EventFactory;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics;
use OEModule\OphCoCvi\models\Element_OphCoCvi_Consent;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo;
use OE\factories\ModelFactory;


class OphCoCviFactory extends EventFactory
{
    protected static $requiredElements = [
        Element_OphCoCvi_EventInfo::class,
        Element_OphCoCvi_Demographics::class,
        // Element_OphCoCvi_Consent::class,
        // Element_OphCoCvi_ClinicalInfo::class,
        // Element_OphCoCvi_ClericalInfo::class
    ];

    public function definition(): array
    {
        return array_merge(
            parent::definition(),
            [
                'event_type_id' => $this->getEventTypeByName('CVI')
            ]
        );
    }

    public function configure()
    {
        parent::configure();

        foreach (static::$requiredElements as $requiredElementClass) {
            $this->afterCreating(function ($event) use ($requiredElementClass) {
                ModelFactory::factoryFor($requiredElementClass)->create([
                    'event_id' => $event->id
                ]);
            });
        }

        return $this;
    }
}
