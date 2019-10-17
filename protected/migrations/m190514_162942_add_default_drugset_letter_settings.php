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
                'default_value' => '',
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

        foreach (\EpisodeStatus::model()->findAll('name = :name', [':name' => 'Post-op']) as $status) {
            foreach (['drug_set' => 'Drug Set', 'letter' => 'Letter'] as $type => $name) {
                $this->addSetting($field_type_id, "default_{$status->key}_{$type}", "Default {$status->name} {$name} name", $status->name);
            }
        }
        $this->addSetting(
            $field_type_id,
            "default_optom_post_op_letter",
            "Default Optom Post-op Letter name",
            'Community Optom'
        );

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 2,
            'field_type_id' => \SettingFieldType::model()->findByAttributes(['name' => 'Radio buttons'])->id,
            'key' => 'auto_generate_prescription_after_surgery',
            'name' => 'Auto generate default prescription after surgery',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'on'
        ));

        $this->insert('setting_installation', array(
            'key' => 'auto_generate_prescription_after_surgery',
            'value' => 'on'
        ));

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 2,
            'field_type_id' => \SettingFieldType::model()->findByAttributes(['name' => 'Radio buttons'])->id,
            'key' => 'auto_generate_gp_letter_after_surgery',
            'name' => 'Auto generate GP letter after surgery',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'on'
        ));

        $this->insert('setting_installation', array(
            'key' => 'auto_generate_gp_letter_after_surgery',
            'value' => 'on'
        ));

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 2,
            'field_type_id' => \SettingFieldType::model()->findByAttributes(['name' => 'Radio buttons'])->id,
            'key' => 'auto_generate_optom_post_op_letter_after_surgery',
            'name' => 'Auto generate Optom letter after surgery',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'on'
        ));

        $this->insert('setting_installation', array(
            'key' => 'auto_generate_optom_post_op_letter_after_surgery',
            'value' => 'on'
        ));

        // disable prescription and optom letter options for every subspecialty except cataract
        foreach (\Subspecialty::model()->findAll() as $subspecialty) {
            if ($subspecialty->name !== 'Cataract') {
                foreach (['auto_generate_prescription_after_surgery', 'auto_generate_optom_post_op_letter_after_surgery'] as $key) {
                    $this->insert('setting_subspecialty', [
                        'subspecialty_id' => $subspecialty->id,
                        'element_type_id' => null,
                        'key' => $key,
                        'value' => 'off',
                        'last_modified_user_id' => '1',
                        'last_modified_date' => date('Y-m-d H:i:s'),
                        'created_user_id' => '1',
                        'created_date' => date('Y-m-d H:i:s')
                    ]);
                }
                foreach (['default_post_op_drug_set', 'default_optom_post_op_letter'] as $key) {
                    $this->insert('setting_subspecialty', [
                        'subspecialty_id' => $subspecialty->id,
                        'element_type_id' => null,
                        'key' => $key,
                        'value' => '',
                        'last_modified_user_id' => '1',
                        'last_modified_date' => date('Y-m-d H:i:s'),
                        'created_user_id' => '1',
                        'created_date' => date('Y-m-d H:i:s')
                    ]);
                }
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

        foreach (['auto_generate_prescription_after_surgery', 'auto_generate_optom_post_op_letter_after_surgery'] as $key) {
            $this->delete('setting_subspecialty', '`key` = :key', [':key' => $key]);
        }
    }
}
