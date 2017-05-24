<?php

class m131115_091259_patient_pedigree_comments extends CDbMigration
{
    public function up()
    {
        $this->addColumn('patient_pedigree', 'comments', 'varchar(2048) collate utf8_bin not null');
    }

    public function down()
    {
        $this->dropColumn('patient_pedigree', 'comments');
    }
}
