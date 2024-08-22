<?php

namespace OEModule\OphCiExamination\factories\models;

use Event;
use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OEModule\OphCiExamination\models\HistoryRisks;
use OEModule\OphCiExamination\models\HistoryRisksEntry;
use OEModule\OphCiExamination\models\OphCiExaminationRiskSetEntry;

class HistoryRisksFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiExamination')
        ];
    }

    /**
     * @param Event|EventFactory|string|int $event
     * @return HistoryRisksFactory
     */
    public function forEvent($event): self
    {
        return $this->state([
            'event_id' => $event
        ]);
    }

    public function withEntries(int $count = 1)
    {
        return $this->afterCreating(function (HistoryRisks $risks_element) use ($count) {
            $risks_element->entries = ModelFactory::factoryFor(HistoryRisksEntry::class)
                ->count($count)
                ->create([
                    'element_id' => $risks_element->id
                ]);
        });
    }

    public function withRequiredEntries()
    {
        return $this->afterCreating(function (HistoryRisks $risks_element) {
            $subspecialty_id = $risks_element->event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id;
            $criteria = new \CDbCriteria();
            $criteria->join = "JOIN ophciexamination_risk_set rs ON rs.id = t.set_id";
            $criteria->condition = "rs.subspecialty_id = :subspecialty_id";
            $criteria->params[":subspecialty_id"] = $subspecialty_id;
            $required_risks = OphCiExaminationRiskSetEntry::model()->findAll($criteria);

            foreach ($required_risks as $required_risk) {
                HistoryRisksEntry::factory()
                    ->create([
                        'element_id' => $risks_element->id,
                        'risk_id' => $required_risk->ophciexamination_risk_id,
                        'has_risk' => HistoryRisksEntry::$NOT_CHECKED
                    ]);
            }
        });
    }

    protected function mapModelToFormData($model): array
    {
        $result = [
            'entries' => [],
            'present' => "1"
        ];

        foreach ($model->entries as $entry) {
            $result['entries'][] = [
                'risk_id' => $entry->risk_id,
                'has_risk' => $entry->has_risk,
                'comments' => $entry->comments
            ];
        }

        return $result;
    }
}
