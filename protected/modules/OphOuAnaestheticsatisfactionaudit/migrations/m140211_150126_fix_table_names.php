<?php

class m140211_150126_fix_table_names extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('et_ophauanaestheticsataudit_anaesthetist_lookup_created_user_fk', 'et_ophouanaestheticsataudit_anaesthetist_lookup');
        $this->dropForeignKey('et_ophauanaestheticsataudit_anaesthetist_lookup_last_mod_user_fk', 'et_ophouanaestheticsataudit_anaesthetist_lookup');
        $this->dropForeignKey('et_ophauanaestheticsataudit_anaesthetist_lookup_user_id_fk', 'et_ophouanaestheticsataudit_anaesthetist_lookup');

        $this->dropIndex('et_ophauanaestheticsataudit_anaesthetist_lookup_last_mod_user_fk', 'et_ophouanaestheticsataudit_anaesthetist_lookup');
        $this->dropIndex('et_ophauanaestheticsataudit_anaesthetist_lookup_created_user_fk', 'et_ophouanaestheticsataudit_anaesthetist_lookup');

        $this->renameTable('et_ophouanaestheticsataudit_anaesthetist_lookup', 'ophouanaestheticsataudit_anaesthetist_lookup');

        $this->createIndex('ophauanaestheticsataudit_anaesthetist_lookup_user_id_fk', 'ophouanaestheticsataudit_anaesthetist_lookup', 'user_id');
        $this->addForeignKey('ophauanaestheticsataudit_anaesthetist_lookup_user_id_fk', 'ophouanaestheticsataudit_anaesthetist_lookup', 'user_id', 'user', 'id');
        $this->addForeignKey('ophauanaestheticsataudit_anaesthetist_lookup_last_mod_user_fk', 'ophouanaestheticsataudit_anaesthetist_lookup', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophauanaestheticsataudit_anaesthetist_lookup_created_user_fk', 'ophouanaestheticsataudit_anaesthetist_lookup', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_auophanaestheticsataudit_notes_ready_for_discharge_cui_fk', 'et_ophouanaestheticsataudit_notes_ready_for_discharge');
        $this->dropForeignKey('et_auophanaestheticsataudit_notes_ready_for_discharge_lmui_fk', 'et_ophouanaestheticsataudit_notes_ready_for_discharge');

        $this->dropIndex('et_auophanaestheticsataudit_notes_ready_for_discharge_lmui_fk', 'et_ophouanaestheticsataudit_notes_ready_for_discharge');
        $this->dropIndex('et_auophanaestheticsataudit_notes_ready_for_discharge_cui_fk', 'et_ophouanaestheticsataudit_notes_ready_for_discharge');

        $this->renameTable('et_ophouanaestheticsataudit_notes_ready_for_discharge', 'ophouanaestheticsataudit_notes_ready_for_discharge');

        $this->addForeignKey('auophanaestheticsataudit_notes_ready_for_discharge_lmui_fk', 'ophouanaestheticsataudit_notes_ready_for_discharge', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('auophanaestheticsataudit_notes_ready_for_discharge_cui_fk', 'ophouanaestheticsataudit_notes_ready_for_discharge', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_body_temp_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_body_temp');
        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_body_temp_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_body_temp');

        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_body_temp_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_body_temp');
        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_body_temp_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_body_temp');

        $this->renameTable('et_ophouanaestheticsataudit_vitalsigns_body_temp', 'ophouanaestheticsataudit_vitalsigns_body_temp');

        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_body_temp_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_body_temp', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_body_temp_cui_fk', 'ophouanaestheticsataudit_vitalsigns_body_temp', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_conscious_lvl');
        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_conscious_lvl');

        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_conscious_lvl');
        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_conscious_lvl');

        $this->renameTable('et_ophouanaestheticsataudit_vitalsigns_conscious_lvl', 'ophouanaestheticsataudit_vitalsigns_conscious_lvl');

        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_conscious_lvl_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_conscious_lvl', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_conscious_lvl_cui_fk', 'ophouanaestheticsataudit_vitalsigns_conscious_lvl', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_heart_rate_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_heart_rate');
        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_heart_rate_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_heart_rate');

        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_heart_rate_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_heart_rate');
        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_heart_rate_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_heart_rate');

        $this->renameTable('et_ophouanaestheticsataudit_vitalsigns_heart_rate', 'ophouanaestheticsataudit_vitalsigns_heart_rate');

        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_heart_rate_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_heart_rate', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_heart_rate_cui_fk', 'ophouanaestheticsataudit_vitalsigns_heart_rate', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_oxygen_saturation');
        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_oxygen_saturation');

        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_oxygen_saturation');
        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_oxygen_saturation');

        $this->renameTable('et_ophouanaestheticsataudit_vitalsigns_oxygen_saturation', 'ophouanaestheticsataudit_vitalsigns_oxygen_saturation');

        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_oxygen_saturation_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_oxygen_saturation', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_oxygen_saturation_cui_fk', 'ophouanaestheticsataudit_vitalsigns_oxygen_saturation', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_systolic_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_systolic');
        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_systolic_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_systolic');

        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_systolic_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_systolic');
        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_systolic_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_systolic');

        $this->renameTable('et_ophouanaestheticsataudit_vitalsigns_systolic', 'ophouanaestheticsataudit_vitalsigns_systolic');

        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_systolic_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_systolic', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_systolic_cui_fk', 'ophouanaestheticsataudit_vitalsigns_systolic', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_respiratory_rate');
        $this->dropForeignKey('et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_respiratory_rate');

        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_respiratory_rate');
        $this->dropIndex('et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_respiratory_rate');

        $this->renameTable('et_ophouanaestheticsataudit_vitalsigns_respiratory_rate', 'ophouanaestheticsataudit_vitalsigns_respiratory_rate');

        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_respiratory_rate_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_respiratory_rate', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophauanaestheticsataudit_vitalsigns_respiratory_rate_cui_fk', 'ophouanaestheticsataudit_vitalsigns_respiratory_rate', 'created_user_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_respiratory_rate_cui_fk', 'ophouanaestheticsataudit_vitalsigns_respiratory_rate');
        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_respiratory_rate_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_respiratory_rate');

        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_respiratory_rate_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_respiratory_rate');
        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_respiratory_rate_cui_fk', 'ophouanaestheticsataudit_vitalsigns_respiratory_rate');

        $this->renameTable('ophouanaestheticsataudit_vitalsigns_respiratory_rate', 'et_ophouanaestheticsataudit_vitalsigns_respiratory_rate');

        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_respiratory_rate', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_respiratory_rate', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_systolic_cui_fk', 'ophouanaestheticsataudit_vitalsigns_systolic');
        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_systolic_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_systolic');

        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_systolic_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_systolic');
        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_systolic_cui_fk', 'ophouanaestheticsataudit_vitalsigns_systolic');

        $this->renameTable('ophouanaestheticsataudit_vitalsigns_systolic', 'et_ophouanaestheticsataudit_vitalsigns_systolic');

        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_systolic_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_systolic', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_systolic_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_systolic', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_oxygen_saturation_cui_fk', 'ophouanaestheticsataudit_vitalsigns_oxygen_saturation');
        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_oxygen_saturation_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_oxygen_saturation');

        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_oxygen_saturation_cui_fk', 'ophouanaestheticsataudit_vitalsigns_oxygen_saturation');
        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_oxygen_saturation_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_oxygen_saturation');

        $this->renameTable('ophouanaestheticsataudit_vitalsigns_oxygen_saturation', 'et_ophouanaestheticsataudit_vitalsigns_oxygen_saturation');

        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_oxygen_saturation', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_oxygen_saturation', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_heart_rate_cui_fk', 'ophouanaestheticsataudit_vitalsigns_heart_rate');
        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_heart_rate_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_heart_rate');

        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_heart_rate_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_heart_rate');
        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_heart_rate_cui_fk', 'ophouanaestheticsataudit_vitalsigns_heart_rate');

        $this->renameTable('ophouanaestheticsataudit_vitalsigns_heart_rate', 'et_ophouanaestheticsataudit_vitalsigns_heart_rate');

        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_heart_rate_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_heart_rate', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_heart_rate_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_heart_rate', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_conscious_lvl_cui_fk', 'ophouanaestheticsataudit_vitalsigns_conscious_lvl');
        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_conscious_lvl_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_conscious_lvl');

        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_conscious_lvl_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_conscious_lvl');
        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_conscious_lvl_cui_fk', 'ophouanaestheticsataudit_vitalsigns_conscious_lvl');

        $this->renameTable('ophouanaestheticsataudit_vitalsigns_conscious_lvl', 'et_ophouanaestheticsataudit_vitalsigns_conscious_lvl');

        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_conscious_lvl', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_conscious_lvl', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_body_temp_cui_fk', 'ophouanaestheticsataudit_vitalsigns_body_temp');
        $this->dropForeignKey('ophauanaestheticsataudit_vitalsigns_body_temp_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_body_temp');

        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_body_temp_lmui_fk', 'ophouanaestheticsataudit_vitalsigns_body_temp');
        $this->dropIndex('ophauanaestheticsataudit_vitalsigns_body_temp_cui_fk', 'ophouanaestheticsataudit_vitalsigns_body_temp');

        $this->renameTable('ophouanaestheticsataudit_vitalsigns_body_temp', 'et_ophouanaestheticsataudit_vitalsigns_body_temp');

        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_body_temp_lmui_fk', 'et_ophouanaestheticsataudit_vitalsigns_body_temp', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophauanaestheticsataudit_vitalsigns_body_temp_cui_fk', 'et_ophouanaestheticsataudit_vitalsigns_body_temp', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('auophanaestheticsataudit_notes_ready_for_discharge_cui_fk', 'ophouanaestheticsataudit_notes_ready_for_discharge');
        $this->dropForeignKey('auophanaestheticsataudit_notes_ready_for_discharge_lmui_fk', 'ophouanaestheticsataudit_notes_ready_for_discharge');

        $this->dropIndex('auophanaestheticsataudit_notes_ready_for_discharge_lmui_fk', 'ophouanaestheticsataudit_notes_ready_for_discharge');
        $this->dropIndex('auophanaestheticsataudit_notes_ready_for_discharge_cui_fk', 'ophouanaestheticsataudit_notes_ready_for_discharge');

        $this->renameTable('ophouanaestheticsataudit_notes_ready_for_discharge', 'et_ophouanaestheticsataudit_notes_ready_for_discharge');

        $this->addForeignKey('et_auophanaestheticsataudit_notes_ready_for_discharge_lmui_fk', 'et_ophouanaestheticsataudit_notes_ready_for_discharge', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_auophanaestheticsataudit_notes_ready_for_discharge_cui_fk', 'et_ophouanaestheticsataudit_notes_ready_for_discharge', 'created_user_id', 'user', 'id');

        //

        $this->dropForeignKey('ophauanaestheticsataudit_anaesthetist_lookup_created_user_fk', 'ophouanaestheticsataudit_anaesthetist_lookup');
        $this->dropForeignKey('ophauanaestheticsataudit_anaesthetist_lookup_last_mod_user_fk', 'ophouanaestheticsataudit_anaesthetist_lookup');
        $this->dropForeignKey('ophauanaestheticsataudit_anaesthetist_lookup_user_id_fk', 'ophouanaestheticsataudit_anaesthetist_lookup');

        $this->dropIndex('ophauanaestheticsataudit_anaesthetist_lookup_last_mod_user_fk', 'ophouanaestheticsataudit_anaesthetist_lookup');
        $this->dropIndex('ophauanaestheticsataudit_anaesthetist_lookup_created_user_fk', 'ophouanaestheticsataudit_anaesthetist_lookup');
        $this->dropIndex('ophauanaestheticsataudit_anaesthetist_lookup_user_id_fk', 'ophouanaestheticsataudit_anaesthetist_lookup');

        $this->renameTable('ophouanaestheticsataudit_anaesthetist_lookup', 'et_ophouanaestheticsataudit_anaesthetist_lookup');

        $this->createIndex('et_ophauanaestheticsataudit_anaesthetist_lookup_last_mod_user_fk', 'et_ophouanaestheticsataudit_anaesthetist_lookup', 'last_modified_user_id');
        $this->createIndex('et_ophauanaestheticsataudit_anaesthetist_lookup_created_user_fk', 'et_ophouanaestheticsataudit_anaesthetist_lookup', 'created_user_id');

        $this->addForeignKey('et_ophauanaestheticsataudit_anaesthetist_lookup_user_id_fk', 'et_ophouanaestheticsataudit_anaesthetist_lookup', 'user_id', 'user', 'id');
        $this->addForeignKey('et_ophauanaestheticsataudit_anaesthetist_lookup_last_mod_user_fk', 'et_ophouanaestheticsataudit_anaesthetist_lookup', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophauanaestheticsataudit_anaesthetist_lookup_created_user_fk', 'et_ophouanaestheticsataudit_anaesthetist_lookup', 'created_user_id', 'user', 'id');
    }
}
