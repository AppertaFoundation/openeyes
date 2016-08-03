<?php

class m140108_154140_default_arrival_time_for_sequences_and_sessions extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophtroperationbooking_operation_sequence', 'default_admission_time', 'time not null');
        $this->addColumn('ophtroperationbooking_operation_session', 'default_admission_time', 'time not null');

        foreach ($this->dbConnection->createCommand()->select('distinct(start_time) as start_time')->from('ophtroperationbooking_operation_sequence')->queryAll() as $sequence) {
            if (preg_match('/^13:30/', $sequence['start_time'])) {
                $time = '12:00:00';
            } else {
                $time = date('H:i:s', strtotime(date('Y-m-d '.$sequence['start_time'])) - 3600);
            }

            $this->update('ophtroperationbooking_operation_sequence', array('default_admission_time' => $time), "start_time = '{$sequence['start_time']}'");
        }

        foreach ($this->dbConnection->createCommand()->select('distinct(start_time) as start_time')->from('ophtroperationbooking_operation_session')->queryAll() as $session) {
            if (preg_match('/^13:30/', $session['start_time'])) {
                $time = '12:00:00';
            } else {
                $time = date('H:i:s', strtotime(date('Y-m-d '.$session['start_time'])) - 3600);
            }

            $this->update('ophtroperationbooking_operation_session', array('default_admission_time' => $time), "start_time = '{$session['start_time']}'");
        }
    }

    public function down()
    {
        $this->dropColumn('ophtroperationbooking_operation_sequence', 'default_admission_time');
        $this->dropColumn('ophtroperationbooking_operation_session', 'default_admission_time');
    }
}
