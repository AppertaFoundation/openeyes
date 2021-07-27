<?php

class m210722_095414_add_consultant_signature_element extends OEMigration
{
    private const ELEMENT_TBL = 'et_ophcocorrespondence_esign';
    private const ITEM_TBL = 'ophcocorrespondence_signature';

	public function safeUp()
	{
        $this->createOETable(
            self::ELEMENT_TBL,
            [
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED NOT NULL',
                'signature_file_id' => 'INT(10) UNSIGNED NULL',
                'date_signed' => 'DATETIME NULL',
                'signed_user_id' => 'INT(10) UNSIGNED NULL'
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
                'signed_user_id' => 'INT(10) UNSIGNED NOT NULL',
                'signatory_role' => 'VARCHAR(64) NOT NULL',
                'signatory_name' => 'VARCHAR(64) NOT NULL',
                'timestamp' => 'INT(11) NOT NULL',
            ],
            true
        );

        $this->addForeignKey('fk_ophcocorrespondence_signature_etid', self::ITEM_TBL, 'element_id', self::ELEMENT_TBL, 'id');
        $this->addForeignKey('fk_et_ophcocorrespondence_esign_event_id', self::ELEMENT_TBL, 'event_id', 'event', 'id');
        $this->addForeignKey('fk_ophcocorrespondence_signature_sfile_id', self::ITEM_TBL, 'signature_file_id', 'protected_file', 'id');
        $this->addForeignKey('fk_ophcocorrespondence_signature_suser_id', self::ITEM_TBL, 'signed_user_id', 'user', 'id');

        $this->createElementType('OphCoCorrespondence', 'E-Sign', array(
            'class_name' => 'Element_OphCoCorrespondence_Esign',
            'display_order' => 20,
            'parent_class' => null,
            "default" => 1,
            "required" => 1,
        ));
	}

	public function safeDown()
	{
	    $this->deleteElementType('OphCoCorrespondence', 'Element_OphCoCorrespondence_Esign');
        $this->dropForeignKey('fk_ophcocorrespondence_signature_etid', self::ITEM_TBL);
        $this->dropForeignKey('fk_et_ophcocorrespondence_esign_event_id', self::ELEMENT_TBL);
        $this->dropForeignKey('fk_ophcocorrespondence_signature_sfile_id', self::ITEM_TBL);
        $this->dropForeignKey('fk_ophcocorrespondence_signature_suser_id', self::ITEM_TBL);
        $this->dropOETable(self::ITEM_TBL, true);
	    $this->dropOETable(self::ELEMENT_TBL, true);
	}
}