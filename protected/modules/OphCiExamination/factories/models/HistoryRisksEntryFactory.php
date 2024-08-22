<?php

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\HistoryRisks;
use OEModule\OphCiExamination\models\HistoryRisksEntry;
use OEModule\OphCiExamination\models\OphCiExaminationRisk;

class HistoryRisksEntryFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'element_id' => ModelFactory::factoryFor(HistoryRisks::class),
            'risk_id' => ModelFactory::factoryFor(OphCiExaminationRisk::class),
            'has_risk' => $this->faker->randomElement([HistoryRisksEntry::$PRESENT, HistoryRisksEntry::$NOT_PRESENT, HistoryRisksEntry::$NOT_CHECKED])
        ];
    }
}
