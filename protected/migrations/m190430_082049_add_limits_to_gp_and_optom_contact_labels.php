<?php

class m190430_082049_add_limits_to_gp_and_optom_contact_labels extends CDbMigration
{
    public function up()
    {
        $this->update('contact_label', ['max_number_per_patient' => 1],
            'name = :gp_name OR name = :optom_name',
            [
                ':gp_name' => 'General Practitioner',
                ':optom_name' => 'Optometrist'
            ]
        );
    }

    public function down()
    {
        $this->update('contact_label', ['max_number_per_patient' => null],
            'name = :gp_name OR name = :optom_name',
            [
                ':gp_name' => 'General Practitioner',
                ':optom_name' => 'Optometrist'
            ]
        );
    }
}