<?php

class m140220_162303_add_patient_unavailable extends OEMigration
{
    public function up()
    {
        $this->createTable('ophtroperationbooking_scheduleope_patientunavailreason', array(
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
            'ophtroperationbooking_scheduleope_patientunavailreas_lmui_fk',
            'ophtroperationbooking_scheduleope_patientunavailreason',
            'last_modified_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'ophtroperationbooking_scheduleope_patientunavailreas_cui_fk',
            'ophtroperationbooking_scheduleope_patientunavailreason',
            'created_user_id',
            'user',
            'id'
        );

        $this->createTable('ophtroperationbooking_scheduleope_patientunavail', array(
                    'id' => 'pk',
                    'start_date' => 'date NOT NULL',
                    'end_date' => 'date NOT NULL',
                    'reason_id' => 'integer NOT NULL',
                    'element_id' => 'int(10) unsigned DEFAULT 1',
                    'last_modified_user_id' => 'int(10) unsigned DEFAULT 1',
                    'last_modified_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
                    'created_user_id' => 'int(10) unsigned DEFAULT 1',
                    'created_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
                ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey(
            'ophtroperationbooking_scheduleope_patientunavail_lmui_fk',
            'ophtroperationbooking_scheduleope_patientunavail',
            'last_modified_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'ophtroperationbooking_scheduleope_patientunavail_cui_fk',
            'ophtroperationbooking_scheduleope_patientunavail',
            'created_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'ophtroperationbooking_scheduleope_patientunavail_ri_fk',
            'ophtroperationbooking_scheduleope_patientunavail',
            'reason_id',
            'ophtroperationbooking_scheduleope_patientunavailreason',
            'id'
        );
        $this->addForeignKey(
            'ophtroperationbooking_scheduleope_patientunavail_ei_fk',
            'ophtroperationbooking_scheduleope_patientunavail',
            'element_id',
            'et_ophtroperationbooking_scheduleope',
            'id'
        );

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);
    }

    public function down()
    {
        $this->dropTable('ophtroperationbooking_scheduleope_patientunavail');
        $this->dropTable('ophtroperationbooking_scheduleope_patientunavailreason');
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
