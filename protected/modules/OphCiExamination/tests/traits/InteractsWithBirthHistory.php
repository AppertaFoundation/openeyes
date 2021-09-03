<?php


namespace OEModule\OphCiExamination\tests\traits;


use OEModule\OphCiExamination\models\BirthHistory;
use OEModule\OphCiExamination\models\BirthHistory_DeliveryType;

trait InteractsWithBirthHistory
{
    use \WithFaker;
    use \InteractsWithEventTypeElements;

    // pass in the weight_recorded_units to force the units generated
    // otherwise a random unit will be selected
    public function generateBirthHistoryData($attrs = [])
    {
        $generated = [
            'birth_history_delivery_type_id' => $this->getRandomLookup(BirthHistory_DeliveryType::class)->id,
            'gestation_weeks' => $this->faker->numberBetween(20, 42),
            'had_neonatal_specialist_care' => $this->getValidNRBooleanValue(),
            'was_multiple_birth' => $this->getValidNRBooleanValue(),
            'comments' => $this->faker->sentences(3, true)
        ];
        if (!array_key_exists('weight_recorded_units', $attrs)) {
            $attrs['weight_recorded_units'] = $this->faker->randomElement([BirthHistory::$WEIGHT_GRAMS, BirthHistory::$WEIGHT_OZS]);
        }
        if ($attrs['weight_recorded_units'] === BirthHistory::$WEIGHT_GRAMS) {
            $generated['weight_grams'] = $this->faker->numberBetween(225, 10000);
        } else {
            $generated['weight_ozs'] = $this->faker->numberBetween(8, 354);
        }

        return array_merge($generated, $attrs);
    }

    protected function getNRBooleanValues()
    {
        return [BirthHistory::$YES, BirthHistory::$NO, BirthHistory::$NOT_RECORDED];
    }

    protected function getValidNRBooleanValue()
    {
        return $this->getNRBooleanValues()[array_rand($this->getNRBooleanValues())];
    }

    protected function getInvalidNRBooleanValue()
    {
        $valid_values = $this->getNRBooleanValues();

        $invalid = rand(min($valid_values) - 100, max($valid_values) + 100);
        while (in_array($invalid, $valid_values)) {
            $invalid = rand(min($valid_values) - 100, max($valid_values) + 100);
        }

        return $invalid;
    }
}