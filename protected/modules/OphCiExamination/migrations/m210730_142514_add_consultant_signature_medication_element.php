<?php

class m210730_142514_add_consultant_signature_medication_element extends OEMigration
{
    private const ITEM_TBL = 'ophciexamination_signature';
    private const MED_MANAGEMENT_ELEMENT_TBL = 'et_ophciexamination_medicationmanagement';

    public function safeUp()
    {
        $this->createOETable(
            self::ITEM_TBL,
            [
                'id' => 'pk',
                'element_id' => 'INT(11) NOT NULL',
                'type' => 'TINYINT UNSIGNED',
                'signature_file_id' => 'INT(10) UNSIGNED NULL',
                'signed_user_id' => 'INT(10) UNSIGNED NOT NULL',
                'signatory_role' => 'VARCHAR(64) NOT NULL',
                'signatory_name' => 'VARCHAR(64) NOT NULL',
                'timestamp' => 'INT(11) NOT NULL',
            ],
            true
        );

        $this->addForeignKey('fk_ophciexamination_signature_etid', self::ITEM_TBL, 'element_id', self::MED_MANAGEMENT_ELEMENT_TBL, 'id');
        $this->addForeignKey('fk_ophciexamination_signature_sfile_id', self::ITEM_TBL, 'signature_file_id', 'protected_file', 'id');
        $this->addForeignKey('fk_ophciexamination_signature_suser_id', self::ITEM_TBL, 'signed_user_id', 'user', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_ophciexamination_signature_etid', self::ITEM_TBL);
        $this->dropForeignKey('fk_ophciexamination_signature_sfile_id', self::ITEM_TBL);
        $this->dropForeignKey('fk_ophciexamination_signature_suser_id', self::ITEM_TBL);
        $this->dropOETable(self::ITEM_TBL, true);
    }
}
