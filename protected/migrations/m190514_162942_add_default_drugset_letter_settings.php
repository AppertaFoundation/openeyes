<?php

class m190514_162942_add_default_drugset_letter_settings extends CDbMigration
{

    private function addSetting($field_type_id, $key, $name, $default_value)
    {
        $this->insert('setting_metadata', [
                'element_type_id' => null,
                'field_type_id' => $field_type_id,
                'key' => $key,
                'name' => $name,
                'default_value' => $default_value,
                'data' => '',
                'display_order' => 2
            ]
        );
        $this->insert('setting_installation', ['key' => $key, 'value' => $default_value]);
    }

	public function safeUp()
	{
        $this->addColumn('episode_status', 'key', 'VARCHAR(25) DEFAULT NULL AFTER name');
        $this->addColumn('episode_status_version', 'key', 'VARCHAR(25) DEFAULT NULL AFTER name');

        $this->update('episode_status', ['key' => 'new'], 'name = "New"');
        $this->update('episode_status', ['key' => 'under_investigation'], 'name = "Under investigation"');
        $this->update('episode_status', ['key' => 'list_booked'], 'name = "Listed/booked"');
        $this->update('episode_status', ['key' => 'post_op'], 'name = "Post-op"');
        $this->update('episode_status', ['key' => 'follow_up'], 'name = "Follow-up"');
        $this->update('episode_status', ['key' => 'discharged'], 'name = "Discharged"');

        //refresh event table schema
        Yii::app()->db->schema->getTable('episode_status', true);
        Yii::app()->db->schema->getTable('episode_status_version', true);

        $field_type_id = \SettingFieldType::model()->findByAttributes(['name' => 'Text Field'])->id;

        foreach (\EpisodeStatus::model()->findAll() as $status) {
            foreach (['drug_set' => 'Drug Set', 'letter' => 'Letter'] as $type => $name) {
                $this->addSetting($field_type_id, "default_{$status->key}_{$type}", "Default {$status->name}(Episode status) {$name} name", $status->name);
            }
        }
	}

	public function safeDown()
	{
        foreach (['new', 'under_investigation', 'list_booked', 'post_op', 'follow_up', 'discharged'] as $status) {
            foreach (['drug_set', 'letter'] as $type) {
                $this->delete('setting_metadata', ['key' => "default_{$status}_$type"]);
                $this->delete('setting_installation', ['key' => "default_{$status}_$type"]);
            }
	    }

        $this->dropColumn('episode_status', 'key');
        $this->dropColumn('episode_status_version', 'key');
	}
}
