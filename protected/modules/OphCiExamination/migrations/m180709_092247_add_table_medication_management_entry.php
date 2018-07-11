<?php

class m180709_092247_add_table_medication_management_entry extends OEMigration
{
	public function up()
	{
        $this->dropOETable('ophciexamination_medication_management_entry', true);

	    $this->createOETable('ophciexamination_medication_management_entry', [
	        'id' => 'pk',
            'element_id' => 'int not null',
            'ref_medication_id' => 'int not null',
            'form_id' => 'INT NULL',
            'laterality' => 'INT NULL',
            'dose' => 'FLOAT NULL',
            'dose_unit_term' => 'VARCHAR(45) NULL',
            'route_id' => 'INT NULL',
            'frequency_id' => 'INT NULL',
            'duration' => 'INT NULL',
            'start_date' => 'VARCHAR(8) NOT NULL',
            'end_date' => 'VARCHAR(8) NULL',
            'stop_reason_id' => 'INT NULL',
            'stop' => 'boolean not null default 0',
            'prescribe' => 'boolean not null default 0',
        ], true);

	    $this->addForeignKey('fk_element_id', 'ophciexamination_medication_management_entry', 'element_id', 'et_ophciexamination_medicationmanagement', 'id');
	    $this->addForeignKey('fk_ref_medid', 'ophciexamination_medication_management_entry', 'ref_medication_id', 'ref_medication', 'id');
        $this->addForeignKey('fk_form_id', 'ophciexamination_medication_management_entry', 'form_id', 'ref_medication_form', 'id');
        $this->addForeignKey('fk_route_id', 'ophciexamination_medication_management_entry', 'route_id', 'ref_medication_route', 'id');
        $this->addForeignKey('fk_freq_id', 'ophciexamination_medication_management_entry', 'frequency_id', 'ref_medication_frequency', 'id');
	    $this->addForeignKey('fk_stop_reason', 'ophciexamination_medication_management_entry', 'stop_reason_id', 'ophciexamination_medication_stop_reason', 'id');
	}

	public function down()
	{
		$this->dropOETable('ophciexamination_medication_management_entry', true);
	}
}