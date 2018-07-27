<?php

class m180720_062726_create_table_tags_ophciexamination_medication_management_tags extends OEMigration
{
	public function up()
	{
	    $this->createOETable('ophciexamination_medication_management_tags', [
	        'id' => 'pk',
            'tag_id' => 'INT'
        ], true);

	    $this->addForeignKey('fk_tag_id', 'ophciexamination_medication_management_tags', 'tag_id', 'drug_tag', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('fk_tag_id', 'ophciexamination_medication_management_tags');
		$this->dropOETable('ophciexamination_medication_management_tags', true);
	}
}