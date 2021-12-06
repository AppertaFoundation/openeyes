<?php

/**
 * migration to create a new table for extra procedures
 *
 */

class m210827_044456_create_table_ophtrconsent_extra_procedure extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable(
            'ophtrconsent_procedure_extra',
            array(
                'id' => 'pk',
                'term' => 'varchar(255) NOT NULL',
                'short_format' => 'varchar(255) NOT NULL',
                'default_duration' =>  'smallint(5) unsigned NOT NULL',
                'snomed_code' => 'varchar(20) NOT NULL',
                'snomed_term' =>  'varchar(255) NOT NULL DEFAULT "0" ',
                'aliases' =>  'varchar(255) NOT NULL DEFAULT "none" ',
                'unbooked' =>  ' tinyint(1) unsigned NOT NULL DEFAULT "0" ',
                'active' =>  'tinyint(1) NOT NULL DEFAULT "1" ',
                'proc_id'  => 'int(10) unsigned NULL ',
                'institution_id' => 'int(10) unsigned NULL',

                'CONSTRAINT ophtrconsent_procedure_extra_ev_fk FOREIGN KEY (proc_id) REFERENCES proc (id)',
                'CONSTRAINT ophtrconsent_procedure_extra_institution_id_fk FOREIGN KEY (institution_id) REFERENCES institution (id)',

            ),
            true
        );
    }

    public function safeDown()
    {
        $this->dropOETable('ophtrconsent_procedure_extra', true);

        return true;
    }
}
