<?php

namespace OEModule\OphCoCvi\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use Gender;

class Element_OphCoCvi_DemographicsFactory extends ModelFactory
{

    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCoCvi')
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function ($instance) {
            // use attached patient data if data is not set
            if (!$instance->date_of_birth && $patient = $instance->event->getPatient()) {
                $instance->initFromPatient($patient);
                // override gender because we can't trust the init behaviour on this at the moment
                // FIXME: can remove this once OE-13294 is resolved
                $instance->gender_id = ModelFactory::factoryFor(Gender::class)->useExisting()->create()->id;
            }
        });
    }
}
