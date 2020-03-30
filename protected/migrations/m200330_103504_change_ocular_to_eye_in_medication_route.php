<?php

class m200330_103504_change_ocular_to_eye_in_medication_route extends CDbMigration
{
    public function up()
    {
        $this->update('medication_route', array('term' => 'Eye'), "term = 'Ocular'");
    }

    public function down()
    {
        $this->update('medication_route', array('term' => 'Ocular'), "term = 'Eye'");
    }
}
