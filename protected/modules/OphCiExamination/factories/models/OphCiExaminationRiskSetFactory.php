<?php

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;
use Institution;
use OE\factories\models\traits\LooksUpExistingModels;
use Subspecialty;

class OphCiExaminationRiskSetFactory extends ModelFactory
{
    use LooksUpExistingModels;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'institution_id' => Institution::factory()->create(),
            'subspecialty_id' => Subspecialty::factory()->create()
        ];
    }

    /**
     *
     * @param Institution|string|int $institution
     * @return OphCiExaminationRiskSetFactory
     */
    public function forInstitution($institution): self
    {
        return $this->state([
            'institution_id' => $institution
        ]);
    }

    /**
     *
     * @param Subspecialty|string|int $subspecialty
     * @return OphCiExaminationRiskSetFactory
     */
    public function forSubspecialty($subspecialty): self
    {
        return $this->state([
            'subspecialty_id' => $subspecialty
        ]);
    }

    public function existingForSubspecialty($subspecialty)
    {
        return $this->useExisting([
            'subspecialty_id' => $this->mapToFactoryOrId(Subspecialty::class, $subspecialty, 'id')
        ]);
    }
}
