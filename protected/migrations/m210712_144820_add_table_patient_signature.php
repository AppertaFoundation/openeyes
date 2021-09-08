<?php

class m210712_144820_add_table_patient_signature extends OEMigration
{
    public function up()
    {
        $this->createOETable("et_patient_signature", [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED',
            'protected_file_id' => 'INT(10) UNSIGNED',
            'signature_date' => 'DATETIME NULL',
            'signatory_person' => 'TINYINT UNSIGNED',
            'signatory_name' => 'VARCHAR(255)',
            'signatory_required' => 'BOOLEAN NOT NULL DEFAULT 0'
        ], true);

        $this->addForeignKey("fk_et_patient_signature_event", "et_patient_signature", "event_id", "event", "id");
        $this->addForeignKey("fk_et_patient_signature_pf", "et_patient_signature", "protected_file_id", "protected_file", "id");
    }

    public function down()
    {
        $this->dropForeignKey("fk_et_patient_signature_event", "et_patient_signature");
        $this->dropForeignKey("fk_et_patient_signature_pf", "et_patient_signature");

        $this->dropOETable("et_patient_signature", true);
    }
}
