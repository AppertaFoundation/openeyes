<?php

namespace OEModule\OphCiExamination\factories\models;

use Eye;
use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OEModule\OphCiExamination\models\OphCiExamination_Primary_Reason_For_Surgery;
use SplitEventTypeElement;

class Element_OphCiExamination_CataractSurgicalManagementFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiExamination'),
            'eye_id' => Eye::factory()->useExisting(),
            'left_target_postop_refraction' => $this->faker->randomFloat(2, -3, 3),
            'right_target_postop_refraction' => $this->faker->randomFloat(2, -3, 3),
            'left_correction_discussed' => $this->faker->numberBetween(0, 1),
            'right_correction_discussed' => $this->faker->numberBetween(0, 1),
            'left_refraction_category' => $this->faker->numberBetween(0, 2),
            'right_refraction_category' => $this->faker->numberBetween(0, 2),
            'left_eye_id' => $this->faker->randomElement([SplitEventTypeElement::LEFT, SplitEventTypeElement::RIGHT]),
            'right_eye_id' => $this->faker->randomElement([SplitEventTypeElement::LEFT, SplitEventTypeElement::RIGHT]),
            'left_reason_for_surgery_id' => OphCiExamination_Primary_Reason_For_Surgery::factory()->useExisting(),
            'right_reason_for_surgery_id' => OphCiExamination_Primary_Reason_For_Surgery::factory()->useExisting(),
            'left_guarded_prognosis' =>  $this->faker->numberBetween(0, 1),
            'right_guarded_prognosis' =>  $this->faker->numberBetween(0, 1),
        ];
    }

    public function forEvent($event): self
    {
        return $this->state([
            'event_id' => $event
        ]);
    }

    public function forEye($eye_id)
    {
        return $this->state([
            'eye_id' => $eye_id,
        ]);
    }

    public function forSidedTargetRefraction($eye_id, $target_refraction): self
    {
        $side = strtolower(Eye::methodPostFix($eye_id));

        return $this->state([
            $side . "_target_postop_refraction" => $target_refraction,
        ]);
    }
}
