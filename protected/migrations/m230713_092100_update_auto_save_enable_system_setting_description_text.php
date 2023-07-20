<?php

class m230713_092100_update_auto_save_enable_system_setting_description_text extends OEMigration
{
    private const SETTING_NAME = "auto_save_enabled";

    public function safeUp()
    {
        $this->update('setting_metadata', ['description' => 'Enables auto-save functionality in events.
When On, a draft save will occur in the background every 60 seconds. If the user does not cleanly exit the event (i.e. saves or cancels), then the draft will be available to re-open and continue or the rest of the day.
<br><br>
This is useful in situations such as a computer crash, desktop logging out due to being inactive, etc., etc.
<br><br>
<b>NOTE 1:</b> These auto save drafts are only visible to the user that created them and they will be automatically removed after 24 hours.
<br><br>
<b>NOTE 2:</b> Not all event types are suported. At the present time only the Examination event supports auto-save'], '`key` = "' . self::SETTING_NAME . '"');
    }

    public function safeDown()
    {
        echo "m230516_091100_update_auto_save_enable_system_setting_description does not support migration down.\n";
    }
}
