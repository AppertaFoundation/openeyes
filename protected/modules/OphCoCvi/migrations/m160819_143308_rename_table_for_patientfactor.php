<?php

class m160819_143308_rename_table_for_patientfactor extends CDbMigration
{
    public function up()
    {
        $this->renameTable('ophcocvi_clinicinfo_patient_factor', 'ophcocvi_clericinfo_patient_factor');
        $this->renameTable('ophcocvi_clinicinfo_patient_factor_version', 'ophcocvi_clericinfo_patient_factor_version');
        $this->dropForeignKey('et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer');
        $this->dropForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer_version');

        $this->renameColumn('ophcocvi_clericinfo_patient_factor_answer', 'ophcocvi_clinicinfo_patient_factor_id', 'patient_factor_id');
        $this->renameColumn('ophcocvi_clericinfo_patient_factor_answer_version', 'ophcocvi_clinicinfo_patient_factor_id', 'patient_factor_id');

        $this->addForeignKey('et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer', 'patient_factor_id', 'ophcocvi_clericinfo_patient_factor', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer');
        $this->renameColumn('ophcocvi_clericinfo_patient_factor_answer', 'patient_factor_id', 'ophcocvi_clinicinfo_patient_factor_id');
        $this->renameColumn('ophcocvi_clericinfo_patient_factor_answer_version', 'patient_factor_id', 'ophcocvi_clinicinfo_patient_factor_id');

        $this->renameTable('ophcocvi_clericinfo_patient_factor', 'ophcocvi_clinicinfo_patient_factor');
        $this->renameTable('ophcocvi_clericinfo_patient_factor_version', 'ophcocvi_clinicinfo_patient_factor_version');

        $this->addForeignKey('et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer', 'ophcocvi_clinicinfo_patient_factor_id', 'ophcocvi_clinicinfo_patient_factor', 'id');
        $this->addForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer_version', 'ophcocvi_clinicinfo_patient_factor_id', 'ophcocvi_clinicinfo_patient_factor', 'id');
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
