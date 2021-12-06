<?php

class m161020_142154_genetic_patient_update extends CDbMigration
{
    public function up()
    {
        $this->addColumn('genetics_patient', 'gender_id', 'int(10) unsigned');
        $this->addForeignKey('genetics_patient_gender', 'genetics_patient', 'gender_id', 'gender', 'id');

        $this->addColumn('genetics_patient', 'is_deceased', 'tinyint unsigned');
    }

    public function down()
    {
        $this->dropColumn('genetics_patient', 'is_deceased');

        $this->dropForeignKey('genetics_patient_gender', 'genetics_patient');
        $this->dropColumn('genetics_patient', 'gender_id');
    }
}
