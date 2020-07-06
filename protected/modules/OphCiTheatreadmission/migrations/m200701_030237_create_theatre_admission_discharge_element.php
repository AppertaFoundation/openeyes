<?php

class m200701_030237_create_theatre_admission_discharge_element extends OEMigration
{
    public function up()
    {
        // Creating Table
        $this->createOETable('et_ophcitheatreadmission_discharge', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophcitheatreadmission_dis_ev_fk',
            'et_ophcitheatreadmission_discharge',
            'event_id',
            'event',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('et_ophcitheatreadmission_discharge', true);
    }
}
