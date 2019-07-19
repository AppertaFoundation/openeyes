<?php

class m170904_121349_update_risks_status_column extends CDbMigration
{
    public function up()
    {
        $this->update('ophciexamination_history_risks_entry', array('has_risk' => -9), "has_risk IS NULL");
        $this->alterColumn('ophciexamination_history_risks_entry', 'has_risk', 'tinyint(1) NOT NULL DEFAULT -9');
    }

    public function down()
    {
        $this->alterColumn('ophciexamination_history_risks_entry', 'has_risk', 'tinyint(1) DEFAULT NULL');
        $this->update('ophciexamination_history_risks_entry', array('has_risk' => NULL), "has_risk = -9");
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
