<?php

class m160503_142416_initial_worklist_models extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'worklist',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) NOT NULL',
                'description' => 'text',
                'start' => 'datetime',
                'end' => 'datetime',
                'scheduled' => 'boolean default false NOT NULL',
            ),
            true
        );

        $this->createOETable(
            'worklist_attribute',
            array(
                'id' => 'pk',
                'worklist_id' => 'int(11) NOT NULL',
                'name' => 'varchar(255) NOT NULL',
                'display_order' => 'int(3)',
                'UNIQUE KEY `worklist_attribute_unique_order` (`display_order`,`worklist_id`)',
            )
        );
        $this->addForeignKey('worklist_attribute_wl_fk', 'worklist_attribute', 'worklist_id', 'worklist', 'id', 'CASCADE');

        $this->createOETable(
            'worklist_display_context',
            array(
                'id' => 'pk',
                'worklist_id' => 'int(11) NOT NULL',
                'firm_id' => 'int(10) unsigned',
                'subspecialty_id' => 'int(10) unsigned',
                'site_id' => 'int(10) unsigned',
            )
        );

        $this->addForeignKey('worklist_dispctxt_wl_fk', 'worklist_display_context', 'worklist_id', 'worklist', 'id', 'CASCADE');
        $this->addForeignKey('worklist_dispctxt_firm_fk', 'worklist_display_context', 'firm_id', 'firm', 'id', 'CASCADE');
        $this->addForeignKey('worklist_dispctxt_subspecialty_fk', 'worklist_display_context', 'subspecialty_id', 'subspecialty', 'id', 'CASCADE');
        $this->addForeignKey('worklist_dispctxt_site_fk', 'worklist_display_context', 'site_id', 'site', 'id', 'CASCADE');

        // TODO: implement user privileges

        $this->createOETable(
            'worklist_patient',
            array(
                'id' => 'pk',
                'worklist_id' => 'int(11) NOT NULL',
                'patient_id' => 'int(10) unsigned NOT NULL',
                'when' => 'datetime',
            ),
            true
        );

        $this->addForeignKey('worklist_patient_wl_fk', 'worklist_patient', 'worklist_id', 'worklist', 'id', 'CASCADE');
        $this->addForeignKey('worklist_patient_p_fk', 'worklist_patient', 'patient_id', 'patient', 'id', 'CASCADE');

        $this->createOETable(
            'worklist_patient_attribute',
            array(
                'id' => 'pk',
                'worklist_attribute_id' => 'int(11) NOT NULL',
                'worklist_patient_id' => 'int(11) NOT NULL',
                'attribute_value' => 'string',
            ),
            true
        );

        $this->addForeignKey(
            'worklist_patient_attribute_attr_fk',
            'worklist_patient_attribute',
            'worklist_attribute_id',
            'worklist_attribute',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'worklist_patient_attribute_pat_fk',
            'worklist_patient_attribute',
            'worklist_patient_id',
            'worklist_patient',
            'id',
            'CASCADE'
        );

        $this->createOETable(
            'worklist_display_order',
            array(
                'id' => 'pk',
                'worklist_id' => 'int(11) NOT NULL',
                'user_id' => 'int(10) unsigned NOT NULL',
                'display_order' => 'int(3) NOT NULL',
            )
        );

        $this->addForeignKey('worklist_disporder_wl_fk', 'worklist_display_order', 'worklist_id', 'worklist', 'id', 'CASCADE');
        $this->addForeignKey('worklist_disporder_u_fk', 'worklist_display_order', 'user_id', 'user', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropOETable('worklist_display_order');
        $this->dropOETable('worklist_patient_attribute', true);
        $this->dropOETable('worklist_patient', true);
        $this->dropOETable('worklist_display_context');
        $this->dropOETable('worklist_attribute');
        $this->dropOETable('worklist', true);
    }
}
