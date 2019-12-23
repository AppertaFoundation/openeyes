<?php

class m181212_150826_add_is_active_to_ophciexamination_element_set extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_element_set', 'is_active', 'tinyint(1) unsigned not null default 1');
        $this->addColumn('ophciexamination_element_set_version', 'is_active', 'tinyint(1) unsigned not null default 1 AFTER workflow_id');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_element_set', 'is_active');
        $this->dropColumn('ophciexamination_element_set_version', 'is_active');
    }
}