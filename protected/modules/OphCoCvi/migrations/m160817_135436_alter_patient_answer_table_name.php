<?php

class m160817_135436_alter_patient_answer_table_name extends CDbMigration
{
    public function up()
    {
        $this->renameTable("et_ophcocvi_clericinfo_patient_factor_answer", "ophcocvi_clericinfo_patient_factor_answer");
        $this->renameTable("et_ophcocvi_clericinfo_patient_factor_answer_version", "ophcocvi_clericinfo_patient_factor_answer_version");
    }

    public function down()
    {
        $this->dropTable('ophcocvi_clericinfo_patient_factor_answer');
        $this->dropTable('ophcocvi_clericinfo_patient_factor_answer_version');
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
