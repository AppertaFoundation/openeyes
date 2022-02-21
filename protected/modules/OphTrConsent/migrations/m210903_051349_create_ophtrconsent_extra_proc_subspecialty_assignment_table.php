<?php

class m210903_051349_create_ophtrconsent_extra_proc_subspecialty_assignment_table extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophtrconsent_extra_proc_subspecialty_assignment',
            array(
                'id' => 'pk',
                'extra_proc_id' => 'int(11) NULL NOT NULL',
                'subspecialty_id' => 'int(10) unsigned NULL',
                'institution_id' => 'int(10) unsigned NULL',
                'display_order' => 'int(8) NOT NULL',
                'CONSTRAINT ophtrconsent_extra_proc_subspecialty_assignment_extra_fk FOREIGN KEY (extra_proc_id) REFERENCES ophtrconsent_procedure_extra (id)',
                'CONSTRAINT ophtrconsent_extra_proc_subspecialty_assignment_subs_fk FOREIGN KEY (subspecialty_id) REFERENCES subspecialty (id)',
                'CONSTRAINT ophtrconsent_extra_proc_subspecialty_assignment_institution_fk FOREIGN KEY (institution_id) REFERENCES institution (id)',
            ),
            true
        );
        $this->alterOEColumn('ophtrconsent_procedure_extra', 'snomed_term', 'varchar(255) DEFAULT NULL');
        $this->alterOEColumn('ophtrconsent_procedure_extra', 'snomed_code', 'varchar(20) DEFAULT NULL');
        $this->alterOEColumn('ophtrconsent_procedure_extra', 'aliases', 'varchar(255) DEFAULT NULL');
        $this->dropForeignKey('ophtrconsent_procedure_extra_ev_fk', 'ophtrconsent_procedure_extra');
        $this->dropForeignKey('ophtrconsent_procedure_extra_institution_id_fk', 'ophtrconsent_procedure_extra');
        $this->dropOEColumn('ophtrconsent_procedure_extra', 'default_duration', true);
        $this->dropOEColumn('ophtrconsent_procedure_extra', 'unbooked', true);
        $this->dropOEColumn('ophtrconsent_procedure_extra', 'active', true);
        $this->dropOEColumn('ophtrconsent_procedure_extra', 'proc_id', true);
        $this->dropOEColumn('ophtrconsent_procedure_extra', 'institution_id', true);
    }

    public function down()
    {
        echo "m210903_051349_create_ophtrconsent_extra_proc_subspecialty_assignment_table does not support migration down.\n";
        return false;
    }
}
