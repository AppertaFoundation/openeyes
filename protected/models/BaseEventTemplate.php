<?php

abstract class BaseEventTemplate extends BaseActiveRecordVersioned
{
    abstract public function setupAndSave($event, $event_template, $template_json): bool;
    abstract public function getUpdateStatus($event, $old_data, $new_data, $data_has_changed);

    public function updateTemplate($new_template_json)
    {
        $this->template_data = $new_template_json;

        return $this->save();
    }
}
