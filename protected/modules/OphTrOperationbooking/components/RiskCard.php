<?php

    /**
     * Class RiskCard
     * This is a bespoke whiteboard card that displays patient allergies and risks.
     */
class RiskCard extends WBCard
{
    public $whiteboard;
    protected $baseViewFile = 'wb_allergies_and_risks';

    public function init()
    {
        // We are deliberately overriding this here because we don't want the generic initialisation to occur.
    }
}