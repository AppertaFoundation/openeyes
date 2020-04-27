<?php

class m180405_130244_add_phaco_cde_field extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationnote_cataract', 'phaco_cde', 'decimal(5,2)');
        $this->addColumn('et_ophtroperationnote_cataract_version', 'phaco_cde', 'decimal(5,2)');
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationnote_cataract', 'phaco_cde');
        $this->dropColumn('et_ophtroperationnote_cataract_version', 'phaco_cde');
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
