<?php

class m190220_115556_fix_meds_mgment_element_group extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE element_type SET element_group_id = (SELECT id FROM element_group WHERE `name`='Clinical Management'),
							display_order = 440
							WHERE `name` = 'Medication Management'");
    }

    public function down()
    {
        $this->execute("UPDATE element_type SET element_group_id = NULL, display_order = 0
							WHERE `name` = 'Medication Management'");
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
