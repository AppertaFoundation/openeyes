<?php

class m190419_132252_ophinlabresults_migrate_results_type_to_new_structure extends OEMigration
{
	public function up()
	{
	    $this->createOETable('ophinlabresults_field_type',
            [
                'id' => 'int(10) unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(64)'
            ]);

	    $this->insert('ophinlabresults_field_type', ['name' => 'Text Field']);
	    $this->insert('ophinlabresults_field_type', ['name' => 'Numeric Field']);
	    $this->insert('ophinlabresults_field_type', ['name' => 'Drop-down Field']);
	    $this->addColumn('ophinlabresults_type', 'field_type_id', 'int(10) unsigned NOT NULL DEFAULT 1');
	    $this->addColumn('ophinlabresults_type_version', 'field_type_id', 'int(10) unsigned NOT NULL');
        $this->addForeignKey(
            'ophinlabresults_type_field_type_id_result_type_fk',
            'ophinlabresults_type',
            'field_type_id',
            'ophinlabresults_field_type',
            'id'
        );

        $this->addColumn('ophinlabresults_type' , 'default_units' , 'varchar(64)');
        $this->addColumn('ophinlabresults_type_version' , 'default_units' , 'varchar(64)');
        $this->addColumn('ophinlabresults_type', 'custom_warning_message' , 'varchar(200)');
        $this->addColumn('ophinlabresults_type_version', 'custom_warning_message' , 'varchar(200)');
        $this->addColumn('ophinlabresults_type', 'min_range' , 'int(10)');
        $this->addColumn('ophinlabresults_type_version', 'min_range' , 'int(10)');
        $this->addColumn('ophinlabresults_type', 'max_range' , 'int(10)');
        $this->addColumn('ophinlabresults_type_version', 'max_range' , 'int(10)');
        $this->addColumn('ophinlabresults_type', 'normal_min' , 'int(10)');
        $this->addColumn('ophinlabresults_type_version', 'normal_min' , 'int(10)');
        $this->addColumn('ophinlabresults_type', 'normal_max' , 'int(10)');
        $this->addColumn('ophinlabresults_type_version', 'normal_max' , 'int(10)');
        $this->addColumn('ophinlabresults_type', 'show_on_whiteboard', 'boolean not null default false');
        $this->addColumn('ophinlabresults_type_version', 'show_on_whiteboard', 'boolean not null default false');
        $this->dropForeignKey('labresults_type_result_element', 'ophinlabresults_type');
	}

	public function down()
	{
        $this->addForeignKey('labresults_type_result_element', 'ophinlabresults_type', 'result_element_id', 'element_type', 'id');
        $this->dropColumn('ophinlabresults_type', 'show_on_whiteboard');
        $this->dropColumn('ophinlabresults_type_version', 'show_on_whiteboard');
        $this->dropColumn('ophinlabresults_type', 'normal_max');
        $this->dropColumn('ophinlabresults_type_version', 'normal_max');
        $this->dropColumn('ophinlabresults_type', 'normal_min');
        $this->dropColumn('ophinlabresults_type_version', 'normal_min');
        $this->dropColumn('ophinlabresults_type', 'max_range');
        $this->dropColumn('ophinlabresults_type_version', 'max_range');
        $this->dropColumn('ophinlabresults_type', 'min_range');
        $this->dropColumn('ophinlabresults_type_version', 'min_range');
        $this->dropColumn('ophinlabresults_type', 'custom_warning_message');
        $this->dropColumn('ophinlabresults_type_version', 'custom_warning_message');
        $this->dropColumn('ophinlabresults_type', 'default_units');
        $this->dropColumn('ophinlabresults_type_version', 'default_units');

        $this->dropForeignKey('ophinlabresults_type_field_type_id_result_type_fk', 'ophinlabresults_type');

        $this->dropColumn('ophinlabresults_type', 'field_type_id');
        $this->dropColumn('ophinlabresults_type_version', 'field_type_id');

        $this->dropTable('ophinlabresults_field_type');
	}
}