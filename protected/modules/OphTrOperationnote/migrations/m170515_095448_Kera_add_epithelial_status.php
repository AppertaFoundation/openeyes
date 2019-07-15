<?php

class m170515_095448_Kera_add_epithelial_status extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationnote_cxl', 'epithelial_status_id', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophtroperationnote_cxl_version', 'epithelial_status_id', 'tinyint(1) unsigned not null');

        $this->createOETable('ophtroperationnote_cxl_epithelial_status', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'active' => 'boolean NOT NULL DEFAULT true',
            'display_order' => 'integer NOT NULL',
        ), true);

        $this->insert('ophtroperationnote_cxl_epithelial_status',
            array('name' => 'On', 'display_order' => 1));
        $this->insert('ophtroperationnote_cxl_epithelial_status',
            array('name' => 'Off', 'display_order' => 2));
        $this->insert('ophtroperationnote_cxl_epithelial_status',
            array('name' => 'Partial Disruption', 'display_order' => 3));

    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationnote_cxl', 'epithelial_status_id');
        $this->dropColumn('et_ophtroperationnote_cxl_version', 'epithelial_status_id');
        $this->dropOETable('ophtroperationnote_cxl_epithelial_status', true);

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