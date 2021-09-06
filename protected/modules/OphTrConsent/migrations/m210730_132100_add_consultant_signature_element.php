<?php

class m210730_132100_add_consultant_signature_element extends OEMigration
{
    private const ELEMENT_TBL = 'et_ophtrconsent_esign';
    private const ITEM_TBL = 'ophtrconsent_signature';

    public function safeUp()
    {
        $this->createOETable(
            self::ELEMENT_TBL,
            [
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED NOT NULL',
                'healthprof_signature_id' => 'INT(11) NULL'
            ],
            true
        );

        $this->createOETable(
            self::ITEM_TBL,
            [
                'id' => 'pk',
                'element_id' => 'INT(11) NOT NULL',
                'type' => 'TINYINT UNSIGNED',
                'signature_file_id' => 'INT(10) UNSIGNED NULL',
                'signed_user_id' => 'INT(10) UNSIGNED NULL',
                'signatory_role' => 'VARCHAR(64) NOT NULL',
                'signatory_name' => 'VARCHAR(64) NOT NULL',
                'timestamp' => 'INT(11) NOT NULL',
                'initiator_element_type_id' => 'INT(10) UNSIGNED',
                'initiator_row_id' => 'INT(10) UNSIGNED NULL',
            ],
            true
        );

        $this->addForeignKey('fk_ophtrconsent_signature_etid', self::ITEM_TBL, 'element_id', self::ELEMENT_TBL, 'id');
        $this->addForeignKey('fk_et_ophtrconsent_esign_event_id', self::ELEMENT_TBL, 'event_id', 'event', 'id');
        $this->addForeignKey('fk_et_ophtrconsent_hsign_id', self::ELEMENT_TBL, 'healthprof_signature_id', self::ITEM_TBL, 'id');
        $this->addForeignKey('fk_ophtrconsent_signature_sfile_id', self::ITEM_TBL, 'signature_file_id', 'protected_file', 'id');
        $this->addForeignKey('fk_ophtrconsent_signature_suser_id', self::ITEM_TBL, 'signed_user_id', 'user', 'id');
        $this->addForeignKey('fk_ophtrconsent_signature_ietype_id', self::ITEM_TBL, 'initiator_element_type_id', 'element_type', 'id');

        $this->createElementType('OphTrConsent', 'E-Sign', array(
            'class_name' => 'Element_OphTrConsent_Esign',
            'display_order' => 0,
            'parent_class' => null,
            "default" => 1,
            "required" => 1,
        ));
    }

    public function safeDown()
    {
        $this->deleteElementType('OphTrConsent', 'Element_OphTrConsent_Esign');
        $this->dropForeignKey('fk_ophtrconsent_signature_etid', self::ITEM_TBL);
        $this->dropForeignKey('fk_et_ophtrconsent_esign_event_id', self::ELEMENT_TBL);
        $this->dropForeignKey('fk_et_ophtrconsent_hsign_id', self::ELEMENT_TBL);
        $this->dropForeignKey('fk_ophtrconsent_signature_sfile_id', self::ITEM_TBL);
        $this->dropForeignKey('fk_ophtrconsent_signature_suser_id', self::ITEM_TBL);
        $this->dropOETable(self::ITEM_TBL, true);
        $this->dropOETable(self::ELEMENT_TBL, true);
    }
}
