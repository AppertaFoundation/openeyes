<?php

class m190201_132020_medication_attributes extends OEMigration
{
	public function safeUp()
	{
	    $this->createOETable("medication_attribute", array(
	        'id' => 'pk',
            'name' => 'VARCHAR(64)',
        ), true);

        /* A drug can have more than 1 of the same attribute (with different data) */
	    $this->createIndex('idx_med_attr_name', 'medication_attribute', 'name', false);

	    $this->createOETable('medication_attribute_assignment', array(
            'id' => 'pk',
            'medication_id' => 'INT(11)',
            'medication_attribute_id' => 'INT(11)',
            'value' => 'VARCHAR(64)',
            'description' => 'VARCHAR(256) NULL'
        ), true);

	    $this->addForeignKey('fk_med_attr_med_id', 'medication_attribute_assignment', 'medication_id', 'medication', 'id', 'CASCADE');
	    $this->addForeignKey('fk_med_attr_attr_id', 'medication_attribute_assignment', 'medication_attribute_id', 'medication_attribute', 'id', 'CASCADE');
	}

	public function safeDown()
	{
	    $this->dropForeignKey('fk_med_attr_med_id', 'medication_attribute_assignment');
	    $this->dropForeignKey('fk_med_attr_attr_id', 'medication_attribute_assignment');
		$this->dropOETable('medication_attribute_assignment', true);
		$this->dropOETable('medication_attribute', true);
	}
}