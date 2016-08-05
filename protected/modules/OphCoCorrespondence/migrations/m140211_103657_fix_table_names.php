<?php

class m140211_103657_fix_table_names extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('et_ophcocorrespondence_flm_firm_id_fk', 'et_ophcocorrespondence_firm_letter_macro');
        $this->dropForeignKey('et_ophcocorrespondence_flm_last_modified_user_id_fk', 'et_ophcocorrespondence_firm_letter_macro');
        $this->dropForeignKey('et_ophcocorrespondence_flm_created_user_id_fk', 'et_ophcocorrespondence_firm_letter_macro');

        $this->dropIndex('et_ophcocorrespondence_flm_firm_id_fk', 'et_ophcocorrespondence_firm_letter_macro');
        $this->dropIndex('et_ophcocorrespondence_flm_last_modified_user_id_fk', 'et_ophcocorrespondence_firm_letter_macro');
        $this->dropIndex('et_ophcocorrespondence_flm_created_user_id_fk', 'et_ophcocorrespondence_firm_letter_macro');

        $this->renameTable('et_ophcocorrespondence_firm_letter_macro', 'ophcocorrespondence_firm_letter_macro');

        $this->addForeignKey('ophcocorrespondence_flm_created_user_id_fk', 'ophcocorrespondence_firm_letter_macro', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_flm_last_modified_user_id_fk', 'ophcocorrespondence_firm_letter_macro', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_flm_firm_id_fk', 'ophcocorrespondence_firm_letter_macro', 'firm_id', 'firm', 'id');

        //

        $this->dropForeignKey('et_ophcocorrespondence_fls_letter_string_group_id_fk', 'et_ophcocorrespondence_firm_letter_string');
        $this->dropForeignKey('et_ophcocorrespondence_fls_firm_id_fk', 'et_ophcocorrespondence_firm_letter_string');
        $this->dropForeignKey('et_ophcocorrespondence_fls_last_modified_user_id_fk', 'et_ophcocorrespondence_firm_letter_string');
        $this->dropForeignKey('et_ophcocorrespondence_fls_created_user_id_fk', 'et_ophcocorrespondence_firm_letter_string');

        $this->dropIndex('et_ophcocorrespondence_fls_letter_string_group_id_fk', 'et_ophcocorrespondence_firm_letter_string');
        $this->dropIndex('et_ophcocorrespondence_fls_firm_id_fk', 'et_ophcocorrespondence_firm_letter_string');
        $this->dropIndex('et_ophcocorrespondence_fls_last_modified_user_id_fk', 'et_ophcocorrespondence_firm_letter_string');
        $this->dropIndex('et_ophcocorrespondence_fls_created_user_id_fk', 'et_ophcocorrespondence_firm_letter_string');

        $this->renameTable('et_ophcocorrespondence_firm_letter_string', 'ophcocorrespondence_firm_letter_string');

        $this->addForeignKey('ophcocorrespondence_fls_created_user_id_fk', 'ophcocorrespondence_firm_letter_string', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_fls_last_modified_user_id_fk', 'ophcocorrespondence_firm_letter_string', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_fls_firm_id_fk', 'ophcocorrespondence_firm_letter_string', 'firm_id', 'firm', 'id');
        $this->addForeignKey('ophcocorrespondence_fls_letter_string_group_id_fk', 'ophcocorrespondence_firm_letter_string', 'letter_string_group_id', 'et_ophcocorrespondence_letter_string_group', 'id');

        //

        $this->dropForeignKey('et_ophcocorrespondence_fss_firm_id_fk', 'et_ophcocorrespondence_firm_site_secretary');
        $this->dropForeignKey('et_ophcocorrespondence_fss_site_id_fk', 'et_ophcocorrespondence_firm_site_secretary');
        $this->dropForeignKey('et_ophcocorrespondence_fss_last_modified_user_id_fk', 'et_ophcocorrespondence_firm_site_secretary');
        $this->dropForeignKey('et_ophcocorrespondence_fss_created_user_id_fk', 'et_ophcocorrespondence_firm_site_secretary');

        $this->dropIndex('et_ophcocorrespondence_fss_firm_id_fk', 'et_ophcocorrespondence_firm_site_secretary');
        $this->dropIndex('et_ophcocorrespondence_fss_site_id_fk', 'et_ophcocorrespondence_firm_site_secretary');
        $this->dropIndex('et_ophcocorrespondence_fss_last_modified_user_id_fk', 'et_ophcocorrespondence_firm_site_secretary');
        $this->dropIndex('et_ophcocorrespondence_fss_created_user_id_fk', 'et_ophcocorrespondence_firm_site_secretary');

        $this->renameTable('et_ophcocorrespondence_firm_site_secretary', 'ophcocorrespondence_firm_site_secretary');

        $this->addForeignKey('ophcocorrespondence_fss_created_user_id_fk', 'ophcocorrespondence_firm_site_secretary', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_fss_last_modified_user_id_fk', 'ophcocorrespondence_firm_site_secretary', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_fss_site_id_fk', 'ophcocorrespondence_firm_site_secretary', 'site_id', 'site', 'id');
        $this->addForeignKey('ophcocorrespondence_fss_firm_id_fk', 'ophcocorrespondence_firm_site_secretary', 'firm_id', 'firm', 'id');

        //

        $this->dropForeignKey('et_ophcocorrespondence_lm_last_modified_user_id_fk', 'et_ophcocorrespondence_letter_macro');
        $this->dropForeignKey('et_ophcocorrespondence_lm_created_user_id_fk', 'et_ophcocorrespondence_letter_macro');
        $this->dropForeignKey('et_ophcocorrespondence_lm_site_id_fk', 'et_ophcocorrespondence_letter_macro');

        $this->dropIndex('et_ophcocorrespondence_lm_last_modified_user_id_fk', 'et_ophcocorrespondence_letter_macro');
        $this->dropIndex('et_ophcocorrespondence_lm_created_user_id_fk', 'et_ophcocorrespondence_letter_macro');
        $this->dropIndex('et_ophcocorrespondence_lm_site_id_fk', 'et_ophcocorrespondence_letter_macro');

        $this->renameTable('et_ophcocorrespondence_letter_macro', 'ophcocorrespondence_letter_macro');

        $this->addForeignKey('ophcocorrespondence_lm_site_id_fk', 'ophcocorrespondence_letter_macro', 'site_id', 'site', 'id');
        $this->addForeignKey('ophcocorrespondence_lm_created_user_id_fk', 'ophcocorrespondence_letter_macro', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_lm_last_modified_user_id_fk', 'ophcocorrespondence_letter_macro', 'last_modified_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophcocorrespondence_ls2_created_user_id_fk', 'et_ophcocorrespondence_letter_string');
        $this->dropForeignKey('et_ophcocorrespondence_ls2_last_modified_user_id_fk', 'et_ophcocorrespondence_letter_string');
        $this->dropForeignKey('et_ophcocorrespondence_ls2_letter_string_group_id_fk', 'et_ophcocorrespondence_letter_string');
        $this->dropForeignKey('et_ophcocorrespondence_ls2_created_site_id_fk', 'et_ophcocorrespondence_letter_string');

        $this->dropIndex('et_ophcocorrespondence_ls2_created_user_id_fk', 'et_ophcocorrespondence_letter_string');
        $this->dropIndex('et_ophcocorrespondence_ls2_last_modified_user_id_fk', 'et_ophcocorrespondence_letter_string');
        $this->dropIndex('et_ophcocorrespondence_ls2_letter_string_group_id_fk', 'et_ophcocorrespondence_letter_string');
        $this->dropIndex('et_ophcocorrespondence_ls2_created_site_id_fk', 'et_ophcocorrespondence_letter_string');

        $this->renameTable('et_ophcocorrespondence_letter_string', 'ophcocorrespondence_letter_string');

        $this->addForeignKey('ophcocorrespondence_ls2_site_id_fk', 'ophcocorrespondence_letter_string', 'site_id', 'site', 'id');
        $this->addForeignKey('ophcocorrespondence_ls2_letter_string_group_id_fk', 'ophcocorrespondence_letter_string', 'letter_string_group_id', 'et_ophcocorrespondence_letter_string_group', 'id');
        $this->addForeignKey('ophcocorrespondence_ls2_last_modified_user_id_fk', 'ophcocorrespondence_letter_string', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_ls2_created_user_id_fk', 'ophcocorrespondence_letter_string', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophcocorrespondence_lsg_last_modified_user_id_fk', 'et_ophcocorrespondence_letter_string_group');
        $this->dropForeignKey('et_ophcocorrespondence_lsg_created_user_id_fk', 'et_ophcocorrespondence_letter_string_group');

        $this->dropIndex('et_ophcocorrespondence_lsg_last_modified_user_id_fk', 'et_ophcocorrespondence_letter_string_group');
        $this->dropIndex('et_ophcocorrespondence_lsg_created_user_id_fk', 'et_ophcocorrespondence_letter_string_group');

        $this->renameTable('et_ophcocorrespondence_letter_string_group', 'ophcocorrespondence_letter_string_group');

        $this->addForeignKey('ophcocorrespondence_lsg_created_user_id_fk', 'ophcocorrespondence_letter_string_group', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_lsg_last_modified_user_id_fk', 'ophcocorrespondence_letter_string_group', 'last_modified_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophcocorrespondence_slm2_subspecialty_id_fk', 'et_ophcocorrespondence_subspecialty_letter_macro');
        $this->dropForeignKey('et_ophcocorrespondence_slm2_last_modified_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_macro');
        $this->dropForeignKey('et_ophcocorrespondence_slm2_created_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_macro');

        $this->dropIndex('et_ophcocorrespondence_slm2_subspecialty_id_fk', 'et_ophcocorrespondence_subspecialty_letter_macro');
        $this->dropIndex('et_ophcocorrespondence_slm2_last_modified_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_macro');
        $this->dropIndex('et_ophcocorrespondence_slm2_created_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_macro');

        $this->renameTable('et_ophcocorrespondence_subspecialty_letter_macro', 'ophcocorrespondence_subspecialty_letter_macro');

        $this->addForeignKey('ophcocorrespondence_slm2_created_user_id_fk', 'ophcocorrespondence_subspecialty_letter_macro', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_slm2_last_modified_user_id_fk', 'ophcocorrespondence_subspecialty_letter_macro', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_slm2_subspecialty_id_fk', 'ophcocorrespondence_subspecialty_letter_macro', 'subspecialty_id', 'subspecialty', 'id');

        //

        $this->dropForeignKey('et_ophcocorrespondence_sls_created_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string');
        $this->dropForeignKey('et_ophcocorrespondence_sls_last_modified_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string');
        $this->dropForeignKey('et_ophcocorrespondence_sls_letter_string_group_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string');
        $this->dropForeignKey('et_ophcocorrespondence_sls_subspecialty_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string');

        $this->dropIndex('et_ophcocorrespondence_sls_created_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string');
        $this->dropIndex('et_ophcocorrespondence_sls_last_modified_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string');
        $this->dropIndex('et_ophcocorrespondence_sls_letter_string_group_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string');
        $this->dropIndex('et_ophcocorrespondence_sls_subspecialty_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string');

        $this->renameTable('et_ophcocorrespondence_subspecialty_letter_string', 'ophcocorrespondence_subspecialty_letter_string');

        $this->addForeignKey('ophcocorrespondence_sls_subspecialty_id_fk', 'ophcocorrespondence_subspecialty_letter_string', 'subspecialty_id', 'subspecialty', 'id');
        $this->addForeignKey('ophcocorrespondence_sls_last_modified_user_id_fk', 'ophcocorrespondence_subspecialty_letter_string', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_sls_letter_string_group_id_fk', 'ophcocorrespondence_subspecialty_letter_string', 'letter_string_group_id', 'ophcocorrespondence_letter_string_group', 'id');
        $this->addForeignKey('ophcocorrespondence_sls_created_user_id_fk', 'ophcocorrespondence_subspecialty_letter_string', 'created_user_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('ophcocorrespondence_sls_created_user_id_fk', 'ophcocorrespondence_subspecialty_letter_string');
        $this->dropForeignKey('ophcocorrespondence_sls_last_modified_user_id_fk', 'ophcocorrespondence_subspecialty_letter_string');
        $this->dropForeignKey('ophcocorrespondence_sls_letter_string_group_id_fk', 'ophcocorrespondence_subspecialty_letter_string');
        $this->dropForeignKey('ophcocorrespondence_sls_subspecialty_id_fk', 'ophcocorrespondence_subspecialty_letter_string');

        $this->dropIndex('ophcocorrespondence_sls_created_user_id_fk', 'ophcocorrespondence_subspecialty_letter_string');
        $this->dropIndex('ophcocorrespondence_sls_last_modified_user_id_fk', 'ophcocorrespondence_subspecialty_letter_string');
        $this->dropIndex('ophcocorrespondence_sls_letter_string_group_id_fk', 'ophcocorrespondence_subspecialty_letter_string');
        $this->dropIndex('ophcocorrespondence_sls_subspecialty_id_fk', 'ophcocorrespondence_subspecialty_letter_string');

        $this->renameTable('ophcocorrespondence_subspecialty_letter_string', 'et_ophcocorrespondence_subspecialty_letter_string');

        $this->addForeignKey('et_ophcocorrespondence_sls_subspecialty_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string', 'subspecialty_id', 'subspecialty', 'id');
        $this->addForeignKey('et_ophcocorrespondence_sls_last_modified_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_sls_letter_string_group_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string', 'letter_string_group_id', 'ophcocorrespondence_letter_string_group', 'id');
        $this->addForeignKey('et_ophcocorrespondence_sls_created_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_string', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophcocorrespondence_slm2_subspecialty_id_fk', 'ophcocorrespondence_subspecialty_letter_macro');
        $this->dropForeignKey('ophcocorrespondence_slm2_last_modified_user_id_fk', 'ophcocorrespondence_subspecialty_letter_macro');
        $this->dropForeignKey('ophcocorrespondence_slm2_created_user_id_fk', 'ophcocorrespondence_subspecialty_letter_macro');

        $this->dropIndex('ophcocorrespondence_slm2_subspecialty_id_fk', 'ophcocorrespondence_subspecialty_letter_macro');
        $this->dropIndex('ophcocorrespondence_slm2_last_modified_user_id_fk', 'ophcocorrespondence_subspecialty_letter_macro');
        $this->dropIndex('ophcocorrespondence_slm2_created_user_id_fk', 'ophcocorrespondence_subspecialty_letter_macro');

        $this->renameTable('ophcocorrespondence_subspecialty_letter_macro', 'et_ophcocorrespondence_subspecialty_letter_macro');

        $this->addForeignKey('et_ophcocorrespondence_slm2_created_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_macro', 'created_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_slm2_last_modified_user_id_fk', 'et_ophcocorrespondence_subspecialty_letter_macro', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_slm2_subspecialty_id_fk', 'et_ophcocorrespondence_subspecialty_letter_macro', 'subspecialty_id', 'subspecialty', 'id');

        //

        $this->dropForeignKey('ophcocorrespondence_lsg_last_modified_user_id_fk', 'ophcocorrespondence_letter_string_group');
        $this->dropForeignKey('ophcocorrespondence_lsg_created_user_id_fk', 'ophcocorrespondence_letter_string_group');

        $this->dropIndex('ophcocorrespondence_lsg_last_modified_user_id_fk', 'ophcocorrespondence_letter_string_group');
        $this->dropIndex('ophcocorrespondence_lsg_created_user_id_fk', 'ophcocorrespondence_letter_string_group');

        $this->renameTable('ophcocorrespondence_letter_string_group', 'et_ophcocorrespondence_letter_string_group');

        $this->addForeignKey('et_ophcocorrespondence_lsg_created_user_id_fk', 'et_ophcocorrespondence_letter_string_group', 'created_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_lsg_last_modified_user_id_fk', 'et_ophcocorrespondence_letter_string_group', 'last_modified_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophcocorrespondence_ls2_created_user_id_fk', 'ophcocorrespondence_letter_string');
        $this->dropForeignKey('ophcocorrespondence_ls2_last_modified_user_id_fk', 'ophcocorrespondence_letter_string');
        $this->dropForeignKey('ophcocorrespondence_ls2_letter_string_group_id_fk', 'ophcocorrespondence_letter_string');
        $this->dropForeignKey('ophcocorrespondence_ls2_site_id_fk', 'ophcocorrespondence_letter_string');

        $this->dropIndex('ophcocorrespondence_ls2_created_user_id_fk', 'ophcocorrespondence_letter_string');
        $this->dropIndex('ophcocorrespondence_ls2_last_modified_user_id_fk', 'ophcocorrespondence_letter_string');
        $this->dropIndex('ophcocorrespondence_ls2_letter_string_group_id_fk', 'ophcocorrespondence_letter_string');
        $this->dropIndex('ophcocorrespondence_ls2_site_id_fk', 'ophcocorrespondence_letter_string');

        $this->renameTable('ophcocorrespondence_letter_string', 'et_ophcocorrespondence_letter_string');

        $this->addForeignKey('et_ophcocorrespondence_ls2_created_site_id_fk', 'et_ophcocorrespondence_letter_string', 'site_id', 'site', 'id');
        $this->addForeignKey('et_ophcocorrespondence_ls2_letter_string_group_id_fk', 'et_ophcocorrespondence_letter_string', 'letter_string_group_id', 'et_ophcocorrespondence_letter_string_group', 'id');
        $this->addForeignKey('et_ophcocorrespondence_ls2_last_modified_user_id_fk', 'et_ophcocorrespondence_letter_string', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_ls2_created_user_id_fk', 'et_ophcocorrespondence_letter_string', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophcocorrespondence_lm_last_modified_user_id_fk', 'ophcocorrespondence_letter_macro');
        $this->dropForeignKey('ophcocorrespondence_lm_created_user_id_fk', 'ophcocorrespondence_letter_macro');
        $this->dropForeignKey('ophcocorrespondence_lm_site_id_fk', 'ophcocorrespondence_letter_macro');

        $this->dropIndex('ophcocorrespondence_lm_last_modified_user_id_fk', 'ophcocorrespondence_letter_macro');
        $this->dropIndex('ophcocorrespondence_lm_created_user_id_fk', 'ophcocorrespondence_letter_macro');
        $this->dropIndex('ophcocorrespondence_lm_site_id_fk', 'ophcocorrespondence_letter_macro');

        $this->renameTable('ophcocorrespondence_letter_macro', 'et_ophcocorrespondence_letter_macro');

        $this->addForeignKey('et_ophcocorrespondence_lm_site_id_fk', 'et_ophcocorrespondence_letter_macro', 'site_id', 'site', 'id');
        $this->addForeignKey('et_ophcocorrespondence_lm_created_user_id_fk', 'et_ophcocorrespondence_letter_macro', 'created_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_lm_last_modified_user_id_fk', 'et_ophcocorrespondence_letter_macro', 'last_modified_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophcocorrespondence_fss_firm_id_fk', 'ophcocorrespondence_firm_site_secretary');
        $this->dropForeignKey('ophcocorrespondence_fss_site_id_fk', 'ophcocorrespondence_firm_site_secretary');
        $this->dropForeignKey('ophcocorrespondence_fss_last_modified_user_id_fk', 'ophcocorrespondence_firm_site_secretary');
        $this->dropForeignKey('ophcocorrespondence_fss_created_user_id_fk', 'ophcocorrespondence_firm_site_secretary');

        $this->dropIndex('ophcocorrespondence_fss_firm_id_fk', 'ophcocorrespondence_firm_site_secretary');
        $this->dropIndex('ophcocorrespondence_fss_site_id_fk', 'ophcocorrespondence_firm_site_secretary');
        $this->dropIndex('ophcocorrespondence_fss_last_modified_user_id_fk', 'ophcocorrespondence_firm_site_secretary');
        $this->dropIndex('ophcocorrespondence_fss_created_user_id_fk', 'ophcocorrespondence_firm_site_secretary');

        $this->renameTable('ophcocorrespondence_firm_site_secretary', 'et_ophcocorrespondence_firm_site_secretary');

        $this->addForeignKey('et_ophcocorrespondence_fss_created_user_id_fk', 'et_ophcocorrespondence_firm_site_secretary', 'created_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_fss_last_modified_user_id_fk', 'et_ophcocorrespondence_firm_site_secretary', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_fss_site_id_fk', 'et_ophcocorrespondence_firm_site_secretary', 'site_id', 'site', 'id');
        $this->addForeignKey('et_ophcocorrespondence_fss_firm_id_fk', 'et_ophcocorrespondence_firm_site_secretary', 'firm_id', 'firm', 'id');

        //

        $this->dropForeignKey('ophcocorrespondence_fls_letter_string_group_id_fk', 'ophcocorrespondence_firm_letter_string');
        $this->dropForeignKey('ophcocorrespondence_fls_firm_id_fk', 'ophcocorrespondence_firm_letter_string');
        $this->dropForeignKey('ophcocorrespondence_fls_last_modified_user_id_fk', 'ophcocorrespondence_firm_letter_string');
        $this->dropForeignKey('ophcocorrespondence_fls_created_user_id_fk', 'ophcocorrespondence_firm_letter_string');

        $this->dropIndex('ophcocorrespondence_fls_letter_string_group_id_fk', 'ophcocorrespondence_firm_letter_string');
        $this->dropIndex('ophcocorrespondence_fls_firm_id_fk', 'ophcocorrespondence_firm_letter_string');
        $this->dropIndex('ophcocorrespondence_fls_last_modified_user_id_fk', 'ophcocorrespondence_firm_letter_string');
        $this->dropIndex('ophcocorrespondence_fls_created_user_id_fk', 'ophcocorrespondence_firm_letter_string');

        $this->renameTable('ophcocorrespondence_firm_letter_string', 'et_ophcocorrespondence_firm_letter_string');

        $this->addForeignKey('et_ophcocorrespondence_fls_created_user_id_fk', 'et_ophcocorrespondence_firm_letter_string', 'created_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_fls_last_modified_user_id_fk', 'et_ophcocorrespondence_firm_letter_string', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_fls_firm_id_fk', 'et_ophcocorrespondence_firm_letter_string', 'firm_id', 'firm', 'id');
        $this->addForeignKey('et_ophcocorrespondence_fls_letter_string_group_id_fk', 'et_ophcocorrespondence_firm_letter_string', 'letter_string_group_id', 'et_ophcocorrespondence_letter_string_group', 'id');

        //

        $this->dropForeignKey('ophcocorrespondence_flm_firm_id_fk', 'ophcocorrespondence_firm_letter_macro');
        $this->dropForeignKey('ophcocorrespondence_flm_last_modified_user_id_fk', 'ophcocorrespondence_firm_letter_macro');
        $this->dropForeignKey('ophcocorrespondence_flm_created_user_id_fk', 'ophcocorrespondence_firm_letter_macro');

        $this->dropIndex('ophcocorrespondence_flm_firm_id_fk', 'ophcocorrespondence_firm_letter_macro');
        $this->dropIndex('ophcocorrespondence_flm_last_modified_user_id_fk', 'ophcocorrespondence_firm_letter_macro');
        $this->dropIndex('ophcocorrespondence_flm_created_user_id_fk', 'ophcocorrespondence_firm_letter_macro');

        $this->renameTable('ophcocorrespondence_firm_letter_macro', 'et_ophcocorrespondence_firm_letter_macro');

        $this->addForeignKey('et_ophcocorrespondence_flm_created_user_id_fk', 'et_ophcocorrespondence_firm_letter_macro', 'created_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_flm_last_modified_user_id_fk', 'et_ophcocorrespondence_firm_letter_macro', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcocorrespondence_flm_firm_id_fk', 'et_ophcocorrespondence_firm_letter_macro', 'firm_id', 'firm', 'id');
    }
}
