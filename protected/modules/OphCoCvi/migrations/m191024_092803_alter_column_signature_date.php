<?php

class m191024_092803_alter_column_signature_date extends CDbMigration
{
    public function up()
    {
        $this->alterColumn("et_ophcocvi_patient_signature", "signature_date", "DATETIME NULL");
        $this->alterColumn("et_ophcocvi_patient_signature_version", "signature_date", "DATETIME NULL");
    }

    public function down()
    {
        $this->alterColumn("et_ophcocvi_patient_signature", "signature_date", "DATE NULL");
        $this->alterColumn("et_ophcocvi_patient_signature_version", "signature_date", "DATE NULL");
    }
}
