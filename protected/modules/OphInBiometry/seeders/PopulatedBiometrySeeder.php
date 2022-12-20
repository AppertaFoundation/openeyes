<?php
namespace OEModule\OphInBiometry\seeders;

use OE\factories\models\EventFactory;
use OEModule\CypressHelper\resources\SeededEventResource;

class PopulatedBiometrySeeder
{
    public function __construct(\DataContext $context)
    {
        $this->context = $context;
    }

    public function __invoke()
    {
        $event = EventFactory::forModule('OphInBiometry')->create();
        $measurement = \Element_OphInBiometry_Measurement::factory()->new(['event_id' => $event->id]);

        return [
            'event' => SeededEventResource::from($event)->toArray(),
        ];
    }
}
