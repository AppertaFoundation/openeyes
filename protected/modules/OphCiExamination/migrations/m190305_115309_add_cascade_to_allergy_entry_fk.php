<?php

class m190305_115309_add_cascade_to_allergy_entry_fk extends CDbMigration
{
	public function safeUp()
	{
		$this->dropForeignKey('ophciexamination_allergy_entry_el_fk','ophciexamination_allergy_entry');
		$this->addForeignKey('ophciexamination_allergy_entry_el_fk','ophciexamination_allergy_entry', 'element_id', 'et_ophciexamination_allergies', 'id','CASCADE');
	}

	public function safeDown()
	{
		$this->dropForeignKey('ophciexamination_allergy_entry_el_fk','ophciexamination_allergy_entry');
		$this->addForeignKey('ophciexamination_allergy_entry_el_fk','ophciexamination_allergy_entry', 'element_id', 'et_ophciexamination_allergies', 'id');
	}
}