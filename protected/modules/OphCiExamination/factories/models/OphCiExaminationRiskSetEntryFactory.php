<?php

namespace OEModule\OphCiExamination\factories\models;

use Institution;
use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\OphCiExaminationRisk;
use OEModule\OphCiExamination\models\OphCiExaminationRiskSet;

class OphCiExaminationRiskSetEntryFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'ophciexamination_risk_id' => OphCiExaminationRisk::factory()->create(),
            'set_id' => OphCiExaminationRiskSet::factory()->create()
        ];
    }

    /**
     *
     * @param OphCiExaminationRiskSet|string|int $set
     * @return OphCiExaminationRiskSetEntryFactory
     */
    public function forSet($set): self
    {
        return $this->state([
            'set_id' => $set
        ]);
    }
}
