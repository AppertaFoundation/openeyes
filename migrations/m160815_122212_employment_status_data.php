<?php

class m160815_122212_employment_status_data extends CDbMigration
{

    public function up()
    {
        $this->insert('ophcocvi_clericinfo_employment_status', array('name' => 'Retired', 'display_order' => 1));
        $this->insert('ophcocvi_clericinfo_employment_status', array('name' => 'Employed', 'display_order' => 2));
        $this->insert('ophcocvi_clericinfo_employment_status', array('name' => 'Unemployed', 'display_order' => 3));
        $this->insert('ophcocvi_clericinfo_employment_status', array('name' => 'Child', 'display_order' => 4));
        $this->insert('ophcocvi_clericinfo_employment_status', array('name' => 'Student', 'display_order' => 5));
    }

    public function down()
    {
        $this->truncateTable('ophcocvi_clericinfo_employment_status');
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