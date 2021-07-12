<?php

class m210712_163020_add_generic_consultant_signature_element extends OEMigration
{
    public function up()
    {
        $this->createOETable("et_consultant_signature", [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED',
            'protected_file_id' => 'INT(10) UNSIGNED',
            'signed_by_user_id' => 'INT(10) UNSIGNED',
            'signature_date' => 'DATETIME NULL'
        ], true);

        $this->addForeignKey("fk_et_consultant_signature_event", "et_consultant_signature", "event_id", "event", "id");
        $this->addForeignKey("fk_et_consultant_signature_pf", "et_consultant_signature", "protected_file_id", "protected_file", "id");
        $this->addForeignKey("fk_et_consultant_signature_sb", "et_consultant_signature", "signed_by_user_id", "user", "id");
    }

    public function down()
    {
        $this->dropForeignKey("fk_et_consultant_signature_event", "et_consultant_signature");
        $this->dropForeignKey("fk_et_consultant_signature_pf", "et_consultant_signature");
        $this->dropForeignKey("fk_et_consultant_signature_sb", "et_consultant_signature");

        $this->dropOETable("et_consultant_signature", true);
    }
}