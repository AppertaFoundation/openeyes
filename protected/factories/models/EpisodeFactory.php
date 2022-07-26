<?php

namespace OE\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\traits\HasFirm;

class EpisodeFactory extends ModelFactory
{
    use HasFirm;

    public function definition(): array
    {
        return [
            'patient_id' => ModelFactory::factoryFor(\Patient::class),
            'firm_id' => ModelFactory::factoryFor(Firm::class)->useExisting()
        ];
    }
}
