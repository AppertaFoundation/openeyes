<?php

class m170511_093723_remove_internal_referral_option extends CDbMigration
{
    public function up()
    {
        $this->delete("ophcocorrespondence_internal_referral_settings", '`key` = "internal_referral_booking_address"');
        $this->delete("setting_internal_referral", "`key` = 'internal_referral_booking_address'");
    }

    public function down()
    {
        echo "m170511_093723_remove_internal_referral_option does not support migration down.\n";
        return false;
    }
}