<?php

namespace OE\factories\models;

use Institution;
use OE\factories\ModelFactory;
use PathwayType;
use PathwayTypeStep;

class PathwayTypeFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'active' => true,
            'institution_id' => Institution::factory()->useExisting()
        ];
    }

    public function create(array $attributes = [])
    {
        $this->afterCreating(function (PathwayType $pathway_type) {
            foreach ($pathway_type->default_steps as $default_step) {
                if ($default_step instanceof ModelFactory) {
                    $default_step->create(['pathway_type_id' => $pathway_type->id]);
                } else {
                    $default_step->pathway_type_id = $pathway_type->id;
                    $default_step->save();
                }
            }
        });

        return parent::create($attributes);
    }

    /**
     * A state to specify the step types that should be part of the default pathway
     * for this PathwayType.
     *
     * @see PathwayTypeStepFactory
     * @param array $type_short_names
     * @return self
     */
    public function withStepsOfType(array $type_short_names = []): self
    {
        return $this->afterMaking(function (PathwayType $pathway_type) use ($type_short_names) {
            $pathway_type->default_steps = array_merge(
                $pathway_type->default_steps ?? [],
                array_map(
                    function ($type_short_name) {
                        return PathwayTypeStep::factory()->ofStepType($type_short_name);
                    },
                    $type_short_names
                )
            );
        });
    }
}
