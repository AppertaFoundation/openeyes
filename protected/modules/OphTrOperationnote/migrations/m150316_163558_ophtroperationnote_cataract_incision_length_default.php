<?php

class m150316_163558_ophtroperationnote_cataract_incision_length_default extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophtroperationnote_cataract_incision_length_default', array(
            'id' => 'pk',
            'firm_id' => 'int(10) unsigned NOT NULL',
            'value' => 'float',
            'KEY `ophtroperationnote_default_incision_firm_fk` (`firm_id`)',
            'CONSTRAINT `ophtroperationnote_default_incision_firm_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
            'CONSTRAINT ophtroperationnote_cataract_incision_length_default_firm_u UNIQUE (firm_id)',
        ), true);
    }

    public function down()
    {
        $this->dropOETable('ophtroperationnote_cataract_incision_length_default', true);
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
