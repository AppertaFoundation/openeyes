<?php

class m140922_141630_patient_consent_checkbox extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophcotherapya_mrservicein', 'patient_sharedata_consent', 'tinyint(1) unsigned null');
        $this->addColumn('et_ophcotherapya_mrservicein_version', 'patient_sharedata_consent', 'tinyint(1) unsigned null');
    }

    public function down()
    {
        $this->dropColumn('et_ophcotherapya_mrservicein', 'patient_sharedata_consent');
        $this->dropColumn('et_ophcotherapya_mrservicein_version', 'patient_sharedata_consent');
    }
}
