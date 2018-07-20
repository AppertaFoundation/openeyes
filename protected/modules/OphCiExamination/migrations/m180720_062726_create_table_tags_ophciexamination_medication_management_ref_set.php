<?php

class m180720_062726_create_table_tags_ophciexamination_medication_management_ref_set extends OEMigration
{
	public function up()
	{
	    $this->createOETable('ophciexamination_medication_management_ref_set', [
	        'id' => 'pk',
            'ref_set_id' => 'INT'
        ], true);

	    $this->addForeignKey('fk_ophci_ex_mm_refset', 'ophciexamination_medication_management_ref_set', 'ref_set_id', 'ref_set', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('fk_ref_set_id', 'ophciexamination_medication_management_ref_set');
		$this->dropOETable('ophciexamination_medication_management_ref_set', true);
	}
}