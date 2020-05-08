<?php

class m140311_093616_add_sesion_metadata extends CDbMigration
{
    public function up()
    {
        $this->createTable('ophtroperationbooking_operation_session_unavailreason', array(
                        'id' => 'pk',
                        'name' => 'string NOT NULL',
                        'enabled' => 'boolean NOT NULL DEFAULT true',
                        'display_order' => 'integer NOT NULL',
                        'last_modified_user_id' => 'int(10) unsigned DEFAULT 1',
                        'last_modified_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
                        'created_user_id' => 'int(10) unsigned  DEFAULT 1',
                        'created_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
                ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey(
            'ophtroperationbooking_operation_session_unavailreas_lmui_fk',
            'ophtroperationbooking_operation_session_unavailreason',
            'last_modified_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'ophtroperationbooking_operation_session_unavailreas_cui_fk',
            'ophtroperationbooking_operation_session_unavailreason',
            'created_user_id',
            'user',
            'id'
        );

        $this->addColumn('ophtroperationbooking_operation_session', 'unavailablereason_id', 'integer');
        $this->addForeignKey(
            'ophtroperationbooking_operation_session_uari_fk',
            'ophtroperationbooking_operation_session',
            'unavailablereason_id',
            'ophtroperationbooking_operation_session_unavailreason',
            'id'
        );

        $this->addColumn('ophtroperationbooking_operation_session', 'max_procedures', 'integer DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('ophtroperationbooking_operation_session', 'max_procedures');
        $this->dropForeignKey('ophtroperationbooking_operation_session_uari_fk', 'ophtroperationbooking_operation_session');
        $this->dropColumn('ophtroperationbooking_operation_session', 'unavailablereason_id');
        $this->dropTable('ophtroperationbooking_operation_session_unavailreason');
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
