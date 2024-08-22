<?php

abstract class BaseEventTemplate extends BaseActiveRecordVersioned
{
    public $template_data = null;

    abstract public function setupAndSave($event, $event_template, $template_json): bool;
    abstract public function getUpdateStatus($event, $old_data, $new_data, $data_has_changed);

    public function updateTemplate($new_template_json)
    {
        $this->template_data = $new_template_json;

        return $this->save();
    }

    /**
     * Scope to limit to templates owned by the given user id
     *
     * @param mixed $user_id
     * @return BaseEventTemplate
     */
    public function forUserId($user_id): self
    {
        $this->getDbCriteria()
            ->mergeWith([
                'with' => [
                    'user_assignment' => [
                        'joinType' => 'INNER JOIN',
                        'condition' => 'user_id = :forUserUserId',
                        'params' => [':forUserUserId' => $user_id]
                    ]
                ]
            ]);

        return $this;
    }

    public function getname()
    {
        return $this->event_template->name;
    }
}
