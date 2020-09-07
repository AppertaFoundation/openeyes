<?php

class m190401_115247_rename_history_medications_element extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE element_type SET `name`='Medication History' WHERE `name` = 'Medications';");
    }

    public function down()
    {
        $this->execute("UPDATE element_type SET `name`='Medications' WHERE `name` = 'Medication History';");
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
