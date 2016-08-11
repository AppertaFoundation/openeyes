<?php

class m140212_091019_fix_table_names extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('et_ophtrconsent_permissions_images_cui_fk', 'et_ophtrconsent_permissions_images');
        $this->dropForeignKey('et_ophtrconsent_permissions_images_lmui_fk', 'et_ophtrconsent_permissions_images');

        $this->dropIndex('et_ophtrconsent_permissions_images_lmui_fk', 'et_ophtrconsent_permissions_images');
        $this->dropIndex('et_ophtrconsent_permissions_images_cui_fk', 'et_ophtrconsent_permissions_images');

        $this->renameTable('et_ophtrconsent_permissions_images', 'ophtrconsent_permissions_images');

        $this->addForeignKey('ophtrconsent_permissions_images_cui_fk', 'ophtrconsent_permissions_images', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophtrconsent_permissions_images_lmui_fk', 'ophtrconsent_permissions_images', 'last_modified_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophtrconsent_procedure_add_procs_add_procs_cui_fk', 'et_ophtrconsent_procedure_add_procs_add_procs');
        $this->dropForeignKey('et_ophtrconsent_procedure_add_procs_add_procs_ele_fk', 'et_ophtrconsent_procedure_add_procs_add_procs');
        $this->dropForeignKey('et_ophtrconsent_procedure_add_procs_add_procs_lku_fk', 'et_ophtrconsent_procedure_add_procs_add_procs');
        $this->dropForeignKey('et_ophtrconsent_procedure_add_procs_add_procs_lmui_fk', 'et_ophtrconsent_procedure_add_procs_add_procs');

        $this->dropIndex('et_ophtrconsent_procedure_add_procs_add_procs_cui_fk', 'et_ophtrconsent_procedure_add_procs_add_procs');
        $this->dropIndex('et_ophtrconsent_procedure_add_procs_add_procs_ele_fk', 'et_ophtrconsent_procedure_add_procs_add_procs');
        $this->dropIndex('et_ophtrconsent_procedure_add_procs_add_procs_lku_fk', 'et_ophtrconsent_procedure_add_procs_add_procs');
        $this->dropIndex('et_ophtrconsent_procedure_add_procs_add_procs_lmui_fk', 'et_ophtrconsent_procedure_add_procs_add_procs');

        $this->renameTable('et_ophtrconsent_procedure_add_procs_add_procs', 'ophtrconsent_procedure_add_procs_add_procs');

        $this->addForeignKey('ophtrconsent_procedure_add_procs_add_procs_lmui_fk', 'ophtrconsent_procedure_add_procs_add_procs', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophtrconsent_procedure_add_procs_add_procs_lku_fk', 'ophtrconsent_procedure_add_procs_add_procs', 'proc_id', 'proc', 'id');
        $this->addForeignKey('ophtrconsent_procedure_add_procs_add_procs_ele_fk', 'ophtrconsent_procedure_add_procs_add_procs', 'element_id', 'et_ophtrconsent_procedure', 'id');
        $this->addForeignKey('ophtrconsent_procedure_add_procs_add_procs_cui_fk', 'ophtrconsent_procedure_add_procs_add_procs', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophtrconsent_procedure_proc_defaults_cui_fk', 'et_ophtrconsent_procedure_proc_defaults');
        $this->dropForeignKey('et_ophtrconsent_procedure_proc_defaults_lmui_fk', 'et_ophtrconsent_procedure_proc_defaults');

        $this->dropIndex('et_ophtrconsent_procedure_proc_defaults_lmui_fk', 'et_ophtrconsent_procedure_proc_defaults');
        $this->dropIndex('et_ophtrconsent_procedure_proc_defaults_cui_fk', 'et_ophtrconsent_procedure_proc_defaults');

        $this->renameTable('et_ophtrconsent_procedure_proc_defaults', 'ophtrconsent_procedure_proc_defaults');

        $this->addForeignKey('ophtrconsent_procedure_proc_defaults_lmui_fk', 'ophtrconsent_procedure_proc_defaults', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophtrconsent_procedure_proc_defaults_cui_fk', 'ophtrconsent_procedure_proc_defaults', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophtrconsent_procedure_procedures_procedures_cui_fk', 'et_ophtrconsent_procedure_procedures_procedures');
        $this->dropForeignKey('et_ophtrconsent_procedure_procedures_procedures_ele_fk', 'et_ophtrconsent_procedure_procedures_procedures');
        $this->dropForeignKey('et_ophtrconsent_procedure_procedures_procedures_lku_fk', 'et_ophtrconsent_procedure_procedures_procedures');
        $this->dropForeignKey('et_ophtrconsent_procedure_procedures_procedures_lmui_fk', 'et_ophtrconsent_procedure_procedures_procedures');

        $this->dropIndex('et_ophtrconsent_procedure_procedures_procedures_cui_fk', 'et_ophtrconsent_procedure_procedures_procedures');
        $this->dropIndex('et_ophtrconsent_procedure_procedures_procedures_ele_fk', 'et_ophtrconsent_procedure_procedures_procedures');
        $this->dropIndex('et_ophtrconsent_procedure_procedures_procedures_lku_fk', 'et_ophtrconsent_procedure_procedures_procedures');
        $this->dropIndex('et_ophtrconsent_procedure_procedures_procedures_lmui_fk', 'et_ophtrconsent_procedure_procedures_procedures');

        $this->renameTable('et_ophtrconsent_procedure_procedures_procedures', 'ophtrconsent_procedure_procedures_procedures');

        $this->addForeignKey('ophtrconsent_procedure_procedures_procedures_lmui_fk', 'ophtrconsent_procedure_procedures_procedures', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophtrconsent_procedure_procedures_procedures_lku_fk', 'ophtrconsent_procedure_procedures_procedures', 'proc_id', 'proc', 'id');
        $this->addForeignKey('ophtrconsent_procedure_procedures_procedures_ele_fk', 'ophtrconsent_procedure_procedures_procedures', 'element_id', 'et_ophtrconsent_procedure', 'id');
        $this->addForeignKey('ophtrconsent_procedure_procedures_procedures_cui_fk', 'ophtrconsent_procedure_procedures_procedures', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophtrconsent_type_type_cui_fk', 'et_ophtrconsent_type_type');
        $this->dropForeignKey('et_ophtrconsent_type_type_lmui_fk', 'et_ophtrconsent_type_type');

        $this->dropIndex('et_ophtrconsent_type_type_lmui_fk', 'et_ophtrconsent_type_type');
        $this->dropIndex('et_ophtrconsent_type_type_cui_fk', 'et_ophtrconsent_type_type');

        $this->renameTable('et_ophtrconsent_type_type', 'ophtrconsent_type_type');

        $this->addForeignKey('ophtrconsent_type_type_lmui_fk', 'ophtrconsent_type_type', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophtrconsent_type_type_cui_fk', 'ophtrconsent_type_type', 'created_user_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('ophtrconsent_type_type_cui_fk', 'ophtrconsent_type_type');
        $this->dropForeignKey('ophtrconsent_type_type_lmui_fk', 'ophtrconsent_type_type');

        $this->dropIndex('ophtrconsent_type_type_lmui_fk', 'ophtrconsent_type_type');
        $this->dropIndex('ophtrconsent_type_type_cui_fk', 'ophtrconsent_type_type');

        $this->renameTable('ophtrconsent_type_type', 'et_ophtrconsent_type_type');

        $this->addForeignKey('et_ophtrconsent_type_type_lmui_fk', 'et_ophtrconsent_type_type', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophtrconsent_type_type_cui_fk', 'et_ophtrconsent_type_type', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophtrconsent_procedure_procedures_procedures_cui_fk', 'ophtrconsent_procedure_procedures_procedures');
        $this->dropForeignKey('ophtrconsent_procedure_procedures_procedures_ele_fk', 'ophtrconsent_procedure_procedures_procedures');
        $this->dropForeignKey('ophtrconsent_procedure_procedures_procedures_lku_fk', 'ophtrconsent_procedure_procedures_procedures');
        $this->dropForeignKey('ophtrconsent_procedure_procedures_procedures_lmui_fk', 'ophtrconsent_procedure_procedures_procedures');

        $this->dropIndex('ophtrconsent_procedure_procedures_procedures_cui_fk', 'ophtrconsent_procedure_procedures_procedures');
        $this->dropIndex('ophtrconsent_procedure_procedures_procedures_ele_fk', 'ophtrconsent_procedure_procedures_procedures');
        $this->dropIndex('ophtrconsent_procedure_procedures_procedures_lku_fk', 'ophtrconsent_procedure_procedures_procedures');
        $this->dropIndex('ophtrconsent_procedure_procedures_procedures_lmui_fk', 'ophtrconsent_procedure_procedures_procedures');

        $this->renameTable('ophtrconsent_procedure_procedures_procedures', 'et_ophtrconsent_procedure_procedures_procedures');

        $this->addForeignKey('et_ophtrconsent_procedure_procedures_procedures_lmui_fk', 'et_ophtrconsent_procedure_procedures_procedures', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophtrconsent_procedure_procedures_procedures_lku_fk', 'et_ophtrconsent_procedure_procedures_procedures', 'proc_id', 'proc', 'id');
        $this->addForeignKey('et_ophtrconsent_procedure_procedures_procedures_ele_fk', 'et_ophtrconsent_procedure_procedures_procedures', 'element_id', 'et_ophtrconsent_procedure', 'id');
        $this->addForeignKey('et_ophtrconsent_procedure_procedures_procedures_cui_fk', 'et_ophtrconsent_procedure_procedures_procedures', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophtrconsent_procedure_proc_defaults_cui_fk', 'ophtrconsent_procedure_proc_defaults');
        $this->dropForeignKey('ophtrconsent_procedure_proc_defaults_lmui_fk', 'ophtrconsent_procedure_proc_defaults');

        $this->dropIndex('ophtrconsent_procedure_proc_defaults_lmui_fk', 'ophtrconsent_procedure_proc_defaults');
        $this->dropIndex('ophtrconsent_procedure_proc_defaults_cui_fk', 'ophtrconsent_procedure_proc_defaults');

        $this->renameTable('ophtrconsent_procedure_proc_defaults', 'et_ophtrconsent_procedure_proc_defaults');

        $this->addForeignKey('et_ophtrconsent_procedure_proc_defaults_lmui_fk', 'et_ophtrconsent_procedure_proc_defaults', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophtrconsent_procedure_proc_defaults_cui_fk', 'et_ophtrconsent_procedure_proc_defaults', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophtrconsent_procedure_add_procs_add_procs_cui_fk', 'ophtrconsent_procedure_add_procs_add_procs');
        $this->dropForeignKey('ophtrconsent_procedure_add_procs_add_procs_ele_fk', 'ophtrconsent_procedure_add_procs_add_procs');
        $this->dropForeignKey('ophtrconsent_procedure_add_procs_add_procs_lku_fk', 'ophtrconsent_procedure_add_procs_add_procs');
        $this->dropForeignKey('ophtrconsent_procedure_add_procs_add_procs_lmui_fk', 'ophtrconsent_procedure_add_procs_add_procs');

        $this->dropIndex('ophtrconsent_procedure_add_procs_add_procs_cui_fk', 'ophtrconsent_procedure_add_procs_add_procs');
        $this->dropIndex('ophtrconsent_procedure_add_procs_add_procs_ele_fk', 'ophtrconsent_procedure_add_procs_add_procs');
        $this->dropIndex('ophtrconsent_procedure_add_procs_add_procs_lku_fk', 'ophtrconsent_procedure_add_procs_add_procs');
        $this->dropIndex('ophtrconsent_procedure_add_procs_add_procs_lmui_fk', 'ophtrconsent_procedure_add_procs_add_procs');

        $this->renameTable('ophtrconsent_procedure_add_procs_add_procs', 'et_ophtrconsent_procedure_add_procs_add_procs');

        $this->addForeignKey('et_ophtrconsent_procedure_add_procs_add_procs_lmui_fk', 'et_ophtrconsent_procedure_add_procs_add_procs', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophtrconsent_procedure_add_procs_add_procs_lku_fk', 'et_ophtrconsent_procedure_add_procs_add_procs', 'proc_id', 'proc', 'id');
        $this->addForeignKey('et_ophtrconsent_procedure_add_procs_add_procs_ele_fk', 'et_ophtrconsent_procedure_add_procs_add_procs', 'element_id', 'et_ophtrconsent_procedure', 'id');
        $this->addForeignKey('et_ophtrconsent_procedure_add_procs_add_procs_cui_fk', 'et_ophtrconsent_procedure_add_procs_add_procs', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophtrconsent_permissions_images_cui_fk', 'ophtrconsent_permissions_images');
        $this->dropForeignKey('ophtrconsent_permissions_images_lmui_fk', 'ophtrconsent_permissions_images');

        $this->dropIndex('ophtrconsent_permissions_images_lmui_fk', 'ophtrconsent_permissions_images');
        $this->dropIndex('ophtrconsent_permissions_images_cui_fk', 'ophtrconsent_permissions_images');

        $this->renameTable('ophtrconsent_permissions_images', 'et_ophtrconsent_permissions_images');

        $this->addForeignKey('et_ophtrconsent_permissions_images_cui_fk', 'et_ophtrconsent_permissions_images', 'created_user_id', 'user', 'id');
        $this->addForeignKey('et_ophtrconsent_permissions_images_lmui_fk', 'et_ophtrconsent_permissions_images', 'last_modified_user_id', 'user', 'id');
    }
}
