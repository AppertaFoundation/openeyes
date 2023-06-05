<?php

namespace OE\factories\models;

use Institution;
use OE\factories\ModelFactory;
use ServiceSubspecialtyAssignment;
use Subspecialty;

class FirmFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'subspecialty_id' => Subspecialty::factory()->useExisting(),
            'institution_id' => Institution::factory()->useExisting()
        ];
    }

    /**
     * @param User|UserFactory|int|string|null $user
     * @return self
     */
    public function withConsultant($user = null): self
    {
        $user ??= \User::factory()->withContact()->create();
        return $this->state([
            'consultant_id' => $user
        ]);
    }

    public function withSubspecialty($subspecialty = null): self
    {
        $subspecialty ??= \Subspecialty::factory()->create();
        return $this->state([
            'subspecialty_id' => $subspecialty,
            'service_subspecialty_assignment_id' => function ($attributes) {
                return ServiceSubspecialtyAssignment::factory()->state([
                    'subspecialty_id' => $attributes['subspecialty_id']
                ]);
            }
        ]);
    }

    public function cannotOwnEpisode(): self
    {
        return $this->state([
            'can_own_an_episode' => false
        ]);
    }

    public function canOwnEpisode(): self
    {
        return $this->state([
            'can_own_an_episode' => true
        ]);
    }
}
