<?php

class m220107_091100_add_signature_to_patient_attorney_deputy extends OEMigration
{
    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable('ophtrconsent_patient_attorney_deputy_contact', true)) {
            $this->addOEColumn('ophtrconsent_patient_attorney_deputy_contact', 'signature_id', 'INT(11) NULL', true);
            $this->addForeignKey('fk_et_ophtrc_patient_attorney_deputy_contact', 'ophtrconsent_patient_attorney_deputy_contact', 'signature_id', 'ophtrconsent_signature', 'id');
        }
    }

    public function safeDown()
    {
        if ($this->dbConnection->schema->getTable('ophtrconsent_patient_attorney_deputy_contact', true)) {
            $this->dropForeignKey('fk_et_ophtrc_patient_attorney_deputy_contact', 'ophtrconsent_patient_attorney_deputy_contact');
            $this->dropOEColumn('ophtrconsent_patient_attorney_deputy_contact', 'signature_id', true);
        }
    }
}
