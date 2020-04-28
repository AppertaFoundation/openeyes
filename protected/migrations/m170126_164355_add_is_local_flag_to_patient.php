<?php

class m170126_164355_add_is_local_flag_to_patient extends CDbMigration
{
    public function up()
    {
            $this->addColumn('patient', 'is_local', 'tinyint(1) unsigned');
            $this->addColumn('patient_version', 'is_local', 'tinyint(1) unsigned');
    }

    public function down()
    {
            $this->dropColumn('patient', 'is_local');
            $this->dropColumn('patient_version', 'is_local');
    }
}
