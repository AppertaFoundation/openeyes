<?php

class m220301_173000_set_red_flag_default_institution_assignment extends OEMigration
{
    public function safeUp()
    {
        $this->execute("INSERT INTO ophciexamination_ae_red_flags_option_institution (ophciexamination_ae_red_flags_option_id, institution_id)
                        SELECT id, (SELECT id FROM institution WHERE remote_id = :remote_id)
                        FROM ophciexamination_ae_red_flags_option", [':remote_id' => Yii::app()->params['institution_code']]);
    }
}
