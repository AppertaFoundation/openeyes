<?php

namespace OE\factories\models\traits;

use Firm;
use OE\factories\ModelFactory;

trait HasFirm
{
    public function forFirmWithName(string $firmName)
    {
        return $this->state(function ($attributes) use ($firmName) {
            return [
                'firm_id' => ModelFactory::factoryFor(Firm::class)->useExisting([
                    'name' => $firmName
                ])
            ];
        });
    }
}
