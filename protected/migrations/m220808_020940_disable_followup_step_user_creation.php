<?php

class m220808_020940_disable_followup_step_user_creation extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->update(
            'pathway_step_type',
            ['user_can_create' => 0],
            'short_name = \'Book Apt.\''
        );
    }

    public function safeDown()
    {
        $this->update(
            'pathway_step_type',
            ['user_can_create' => 1],
            'short_name = \'Book Apt.\''
        );
    }
}
