<?php
// migration to change description of setting_metadata for the row where `key` = 'event_lock_days'
class m230417_153500_update_event_lock_days_descripton extends OEMigration {
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp() {
        $this->update('setting_metadata', ['description' => 'The number of complete days that any newly created event should be editable for.
  E.g., If set to 0 and an Examination event is created at 11:00AM on Tuesday, it will be editable until 11:59:59PM on Tuesday. After that the edit button will be removed and the event will be locked. 
  If set to 1, that same event would have been editable until 11:59:59PM on Wednesday. Note that system admins can stil edit an event at any time.'], "`key` = 'event_lock_days'");
    }

    public function safeDown() {
        echo "m230417_153500_update_event_lock_days_descripton does not support migration down.\n";
    }
}