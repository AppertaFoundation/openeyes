<?php

class m170929_132400_modify_system_setting_therapy_sender extends CDbMigration
{
    public function up()
    {
        $this->update('setting_metadata', array('field_type_id' => '4'), "`key` = 'OphCoTherapyapplication_sender_email'");
    }

    public function down()
    {
        ## No down required
    }
}
