<?php

class m180306_112130_whiteboard_settings extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophtroperationbooking_whiteboard_settings', array(
            'id' => 'pk',
            'display_order' => "tinyint(3) unsigned DEFAULT '0'",
            'field_type_id' => 'int(10) unsigned NOT NULL',
            'key' =>  'varchar(64) NOT NULL',
            'name' => 'varchar(64) NOT NULL',
            'data' =>  'varchar(4096) NOT NULL',
            'default_value' =>  'varchar(64) NOT NULL',
        ), $versioned = true);

        $this->addForeignKey('ophtroperationbooking_int_ref_set_field_type_id_fk', 'ophtroperationbooking_whiteboard_settings', 'field_type_id', 'setting_field_type', 'id');
        $this->addForeignKey('ophtroperationbooking_int_ref_set_created_user_id_fk', 'ophtroperationbooking_whiteboard_settings', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophtroperationbooking_int_ref_set_last_modified_user_id_fk', 'ophtroperationbooking_whiteboard_settings', 'last_modified_user_id', 'user', 'id');

        $this->createOETable('ophtroperationbooking_whiteboard_settings_data', array(
            'id' => 'pk',
            'element_type_id' => 'int(10) unsigned DEFAULT NULL',
            'key' =>  'varchar(64) NOT NULL',
            'value' => 'varchar(255) COLLATE utf8_bin NULL',
        ), $versioned = true);

        $this->insert('ophtroperationbooking_whiteboard_settings', array(
            'field_type_id' => 4,
            'key' => 'refresh_after_opbooking_completed',
            'name' => 'Allow whiteboard to refresh after Booking is completed (hours)'
        ));

        $refresh_setting = new OphTrOperationbooking_Whiteboard_Settings_Data();
        $refresh_setting->key = 'refresh_after_opbooking_completed';
        $refresh_setting->value = 0; //hour
        $refresh_setting->save();

        $this->addColumn('et_ophtroperationbooking_operation', 'operation_completion_date', 'datetime NULL');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'operation_completion_date', 'datetime NULL');
    }

    public function down()
    {
        $this->dropForeignKey('ophtroperationbooking_int_ref_set_field_type_id_fk', 'ophtroperationbooking_whiteboard_settings');
        $this->dropForeignKey('ophtroperationbooking_int_ref_set_created_user_id_fk', 'ophtroperationbooking_whiteboard_settings');
        $this->dropForeignKey('ophtroperationbooking_int_ref_set_last_modified_user_id_fk', 'ophtroperationbooking_whiteboard_settings');

        $this->dropOETable('ophtroperationbooking_whiteboard_settings', $versioned = true);
        $this->dropOETable('ophtroperationbooking_whiteboard_settings_data', $versioned = true);

        $this->dropColumn('et_ophtroperationbooking_operation', 'operation_completion_date');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'operation_completion_date');
    }
}