<?php

class m170821_080234_history_meds_prescription_link extends OEMigration
{
	public function up()
	{
	    $this->addColumn('ophciexamination_history_medications_entry',
            'prescription_item_id',
            'int(10) unsigned');
        $this->addColumn('ophciexamination_history_medications_entry_version',
            'prescription_item_id',
            'int(10) unsigned');
        $this->addForeignKey('ophciexamination_history_meds_entry_prescr_fk',
            'ophciexamination_history_medications_entry', 'prescription_item_id', 'ophdrprescription_item', 'id');
	}

	public function down()
	{
		$this->dropColumn('ophciexamination_history_medications_entry_version', 'prescription_item_id');
        $this->dropColumn('ophciexamination_history_medications_entry', 'prescription_item_id');
	}
}