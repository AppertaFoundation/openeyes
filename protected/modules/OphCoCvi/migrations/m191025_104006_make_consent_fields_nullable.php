<?php

class m191025_104006_make_consent_fields_nullable extends CDbMigration
{
    public function up()
    {
        $this->alterColumn("et_ophcocvi_patient_signature", "consented_to_gp", "BOOLEAN NULL DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_patient_signature_version", "consented_to_gp", "BOOLEAN NULL DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_patient_signature", "consented_to_la", "BOOLEAN NULL  DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_patient_signature_version", "consented_to_la", "BOOLEAN NULL DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_patient_signature", "consented_to_rcop", "BOOLEAN NULL DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_patient_signature_version", "consented_to_rcop", "BOOLEAN NULL DEFAULT NULL");
    }

    public function down()
    {
        $this->alterColumn("et_ophcocvi_patient_signature", "consented_to_gp", "BOOLEAN NOT NULL");
        $this->alterColumn("et_ophcocvi_patient_signature_version", "consented_to_gp", "BOOLEAN NOT NULL");
        $this->alterColumn("et_ophcocvi_patient_signature", "consented_to_la", "BOOLEAN NOT NULL");
        $this->alterColumn("et_ophcocvi_patient_signature_version", "consented_to_la", "BOOLEAN NOT NULL");
        $this->alterColumn("et_ophcocvi_patient_signature", "consented_to_rcop", "BOOLEAN NOT NULL");
        $this->alterColumn("et_ophcocvi_patient_signature_version", "consented_to_rcop", "BOOLEAN NOT NULL");
    }
}
