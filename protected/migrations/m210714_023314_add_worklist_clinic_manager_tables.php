<?php

class m210714_023314_add_worklist_clinic_manager_tables extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'pathway_type',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) NOT NULL',
                'default_owner_id' => 'int(10) unsigned',
                'is_preset' => 'tinyint(1) DEFAULT 1',
                'active' => 'tinyint(1) unsigned DEFAULT 1',
            ),
            true
        );

        $this->createOETable(
            'pathway',
            array(
                'id' => 'pk',
                'worklist_patient_id' => 'int NOT NULL',
                'pathway_type_id' => 'int NOT NULL',
                'owner_id' => 'int(10) unsigned',
                'start_time' => 'datetime',
                'end_time' => 'datetime',
                'status' => 'int DEFAULT 0',
                'did_not_attend' => 'tinyint(1) unsigned DEFAULT 0'
            ),
            true
        );

        $this->createOETable(
            'pathway_step_type',
            array(
                'id' => 'pk',
                'action_id' => 'int',
                'default_state' => 'int',
                'group' => 'varchar(20)',
                'small_icon' => 'varchar(20)',
                'large_icon' => 'varchar(20)',
                'widget_view' => 'varchar(255)',
                'type' => 'varchar(20) NOT NULL',
                'user_can_create' => 'tinyint(1) DEFAULT 1',
                'short_name' => 'varchar(20) NOT NULL',
                'long_name' => 'varchar(100) NOT NULL',
                'active' => 'int DEFAULT 1',
            ),
            true
        );

        $this->createOETable(
            'pathway_type_step',
            array(
                'id' => 'pk',
                'pathway_type_id' => 'int NOT NULL',
                'step_type_id' => 'int NOT NULL',
                'order' => 'int NOT NULL',
                'short_name' => 'varchar(20) NOT NULL',
                'long_name' => 'varchar(100) NOT NULL',
                'status' => 'int DEFAULT 0',
            ),
            true
        );

        $this->createOETable(
            'pathway_step',
            array(
                'id' => 'pk',
                'pathway_id' => 'int NOT NULL',
                'step_type_id' => 'int NOT NULL',
                'short_name' => 'varchar(20) NOT NULL',
                'long_name' => 'varchar(100) NOT NULL',
                'started_user_id' => 'int(10) unsigned',
                'completed_user_id' => 'int(10) unsigned',
                'order' => 'int NOT NULL',
                'pincode' => 'varchar(255)',
                'start_time' => 'datetime',
                'end_time' => 'datetime',
                'status' => 'int DEFAULT 0',
            ),
            true
        );

        $this->createOETable(
            'pathway_comment',
            array(
                'id' => 'pk',
                'pathway_id' => 'int NOT NULL',
                'doctor_id' => 'int(10) unsigned NOT NULL',
                'comment' => 'text NOT NULL',
            ),
            true
        );

        $this->createOETable(
            'pathway_step_comment',
            array(
                'id' => 'pk',
                'pathway_step_id' => 'int NOT NULL',
                'doctor_id' => 'int(10) unsigned NOT NULL',
                'comment' => 'text NOT NULL',
            ),
            true
        );

        $this->addForeignKey(
            'pathway_type_owner_fk',
            'pathway_type',
            'default_owner_id',
            'user',
            'id'
        );

        $this->addForeignKey(
            'pathway_worklist_patient_fk',
            'pathway',
            'worklist_patient_id',
            'worklist_patient',
            'id'
        );
        $this->addForeignKey(
            'pathway_type_fk',
            'pathway',
            'pathway_type_id',
            'pathway_type',
            'id'
        );
        $this->addForeignKey(
            'pathway_owner_fk',
            'pathway',
            'owner_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'pathway_step_pathway_fk',
            'pathway_step',
            'pathway_id',
            'pathway',
            'id'
        );
        $this->addForeignKey(
            'pathway_step_type_fk',
            'pathway_step',
            'step_type_id',
            'pathway_step_type',
            'id'
        );
        $this->addForeignKey(
            'pathway_step_started_user_fk',
            'pathway_step',
            'started_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'pathway_step_completed_user_fk',
            'pathway_step',
            'completed_user_id',
            'user',
            'id'
        );

        $this->addForeignKey(
            'pathway_comment_pathway_fk',
            'pathway_comment',
            'pathway_id',
            'pathway',
            'id'
        );

        $this->addForeignKey(
            'pathway_comment_doctor_fk',
            'pathway_comment',
            'doctor_id',
            'user',
            'id'
        );

        $this->addForeignKey(
            'pathway_step_comment_pathway_step_fk',
            'pathway_step_comment',
            'pathway_step_id',
            'pathway_step',
            'id'
        );

        $this->addForeignKey(
            'pathway_step_comment_doctor_fk',
            'pathway_comment',
            'doctor_id',
            'user',
            'id'
        );

        $this->addForeignKey(
            'pathway_type_step_pt_fk',
            'pathway_type_step',
            'pathway_type_id',
            'pathway_type',
            'id'
        );

        $this->addForeignKey(
            'pathway_type_step_st_fk',
            'pathway_type_step',
            'step_type_id',
            'pathway_step_type',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable(
            'pathway_type_step',
            true
        );
        $this->dropOETable(
            'pathway_step_comment',
            true
        );
        $this->dropOETable(
            'pathway_comment',
            true
        );
        $this->dropOETable(
            'pathway_step',
            true
        );
        $this->dropOETable(
            'pathway_step_type',
            true
        );
        $this->dropOETable(
            'pathway',
            true
        );
        $this->dropOETable(
            'pathway_type',
            true
        );
    }
}
