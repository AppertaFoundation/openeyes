<?php

class m190321_141548_rename_pas_visit_id_in_event extends OEMigration
{

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	    $this->renameColumn('event', 'pas_visit_id', 'worklist_patient_id');
	    $this->renameColumn('event_version', 'pas_visit_id', 'worklist_patient_id');
	}

	public function safeDown()
	{
        $this->renameColumn('event', 'worklist_patient_id', 'pas_visit_id');
        $this->renameColumn('event_version', 'worklist_patient_id', 'pas_visit_id');
	}
}