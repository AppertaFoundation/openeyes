<?php

class m190201_132020_medication_attributes extends OEMigration
{
	public function safeUp()
	{
	    $this->createOETable("medication_attribute", array(
	        'id' => 'pk',
            'name' => 'VARCHAR(64)',
        ), true);

	    $this->createIndex('idx_med_attr_name', 'medication_attribute', 'name', true);

	    $this->createOETable("medication_attribute_option", array(
	        'id' => 'pk',
            'medication_attribute_id' => 'INT NOT NULL',
            'value' => 'VARCHAR(64)',
            'description' => 'VARCHAR(256) NULL'
        ), true);

	    $this->createIndex('med_attr_opt_med_att_idx', 'medication_attribute_option', 'medication_attribute_id');
	    $this->createIndex('med_attr_opt_value_idx', 'medication_attribute_option', 'value');

        $this->addForeignKey('fk_med_attr_opt_attr_id', 'medication_attribute_option', 'medication_attribute_id', 'medication_attribute', 'id', 'CASCADE');

	    $this->createOETable('medication_attribute_assignment', array(
            'id' => 'pk',
            'medication_id' => 'INT(11)',
            'medication_attribute_option_id' => 'INT(11)',
        ), true);

	    $this->addForeignKey('fk_med_attr_med_id', 'medication_attribute_assignment', 'medication_id', 'medication', 'id', 'CASCADE');
	    $this->addForeignKey('fk_med_attr_opt_id', 'medication_attribute_assignment', 'medication_attribute_option_id', 'medication_attribute_option', 'id', 'RESTRICT');
	}

	public function safeDown()
	{
        $this->dropForeignKey('fk_med_attr_med_id', 'medication_attribute_assignment');
        $this->dropForeignKey('fk_med_attr_opt_id', 'medication_attribute_assignment');
        $this->dropOETable('medication_attribute_assignment', true);

        $this->dropForeignKey('fk_med_attr_opt_attr_id', 'medication_attribute_option');
	    $this->dropOETable('medication_attribute_option', true);

		$this->dropOETable('medication_attribute', true);
	}
}