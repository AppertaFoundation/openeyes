<?php
namespace OEModule\OphDrPGDPSD\seeders;

use OE\seeders\resources\{
    SeededUserResource,
    SeededEventResource
};

use OE\seeders\BaseSeeder;
use OEModule\OphDrPGDPSD\models\Element_DrugAdministration;
use User;

class EventPendingDeletionSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $institution = $this->app_context->getSelectedInstitution();

        $user = User::factory()
            ->withLocalAuthForInstitution($institution)
            ->withAuthItems([
                    'User',
                    'Edit',
                    'View clinical',
                    'Prescribe',
                ])
            ->withDefaultWorklistFilter()
            ->create();

        $element = Element_DrugAdministration::factory()->withEntries($institution, 1)->create();
        $event = $element->event;
        $patient = $event->episode->patient;

        $event->requestDeletion('Testing');
        $event->save();

        return [
            'user' => SeededUserResource::from($user)->toArray(),
            'event' => SeededEventResource::from($event)->toArray(),
            'worklistName' => $element->assignments[0]->worklist_patient->worklist->name,
            'visitId' => $element->assignments[0]->visit_id,
        ];
    }
}
