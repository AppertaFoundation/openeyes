<?php

class m140828_140728_genetics_patient_comments extends OEMigration
{
    public function up()
    {
        $this->dropColumn('patient_pedigree', 'comments');

        $this->createOETable(
            'genetics_patient',
            array(
                'id' => 'pk',
                'patient_id' => 'int(11) unsigned unique not null',
                'comments' => 'text',
                'constraint genetics_patient_id_fk foreign key (patient_id) references patient (id)',
            ),
            true
        );
    }

    public function down()
    {
        $this->dropTable('genetics_patient');
        $this->addColumn('patient_pedigree', 'comments', 'varchar(2048) collate utf8_bin not null');
    }
}
