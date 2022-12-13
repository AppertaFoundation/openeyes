<?php
namespace OEModule\OphTrOperationbooking\seeders;

use OE\factories\models\EpisodeFactory;
use OE\factories\models\EventFactory;
use OEModule\CypressHelper\resources\SeededEventResource;

class OpBookingWithProceduresSeeder
{
    public function __construct(\DataContext $context)
    {
        $this->context = $context;
    }

    public function __invoke()
    {
        $episode = EpisodeFactory::new(['patient_id' => $this->context->additional_data['patient_id']])->create();

        $procedure_names = $this->context->additional_data['procedure_names'];

        $procedures = array_map(function(string $procedure_name) {
            return \Procedure::factory()->useExisting(['term' => $procedure_name])->create();
        }, $procedure_names);

        $event = EventFactory::forModule('OphTrOperationbooking')
            ->bookedWithStates(
                [
                    'withSingleEye',
                    'withRequiresScheduling'
                ]);

        $event = $event->create(['episode_id' => $episode->id]);

        $operation_element = \Element_OphTrOperationbooking_Operation::model()
            ->findbyAttributes(['event_id' => $event->id]);

        foreach ($procedures as $procedure) {
            \OphTrOperationbooking_Operation_Procedures::factory()->create([
                'element_id' => $operation_element->id,
                'proc_id' => $procedure->id
            ]);
        }

        return [
            'event' =>  SeededEventResource::from($event)->toArray(),
        ];
    }
}
