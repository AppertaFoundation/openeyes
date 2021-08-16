<?php

class m210813_141800_remove_display_order extends OEMigration
{
    public function safeUp()
    {
        $table = Yii::app()->db->schema->getTable('event_type', true);
        if (isset($table->columns['display_order'])) {
            $this->dropColumn('event_type', 'display_order');
        }
        $table = Yii::app()->db->schema->getTable('event_type_version', true);
        if (isset($table->columns['display_order'])) {
            $this->dropColumn('event_type_version', 'display_order');
        }
    }

    public function safeDown()
    {
        $table = Yii::app()->db->schema->getTable('event_type', true);
        if (!isset($table->columns['display_order'])) {
            $this->addOEColumn('event_type', 'display_order', 'int default 1', true);
        }
    }
}
