<?php

class m190916_013331_add_started_date_to_trial_patient extends CDbMigration
{
    public function up()
    {
        $this->addColumn('trial_patient', 'started_date', 'datetime not null');
        $this->addColumn('trial_patient_version', 'started_date', 'datetime not null');

    }

    public function down()
    {
        $this->dropColumn('trial_patient', 'started_date');
        $this->dropColumn('trial_patient_version', 'started_date');
    }
}
