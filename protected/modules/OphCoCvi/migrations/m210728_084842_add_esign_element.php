<?php

class m210728_084842_add_esign_element extends OEMigration
{
    private const ELEMENT_TBL = 'et_ophcocvi_esign';
    private const ITEM_TBL = 'ophcocvi_signature_entry';
    private const RETIRED_ET_CONSENT_CLASS = 'OEModule\OphCoCvi\models\Element_OphCoCvi_ConsentSignature';
    private const RETIRED_ET_CONSULTANT_CLASS = 'OEModule\OphCoCvi\models\Element_OphCoCvi_ConsultantSignature';

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
                'signed_user_id' => 'INT(10) UNSIGNED NULL',
                'signatory_role' => 'VARCHAR(64) NOT NULL',
                'signatory_name' => 'VARCHAR(64) NOT NULL',
                'timestamp' => 'INT(11) NOT NULL',
            ],
            true
        );

        $this->addForeignKey('fk_ophcocvi_signature_etid', self::ITEM_TBL, 'element_id', self::ELEMENT_TBL, 'id');
        $this->addForeignKey('fk_et_ophcocvi_esign_event_id', self::ELEMENT_TBL, 'event_id', 'event', 'id');
        $this->addForeignKey('fk_ophcocvi_signature_sfile_id', self::ITEM_TBL, 'signature_file_id', 'protected_file', 'id');
        $this->addForeignKey('fk_ophcocvi_signature_suser_id', self::ITEM_TBL, 'signed_user_id', 'user', 'id');

        $this->createElementType('OphCoCvi', 'E-Sign', array(
            'class_name' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_Esign',
            'display_order' => 60,
            'parent_class' => null,
            "default" => 1,
            "required" => 1,
        ));

        $this->deleteElementType('OphCoCvi', self::RETIRED_ET_CONSENT_CLASS);
        $this->deleteElementType('OphCoCvi', self::RETIRED_ET_CONSULTANT_CLASS);
    }

    public function safeDown()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName("OphCoCvi");
        $this->insert('element_type', array('name' => 'Consent Signature','class_name' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_ConsentSignature', 'event_type_id' => $event_type_id, 'display_order' => 30, 'required' => 1));
        $this->deleteElementType('OphCoCvi', 'Element_OphCoCvi_Esign');
        $this->dropForeignKey('fk_ophcocvi_signature_etid', self::ITEM_TBL);
        $this->dropForeignKey('fk_et_ophcocvi_esign_event_id', self::ELEMENT_TBL);
        $this->dropForeignKey('fk_ophcocvi_signature_sfile_id', self::ITEM_TBL);
        $this->dropForeignKey('fk_ophcocvi_signature_suser_id', self::ITEM_TBL);
        $this->dropOETable(self::ITEM_TBL, true);
        $this->dropOETable(self::ELEMENT_TBL, true);
    }
}