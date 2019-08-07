<?php

class m170512_101504_Update_Kera_Items extends CDbMigration
{
    public function up()
    {
        $this->update('ophtroperationnote_cxl_epithelial_removal_diameter', array('display_order' => 5), 'name = "6mm"');
        $this->update('ophtroperationnote_cxl_epithelial_removal_diameter', array('name' => '11mm'), 'name = "6mm"');

        $this->addColumn('et_ophtroperationnote_cxl', 'mitomycin_c', 'tinyint(1) unsigned not null default 0');
        $this->addColumn('et_ophtroperationnote_cxl_version', 'mitomycin_c', 'tinyint(1) unsigned not null default 0');


    }

    public function down()
    {
        $this->update('ophtroperationnote_cxl_epithelial_removal_diameter', array('name' => '6mm'), 'name = "11mm"');
        $this->update('ophtroperationnote_cxl_epithelial_removal_diameter', array('display_order' => 1), 'name = "6mm"');

        $this->dropColumn('et_ophtroperationnote_cxl', 'mitomycin_c');
        $this->dropColumn('et_ophtroperationnote_cxl_version', 'mitomycin_c');
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