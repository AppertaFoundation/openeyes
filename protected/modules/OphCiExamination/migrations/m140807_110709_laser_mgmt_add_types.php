<?php

class m140807_110709_laser_mgmt_add_types extends OEMigration
{
    public function up()
    {
        // adding 7 new entries
        $this->dbConnection->createCommand('UPDATE ophciexamination_lasermanagement_lasertype set display_order = display_order+7')->execute();

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);
    }

    public function down()
    {
        $this->delete('ophciexamination_lasermanagement_lasertype', 'display_order <= 7');
        $this->dbConnection->createCommand('UPDATE ophciexamination_lasermanagement_lasertype set display_order = display_order-7')->execute();
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
