<?php

class m131014_072253_add_site extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophcotherapya_mrservicein', 'site_id', 'int(10) unsigned');
        $this->addForeignKey(
            'et_ophcotherapya_mrservicein_site_id_fk',
            'et_ophcotherapya_mrservicein',
            'site_id',
            'site',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcotherapya_mrservicein_site_id_fk', 'et_ophcotherapya_mrservicein');
        $this->dropColumn('et_ophcotherapya_mrservicein', 'site_id');
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
