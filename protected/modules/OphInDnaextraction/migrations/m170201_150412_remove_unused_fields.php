<?php

class m170201_150412_remove_unused_fields extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophindnaextraction_dnaextraction', 'storage_id', 'int(10) unsigned not null');
        $this->addColumn('et_ophindnaextraction_dnaextraction_version', 'storage_id', 'int(10) unsigned not null');

        $this->dropForeignKey('et_ophindnaextraction_dnaextraction_box_id_fk', 'et_ophindnaextraction_dnaextraction');
        $this->dropIndex('et_ophindnaextraction_dnaextraction_box_id_fk', 'et_ophindnaextraction_dnaextraction');

        $this->dropForeignKey('et_ophindnaextraction_dnaextraction_letter_id_fk', 'et_ophindnaextraction_dnaextraction');
        $this->dropIndex('et_ophindnaextraction_dnaextraction_letter_id_fk', 'et_ophindnaextraction_dnaextraction');

        $this->dropForeignKey('et_ophindnaextraction_dnaextraction_number_id_fk', 'et_ophindnaextraction_dnaextraction');
        $this->dropIndex('et_ophindnaextraction_dnaextraction_number_id_fk', 'et_ophindnaextraction_dnaextraction');

        $this->dropColumn('et_ophindnaextraction_dnaextraction', 'box_id');
        $this->dropColumn('et_ophindnaextraction_dnaextraction', 'letter_id');
        $this->dropColumn('et_ophindnaextraction_dnaextraction', 'number_id');

        $this->dropColumn('et_ophindnaextraction_dnaextraction_version', 'box_id');
        $this->dropColumn('et_ophindnaextraction_dnaextraction_version', 'letter_id');
        $this->dropColumn('et_ophindnaextraction_dnaextraction_version', 'number_id');
    }

    public function down()
    {
        $this->dropColumn('et_ophindnaextraction_dnaextraction', 'storage_id');
        $this->dropColumn('et_ophindnaextraction_dnaextraction_version', 'storage_id');

        $this->addColumn('et_ophindnaextraction_dnaextraction', 'box_id', 'int(10) unsigned not null');
        $this->addColumn('et_ophindnaextraction_dnaextraction', 'letter_id', 'int(10) unsigned not null');
        $this->addColumn('et_ophindnaextraction_dnaextraction', 'number_id', 'int(10) unsigned not null');

        $this->addColumn('et_ophindnaextraction_dnaextraction_version', 'box_id', 'int(10) unsigned not null');
        $this->addColumn('et_ophindnaextraction_dnaextraction_version', 'letter_id', 'int(10) unsigned not null');
        $this->addColumn('et_ophindnaextraction_dnaextraction_version', 'number_id', 'int(10) unsigned not null');
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
