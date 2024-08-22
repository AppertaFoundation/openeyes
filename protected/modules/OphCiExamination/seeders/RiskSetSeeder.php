<?php

namespace OEModule\OphCiExamination\seeders;

use OE\seeders\BaseSeeder;
use OE\seeders\resources\GenericModelResource;
use OEModule\OphCiExamination\factories\models\OphCiExaminationRiskSetEntryFactory;
use OEModule\OphCiExamination\factories\models\OphCiExaminationRiskSetFactory;

class RiskSetSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $risk_set = OphCiExaminationRiskSetFactory::new(
            [
                'institution_id' => $this->app_context->getSelectedInstitution(),
            ]
        )
            ->existingForSubspecialty($this->getSeederAttribute('subspecialty_id'))
            ->create();

        if (empty($risk_set->entries)) {
            $risk_set_entry1 = OphCiExaminationRiskSetEntryFactory::new([
                'set_id' => $risk_set
            ])->create();

            $risk_set_entry2 = OphCiExaminationRiskSetEntryFactory::new([
                'set_id' => $risk_set
            ])->create();
        }

        $risk_set_resource = GenericModelResource::from($risk_set);

        return $risk_set_resource->toArray();
    }
}
