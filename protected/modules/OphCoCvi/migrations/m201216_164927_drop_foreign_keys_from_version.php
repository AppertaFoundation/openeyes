<?php

class m201216_164927_drop_foreign_keys_from_version extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_lmui_fk', 'ophcocvi_clericinfo_patient_factor_answer_version');
        $this->dropForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_cui_fk', 'ophcocvi_clericinfo_patient_factor_answer_version');
        $this->dropForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_ele_fk', 'ophcocvi_clericinfo_patient_factor_answer_version');
    }

    public function down()
    {
        $this->addForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_lmui_fk', 'ophcocvi_clericinfo_patient_factor_answer_version', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_cui_fk', 'ophcocvi_clericinfo_patient_factor_answer_version', 'created_user_id', 'user', 'id');
        $this->addForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_ele_fk', 'ophcocvi_clericinfo_patient_factor_answer_version', 'element_id', 'et_ophcocvi_clericinfo', 'id');
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
