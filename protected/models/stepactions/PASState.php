<?php

class PASState extends StepStateType
{
    protected string $action_id = 'pas_callout';
    protected string $event_label = 'Send Message to PAS';
    public function toJSON(): array
    {
        return array_merge(
            parent::toJSON(),
            array(
                'message_type' => null, // HL7 trigger event.
                'args_list' => array() // Mappings of segments to values in OE.
            )
        );
    }

    public function getOptions(): array
    {
        // TODO: Return the list of supported HL7 trigger events.
        return array();
    }
}
