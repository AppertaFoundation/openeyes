<?php

class m170512_122616_Kera_mitomycin_creation extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophtroperationnote_cxl_mitomycin', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'active' => 'boolean NOT NULL DEFAULT true',
            'display_order' => 'integer NOT NULL',
        ), true);

        $this->insert('ophtroperationnote_cxl_mitomycin',
            array('name' => 'Yes', 'display_order' => 1));
        $this->insert('ophtroperationnote_cxl_mitomycin',
            array('name' => 'No', 'display_order' => 2));

    }

    public function down()
    {
        $this->dropOETable('ophtroperationnote_cxl_mitomycin', true);

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