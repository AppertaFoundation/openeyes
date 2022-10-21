<?php

class m221012_105347_rename_service_with_subspecialty_clinic_outcome extends OEMigration
{
    public function safeUp()
    {
        $this->renameOEColumn('ophciexamination_clinicoutcome_entry', 'service_id', 'subspecialty_id', true);
        $this->dropForeignKey('clinicoutcome_entry_service_id_fk', 'ophciexamination_clinicoutcome_entry');
        $this->addForeignKey('clinicoutcome_entry_subspecialty_id_fk', 'ophciexamination_clinicoutcome_entry',
            'subspecialty_id', 'subspecialty', 'id');
    }

    public function safeDown()
    {
        $this->renameOEColumn('ophciexamination_clinicoutcome_entry', 'subspecialty_id', 'service_id', true);
        $this->addForeignKey('clinicoutcome_entry_service_id_fk', 'ophciexamination_clinicoutcome_entry',
            'service_id', 'service', 'id');
        $this->dropForeignKey('clinicoutcome_entry_subspecialty_id_fk', 'ophciexamination_clinicoutcome_entry');
	}
}
