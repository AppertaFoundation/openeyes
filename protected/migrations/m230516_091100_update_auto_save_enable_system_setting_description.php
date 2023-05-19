<?php

class m230516_091100_update_auto_save_enable_system_setting_description extends OEMigration
{
    private const SETTING_NAME = "auto_save_enabled";

    public function safeUp()
    {
        $this->update('setting_metadata', ['description' => 'Enables auto-save functionality in events.
When On, a draft save will occur in the background every x seconds. If the user does not cleanly exit the event (i.e. saves or cancels), then the draft will be available to re-open and continue with.
<br><br>
This is useful in situations such as a computer crash, desktop logging out due to being inactive, etc., etc.
<br><br>
<b>NOTE 1:</b> These auto save drafts are only visible to the user that created them and they will be automatically removed after 24 hours.
<br><br>
<b>NOTE 2:</b> Not all event types are suported. At the present time only the Examination event supports auto-save'], '`key` = "' . self::SETTING_NAME . '"');
    }

    public function down()
    {
        echo "m230516_091100_update_auto_save_enable_system_setting_description does not support migration down.\n";
    }
}
