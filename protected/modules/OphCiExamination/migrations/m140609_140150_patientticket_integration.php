<?php

class m140609_140150_patientticket_integration extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_clinicoutcome_status', 'patientticket', 'boolean DEFAULT false');
        $this->alterColumn('ophciexamination_clinicoutcome_status', 'episode_status_id', 'int(10) unsigned');
        $this->addColumn('ophciexamination_clinicoutcome_status_version', 'patientticket', 'boolean DEFAULT false');
        $this->alterColumn('ophciexamination_clinicoutcome_status_version', 'episode_status_id', 'int(10) unsigned');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_clinicoutcome_status', 'patientticket');
        $this->dropColumn('ophciexamination_clinicoutcome_status_version', 'patientticket');
        $this->delete('ophciexamination_clinicoutcome_status', 'episode_status_id IS NULL');
        $this->delete('ophciexamination_clinicoutcome_status_version', 'episode_status_id IS NULL');
        $this->alterColumn('ophciexamination_clinicoutcome_status', 'episode_status_id', 'int(10) unsigned NOT NULL');
        $this->alterColumn('ophciexamination_clinicoutcome_status_version', 'episode_status_id', 'int(10) unsigned NOT NULL');
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
