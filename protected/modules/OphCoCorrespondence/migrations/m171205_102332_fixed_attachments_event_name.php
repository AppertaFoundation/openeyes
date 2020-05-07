<?php

class m171205_102332_fixed_attachments_event_name extends CDbMigration
{
    public function up()
    {
        $this->update("ophcorrespondence_init_method", array('description' => 'Clinic Examination'), "short_code = 'LAST_EXAMINATION_IN_SS'");
        $this->update("ophcorrespondence_init_method", array('description' => 'Operation Note'), "short_code = 'LAST_OP_NOTE_IN_SS'");
        $this->update("ophcorrespondence_init_method", array('description' => 'Injection Event'), "short_code = 'LAST_INJECTION_IN_SS'");
        $this->update("ophcorrespondence_init_method", array('description' => 'Laser Event'), "short_code = 'LAST_LASER_IN_SS'");
        $this->update("ophcorrespondence_init_method", array('description' => 'Prescription Event'), "short_code = 'LAST_PRESCRIPTION_IN_SS'");
    }

    public function down()
    {
        $this->update("ophcorrespondence_init_method", array('description' => 'Last Prescription Event'), "short_code = 'LAST_PRESCRIPTION_IN_SS'");
        $this->update("ophcorrespondence_init_method", array('description' => 'Last Laser Event'), "short_code = 'LAST_LASER_IN_SS'");
        $this->update("ophcorrespondence_init_method", array('description' => 'Last Injection Event'), "short_code = 'LAST_INJECTION_IN_SS'");
        $this->update("ophcorrespondence_init_method", array('description' => 'Last Operation Note'), "short_code = 'LAST_OP_NOTE_IN_SS'");
        $this->update("ophcorrespondence_init_method", array('description' => 'Last Clinic Examination'), "short_code = 'LAST_EXAMINATION_IN_SS'");
    }

}
