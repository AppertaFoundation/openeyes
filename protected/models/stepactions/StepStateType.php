<?php

abstract class StepStateType
{
    protected string $action_id;
    protected string $event_label;

    /**
     * @return array
     */
    public function toJSON(): array
    {
        return array(
            'action_type' => $this->action_id,
            'event_label' => $this->event_label
        );
    }

    /**
     * @return array
     */
    abstract public function getOptions(): array;
}
