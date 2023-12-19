<?php

class m231124_083728_signature_length extends OEMigration
{
    public function safeUp()
    {
        $this->alterOEColumn('signature_request', 'signatory_name', 'VARCHAR(128)', true);
        $this->alterOEColumn('ophcocvi_signature', 'signatory_name', 'VARCHAR(128)', true);
        $this->alterOEColumn('ophdrprescription_signature', 'signatory_name', 'VARCHAR(128)', true);
        $this->alterOEColumn('ophtrconsent_signature', 'signatory_name', 'VARCHAR(128)', true);
        $this->alterOEColumn('ophciexamination_signature', 'signatory_name', 'VARCHAR(128)', true);
        $this->alterOEColumn('ophcocorrespondence_signature', 'signatory_name', 'VARCHAR(128)', true);
    }

    public function safeDown()
    {
        $this->alterOEColumn('signature_request', 'signatory_name', 'VARCHAR(64)', true);
        $this->alterOEColumn('ophcocvi_signature', 'signatory_name', 'VARCHAR(64)', true);
        $this->alterOEColumn('ophdrprescription_signature', 'signatory_name', 'VARCHAR(64)', true);
        $this->alterOEColumn('ophtrconsent_signature', 'signatory_name', 'VARCHAR(64)', true);
        $this->alterOEColumn('ophciexamination_signature', 'signatory_name', 'VARCHAR(64)', true);
        $this->alterOEColumn('ophcocorrespondence_signature', 'signatory_name', 'VARCHAR(64)', true);
    }
}
