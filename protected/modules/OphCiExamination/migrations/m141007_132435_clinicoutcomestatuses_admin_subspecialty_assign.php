<?php

class m141007_132435_clinicoutcomestatuses_admin_subspecialty_assign extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophciexamination_clinicoutcome_status_options', array(
                    'id' => 'pk',
                    'clinicoutcome_status_id' => 'int(10) unsigned NOT NULL',
                    'subspecialty_id' => 'int(10) unsigned NOT NULL',
                ), true);

        $this->addForeignKey(
            'ophciexamination_clinicoutcome_status_options_ciid_fk',
            'ophciexamination_clinicoutcome_status_options',
            'clinicoutcome_status_id',
            'ophciexamination_clinicoutcome_status',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_clinicoutcome_status_options_ssid_fk',
            'ophciexamination_clinicoutcome_status_options',
            'subspecialty_id',
            'subspecialty',
            'id'
        );

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);
    }

    public function down()
    {
        $this->dropOETable('ophciexamination_clinicoutcome_status_options', true);
        $this->delete('ophciexamination_clinicoutcome_status', 'name = ? or name = ?', array('Refer to VC', 'Suitable for stable monitoring clinic'));
    }
}
