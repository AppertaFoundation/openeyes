<?php

class m190919_042824_delete_started_date_from_trial_patient extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('trial_patient', 'started_date');
        $this->dropColumn('trial_patient_version', 'started_date');
    }

    public function down()
    {
        $this->addColumn('trial_patient', 'started_date', 'datetime not null');
        $this->addColumn('trial_patient_version', 'started_date', 'datetime not null');
    }
}
