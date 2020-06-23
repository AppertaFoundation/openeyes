<?php

class m160510_075145_add_automatic_worklists extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'worklist_definition',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) NOT NULL',
                'description' => 'text',
                'worklist_name' => 'string',
                'rrule' => 'text',
                'start_time' => 'time NOT NULL',
                'end_time' => 'time NOT NULL',
                'active_from' => 'datetime NOT NULL',
                'active_until' => 'datetime',
                'scheduled' => 'boolean default false NOT NULL',
                'display_order' => 'int(8) NOT NULL',
            ),
            true
        );

        $this->createOETable(
            'worklist_definition_mapping',
            array(
                'id' => 'pk',
                'worklist_definition_id' => 'int(11) NOT NULL',
                'key' => 'string NOT NULL',
                'display_order' => 'int(2)',
            )
        );

        $this->addForeignKey(
            'worklist_definition_mapping_wl_fk',
            'worklist_definition_mapping',
            'worklist_definition_id',
            'worklist_definition',
            'id',
            'CASCADE'
        );

        $this->createOETable(
            'worklist_definition_mapping_value',
            array(
                'id' => 'pk',
                'worklist_definition_mapping_id' => 'int(11) NOT NULL',
                'mapping_value' => 'varchar(1024) NOT NULL',
            )
        );

        $this->addForeignKey(
            'worklist_definition_mapping_value_wldm_fk',
            'worklist_definition_mapping_value',
            'worklist_definition_mapping_id',
            'worklist_definition_mapping',
            'id',
            'CASCADE'
        );

        $this->createOETable(
            'worklist_definition_display_context',
            array(
            'id' => 'pk',
            'worklist_definition_id' => 'int(11) NOT NULL',
            'firm_id' => 'int(10) unsigned',
            'subspecialty_id' => 'int(10) unsigned',
            'site_id' => 'int(10) unsigned',
            )
        );

        $this->addForeignKey('worklist_definition_dispctxt_wl_fk', 'worklist_definition_display_context', 'worklist_definition_id', 'worklist_definition', 'id', 'CASCADE');
        $this->addForeignKey('worklist_definition_dispctxt_firm_fk', 'worklist_definition_display_context', 'firm_id', 'firm', 'id', 'CASCADE');
        $this->addForeignKey('worklist_definition_dispctxt_subspecialty_fk', 'worklist_definition_display_context', 'subspecialty_id', 'subspecialty', 'id', 'CASCADE');
        $this->addForeignKey('worklist_definition_dispctxt_site_fk', 'worklist_definition_display_context', 'site_id', 'site', 'id', 'CASCADE');

        $this->addColumn('worklist', 'worklist_definition_id', 'int(11)');

        $this->addForeignKey('worklist_wldfn_fk', 'worklist', 'worklist_definition_id', 'worklist_definition', 'id', 'CASCADE');

        $this->addColumn('worklist_version', 'worklist_definition_id', 'int(11)');
    }

    public function down()
    {
        $this->dropColumn('worklist_version', 'worklist_definition_id');
        $this->dropForeignKey('worklist_wldfn_fk', 'worklist');
        $this->dropColumn('worklist', 'worklist_definition_id');
        $this->dropOETable('worklist_definition_display_context');
        $this->dropOETable('worklist_definition_mapping_value');
        $this->dropOETable('worklist_definition_mapping');
        $this->dropOETable('worklist_definition', true);
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
