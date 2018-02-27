<?php

class m131002_122530_add_diabetic_diagnosis_drgrading extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_drgrading', 'secondarydiagnosis_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_drgrading', 'secondarydiagnosis_disorder_id', 'BIGINT unsigned');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_drgrading', 'secondarydiagnosis_disorder_id');
        $this->dropColumn('et_ophciexamination_drgrading', 'secondarydiagnosis_id');
    }
}
