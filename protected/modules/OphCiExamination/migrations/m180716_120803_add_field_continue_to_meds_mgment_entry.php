<?php

class m180716_120803_add_field_continue_to_meds_mgment_entry extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('ophciexamination_medication_management_entry', 'continue', 'boolean not null default 0');
	    $this->addColumn('ophciexamination_medication_management_entry_version', 'continue', 'boolean not null default 0');
	}

	public function down()
	{
        $this->dropColumn('ophciexamination_medication_management_entry', 'continue');
        $this->dropColumn('ophciexamination_medication_management_entry_version', 'continue');
	}
}