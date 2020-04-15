<?php

class m190822_021939_add_practice_id_to_patient_contact_associate_table extends CDbMigration
{
    public function up()
    {
        $this->addColumn('patient_contact_associate' , 'practice_id' , 'int(10) unsigned NOT NULL AFTER gp_id');
        $this->addColumn('patient_contact_associate_version' , 'practice_id' , 'int(10) unsigned NOT NULL AFTER gp_id');
        $this->addForeignKey('patient_contact_associate_practice_fk', 'patient_contact_associate', 'practice_id', 'practice', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('patient_contact_associate_practice_fk', 'patient_contact_associate');
        $this->dropColumn('patient_contact_associate', 'practice_id');
        $this->dropColumn('patient_contact_associate_version', 'practice_id');
    }
}
