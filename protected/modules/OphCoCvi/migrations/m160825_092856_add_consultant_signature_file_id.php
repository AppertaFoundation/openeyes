<?php

class m160825_092856_add_consultant_signature_file_id extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_clinicinfo', 'consultant_signature_file_id', 'int(10) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'consultant_signature_file_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophcocvi_clinicinfo_consultant_signature_file_id_fk', 'et_ophcocvi_clinicinfo', 'consultant_signature_file_id', 'protected_file', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocvi_clinicinfo_consultant_signature_file_id_fk', 'et_ophcocvi_clinicinfo');
        $this->dropColumn('et_ophcocvi_clinicinfo', 'consultant_signature_file_id');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'consultant_signature_file_id');
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
