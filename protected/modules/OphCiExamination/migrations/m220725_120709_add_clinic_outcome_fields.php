<?php

class m220725_120709_add_clinic_outcome_fields extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('ophciexamination_clinicoutcome_entry', 'context_id', 'int(10) unsigned default NULL AFTER status_id', true);
        $this->addOEColumn('ophciexamination_clinicoutcome_entry', 'service_id', 'int(10) unsigned default NULL AFTER status_id', true);
        $this->addOEColumn('ophciexamination_clinicoutcome_entry', 'site_id', 'int(10) unsigned default NULL AFTER status_id', true);

        $this->addForeignKey('clinicoutcome_entry_context_id_fk', 'ophciexamination_clinicoutcome_entry',
            'context_id', 'firm', 'id');
        $this->addForeignKey('clinicoutcome_entry_service_id_fk', 'ophciexamination_clinicoutcome_entry',
            'service_id', 'service', 'id');
        $this->addForeignKey('clinicoutcome_entry_site_id_fk', 'ophciexamination_clinicoutcome_entry',
            'site_id', 'site', 'id');
    }

    public function safeDown()
    {
        $this->dropOEColumn('ophciexamination_clinicoutcome_entry', 'context_id', true);
        $this->dropOEColumn('ophciexamination_clinicoutcome_entry', 'service_id', true);
        $this->dropOEColumn('ophciexamination_clinicoutcome_entry', 'site_id', true);
    }
}
