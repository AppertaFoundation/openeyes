<?php

class m160202_131808_add_value_remap extends OEMigration
{
    public function up()
    {
        $this->createOETable('pasapi_xpath_remap', array(
            'id' => 'pk',
            'name' => 'varchar(127) NOT NULL',
            'xpath' => 'varchar(511) NOT NULL',
        ), true);

        $this->createOETable('pasapi_remap_value', array(
            'id' => 'pk',
            'xpath_id' => 'int(11) NOT NULL',
            'input' => 'varchar(31) NOT NULL',
            'output' => 'varchar(31) default NULL',
        ), true);

        $this->addForeignKey(
            'pasapi_remap_value_xpath_id_fk',
            'pasapi_remap_value',
            'xpath_id',
            'pasapi_xpath_remap',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropOETable('pasapi_remap_value', true);
        $this->dropOETable('pasapi_xpath_remap', true);
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
