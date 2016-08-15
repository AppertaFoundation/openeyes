<?php

class m160614_085916_patient_is_deceased_flag extends OEMigration
{
    public function up()
    {
        $this->addColumn('patient', 'is_deceased', 'tinyint not null default 0');
        $this->addColumn('patient_version', 'is_deceased', 'tinyint not null default 0');

        $this->update('patient', array('is_deceased' => 1), 'date_of_death IS NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('patient_version', 'is_deceased');
        $this->dropColumn('patient', 'is_deceased');
    }
}
