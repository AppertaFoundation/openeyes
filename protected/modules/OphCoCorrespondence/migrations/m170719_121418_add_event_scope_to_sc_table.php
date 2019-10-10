<?php

class m170719_121418_add_event_scope_to_sc_table extends CDbMigration
{
    public function up()
    {
        $this->addColumn('patient_shortcode', 'global_scope', 'BOOLEAN NOT NULL DEFAULT 1');
    }

    public function down()
    {
        $this->dropColumn('patient_shortcode', 'global_scope');
    }
}