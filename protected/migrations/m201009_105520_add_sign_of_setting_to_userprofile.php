<?php

class m201009_105520_add_sign_of_setting_to_userprofile extends \OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('user', 'correspondence_sign_off_user_id', 'INT(10) UNSIGNED DEFAULT NULL', true);
        $this->addForeignKey('user_corresp_sign_of_uid', 'user', 'correspondence_sign_off_user_id', 'user', 'id');

        $this->update('user', ['correspondence_sign_off_user_id' => new \CDbExpression('id')]);
    }

    public function safeDown()
    {
        $this->dropForeignKey('user_corresp_sign_of_uid', 'user');
        $this->dropOEColumn('user', 'correspondence_sign_off_user_id', true);
    }
}
