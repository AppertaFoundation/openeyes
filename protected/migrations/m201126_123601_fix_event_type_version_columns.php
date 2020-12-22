<?php

class m201126_123601_fix_event_type_version_columns extends OEMigration
{
    public function safeUp()
    {
        $table = Yii::app()->db->schema->getTable('event_type_version');
        if (isset($table->columns['display_order'])) {
            $this->dropColumn('event_type_version', 'display_order');
        }
        $this->alterColumn('event_type_version', 'version_date', 'datetime AFTER hint_position');
        $this->alterColumn('event_type_version', 'version_id', 'INT(10) UNSIGNED auto_increment AFTER version_date');
    }

    public function safeDown()
    {
        echo "m201126_123601_fix_event_type_version_columns does not support migration down.\n";
        return false;
    }
}
