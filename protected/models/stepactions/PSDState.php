<?php

class PSDState extends StepStateType
{
    protected string $action_id = 'manage_psd';
    protected string $event_label = 'Manage PSD administration';
    public function toJSON(): array
    {
        return array_merge(
            parent::toJSON(),
            array(
                'preset_id' => null,
                'laterality' => null
            )
        );
    }

    public function getOptions(): array
    {
        // No customisation options for PSD administration.
        return array();
    }
}
