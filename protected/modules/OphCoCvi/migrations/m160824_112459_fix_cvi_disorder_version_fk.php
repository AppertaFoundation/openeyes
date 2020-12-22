<?php

class m160824_112459_fix_cvi_disorder_version_fk extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('et_ophcocvi_clinicinfo_disorder_assignment_aid_fk', 'et_ophcocvi_clinicinfo_disorder_assignment_version');
    }

    public function down()
    {
        $this->addForeignKey('et_ophcocvi_clinicinfo_disorder_assignment_aid_fk', 'et_ophcocvi_clinicinfo_disorder_assignment_version', 'id', 'et_ophcocvi_clinicinfo_disorder_assignment', 'id');
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
