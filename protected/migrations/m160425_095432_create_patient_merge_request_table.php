<?php

class m160425_095432_create_patient_merge_request_table extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'patient_merge_request',
            array(
                        'id' => 'pk',

                        'primary_id' => 'int(10) unsigned NOT NULL',
                        'primary_hos_num' => 'varchar(40) NULL',
                        'primary_nhsnum' => 'varchar(40) NULL',
                        'primary_dob' => 'date NULL',
                        'primary_gender' => 'varchar(1) NULL',

                        'secondary_id' => 'int(10) unsigned NOT NULL',
                        'secondary_hos_num' => 'varchar(40) NULL',
                        'secondary_nhsnum' => 'varchar(40) NULL',
                        'secondary_dob' => 'date NULL',
                        'secondary_gender' => 'varchar(1) NULL',

                        'merge_json' => 'text',

                        'comment' => 'text',
                        'status' => 'smallint unsigned default 0',
                ),
            true
        );

        $this->addForeignKey('patient_merge_request_fk1', 'patient_merge_request', 'primary_id', 'patient', 'id');
        $this->addForeignKey('patient_merge_request_fk2', 'patient_merge_request', 'secondary_id', 'patient', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('patient_merge_request_fk1', 'patient_merge_request');
        $this->dropForeignKey('patient_merge_request_fk2', 'patient_merge_request');

        $this->dropOETable('patient_merge_request', true);
    }
}
