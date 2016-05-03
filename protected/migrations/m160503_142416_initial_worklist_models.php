<?php

class m160503_142416_initial_worklist_models extends OEMigration
{
	public function up()
	{
		$this->createOETable('worklist_type',
			array(
				'id' => 'pk',
				'name' => 'varchar(32) NOT NULL',
				'scheduled' => 'boolean default false'
			),
			true
		);

		$this->createOETable('worklist',
			array(
				'id' => 'pk',
				'name' => 'varchar(255) NOT NULL',
				'description' => 'text',
				'start' => 'datetime',
				'end' => 'datetime',
				'worklist_type_id' => 'int(11) NOT NULL',
				'CONSTRAINT `worklist_type_f_id_fk` FOREIGN KEY (`worklist_type_id`) REFERENCES `worklist_type` (`id`)',
			),
			true
		);

		$this->createOETable('worklist_attribute',
			array(
				'id' => 'pk',
				'worklist_id' => 'int(11) NOT NULL',
				'name' => 'varchar(255) NOT NULL',
				'display_order' => 'int(3) NOT NULL',
				'UNIQUE KEY `worklist_attribute_unique_order` (`display_order`)'
			));
		$this->addForeignKey('worklist_attribute_wl_fk', 'worklist_attribute', 'worklist_id', 'worklist', 'id', 'CASCADE');

		$this->createOETable('worklist_display_context',
			array(
				'id' => 'pk',
				'worklist_id' => 'int(11) NOT NULL',
				'firm_id' => 'int(10) unsigned',
				'subspecialty_id' => 'int(10) unsigned',
				'site_id' => 'int(10) unsigned',
			));

		$this->addForeignKey('worklist_dispctxt_wl_fk', 'worklist_display_context', 'worklist_id', 'worklist', 'id', 'CASCADE');
		$this->addForeignKey('worklist_dispctxt_firm_fk', 'worklist_display_context', 'firm_id', 'firm', 'id', 'CASCADE');
		$this->addForeignKey('worklist_dispctxt_subspecialty_fk', 'worklist_display_context', 'subspecialty_id', 'subspecialty', 'id', 'CASCADE');
		$this->addForeignKey('worklist_dispctxt_site_fk', 'worklist_display_context', 'site_id', 'site', 'id', 'CASCADE');

		// NOTE not currently implement user privileges
		
	}

	public function down()
	{
		$this->dropOETable('worklist_display_context');
		$this->dropOETable('worklist_attribute');
		$this->dropOETable('worklist', true);
		$this->dropOETable('worklist_type', true);
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