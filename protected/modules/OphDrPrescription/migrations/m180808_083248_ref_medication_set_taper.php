<?php

class m180808_083248_ref_medication_set_taper extends OEMigration
{
	public function up()
	{
	    $this->createOETable('ref_medication_set_taper', array(
	        'id' => 'pk',
            'ref_medication_set_id' => 'int(11) NOT NULL',
            'dose' => 'FLOAT',
            'frequency_id' => 'INT(11) NOT NULL',
            'duration_id' => 'INT(10) UNSIGNED NOT NULL'
        ), true);

	    $this->addForeignKey('fk_rmst_med_id', 'ref_medication_set_taper', 'ref_medication_set_id', 'ref_medication_set', 'id');
	    $this->addForeignKey('fk_rmst_freq_id', 'ref_medication_set_taper', 'frequency_id', 'ref_medication_frequency', 'id');
	    $this->addForeignKey('fk_rmst_duration_id', 'ref_medication_set_taper', 'duration_id', 'drug_duration', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('fk_rmst_med_id', 'ref_medication_set_taper');
		$this->dropForeignKey('fk_rmst_freq_id', 'ref_medication_set_taper');
		$this->dropForeignKey('fk_rmst_duration_id', 'ref_medication_set_taper');
		$this->dropOETable('ref_medication_set_taper', true);
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