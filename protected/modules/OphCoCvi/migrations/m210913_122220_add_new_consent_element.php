<?php

class m210913_122220_add_new_consent_element extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable("et_ophcocvi_consent", [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED',
            "consented_to_gp" => "BOOLEAN NOT NULL",
            "consented_to_la" => "BOOLEAN NOT NULL",
            "consented_to_rcop" => "BOOLEAN NOT NULL"
        ], true);

        $this->createElementType("OphCoCvi", "Consent", [
            'class_name' => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Consent',
            'default' => true,
            'required' => true,
            'display_order' => 20
        ]);

        $this->migrateData();
    }

    public function migrateData()
    {
        $query = <<<EOT
INSERT INTO et_ophcocvi_consent (event_id, consented_to_gp, consented_to_la, consented_to_rcop)
SELECT event_id, consented_to_gp, consented_to_la, consented_to_rcop
FROM `et_ophcocvi_patient_signature`
EOT;
        $this->execute($query);
    }

    public function safeDown()
    {
        $this->dropTable('et_ophcocvi_consent');
        $this->deleteElementType('OphCoCvi','OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Consent');
    }
}
