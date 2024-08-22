<?php

namespace OEModule\OphCoCvi\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OE\factories\models\traits\LooksUpExistingModels;

class Element_OphCoCvi_EventInfoFactory extends ModelFactory
{
    use LooksUpExistingModels;

    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCoCvi')
        ];
    }

    public function draft()
    {
        return $this->state(function ($attributes) {
            return [
                'is_draft' => true
            ];
        });
    }

    public function notDraft()
    {
        return $this->state(function ($attributes) {
            return [
                'is_draft' => false
            ];
        });
    }

    /**
     * @param string|array|Site $site
     */
    public function withSite($site = null)
    {
        $site = $this->mapToFactoryOrId(Site::class, $site);

        return $this->state(function ($attributes) use ($site) {

            return [
                'site_id' => $site
            ];
        });
    }

    /**
     * N.B. this stored as a consultant id, but its actually a relation to a firm
     *
     * @param string|array|Firm $firm
     */
    public function withFirm($firm = null)
    {
        $firm = $this->mapToFactoryOrId(Firm::class, $firm);

        return $this->state(function ($attributes) use ($firm) {
            return [
                'consultant_in_charge_of_this_cvi_id' => $firm
            ];
        });
    }
}
