<?php

class m210830_112912_additional_signatures extends OEMigration
{
    private const ELEMENT_TBL = 'et_ophtrconsent_additional_signatures';
    private const TYPE_PATIENT_AGREEMENT_ID = 1;
    private const TYPE_PARENTAL_AGREEMENT_ID = 2;
    private const TYPE_PATIENT_PARENTAL_AGREEMENT_ID = 3;

    public function safeUp()
    {
        $this->createOETable(
            self::ELEMENT_TBL,
            [
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED',
                'interpreter_required' => 'TINYINT(1) UNSIGNED DEFAULT 0',
                'interpreter_name' => 'varchar(255) DEFAULT NULL',
                'interpreter_signature_id' => 'INT(11) NULL DEFAULT NULL',
                'witness_required' => 'TINYINT(1) UNSIGNED DEFAULT 0',
                'witness_name' => 'varchar(255) DEFAULT NULL',
                'witness_signature_id' => 'INT(11) NULL DEFAULT NULL',
                'child_agreement' => 'TINYINT(1) UNSIGNED NULL',
                'guardian_required' => 'TINYINT(1) UNSIGNED DEFAULT 0',
                'guardian_name' => 'varchar(255) DEFAULT NULL',
                'guardian_relationship' => 'varchar(255) DEFAULT NULL',
                'guardian_signature_id' => 'INT(11) NULL DEFAULT NULL',
                'patient_signature_id' => 'INT(11) NULL DEFAULT NULL',
                'child_signature_id' => 'INT(11) NULL DEFAULT NULL',
            ],
            true
        );

        $this->addForeignKey('fk_et_ophtrconsent_signature_event_id', self::ELEMENT_TBL, 'event_id', 'event', 'id');
        $this->addForeignKey('fk_et_ophtrconsent_add_signature_isid', self::ELEMENT_TBL, 'interpreter_signature_id', 'ophtrconsent_signature', 'id');
        $this->addForeignKey('fk_et_ophtrconsent_add_signature_wsid', self::ELEMENT_TBL, 'witness_signature_id','ophtrconsent_signature', 'id');
        $this->addForeignKey('fk_et_ophtrconsent_add_signature_gsid', self::ELEMENT_TBL, 'guardian_signature_id','ophtrconsent_signature', 'id');
        $this->addForeignKey('fk_et_ophtrconsent_add_signature_csid', self::ELEMENT_TBL, 'child_signature_id','ophtrconsent_signature', 'id');
        $this->addForeignKey('fk_et_ophtrconsent_add_signature_psid', self::ELEMENT_TBL, 'patient_signature_id','ophtrconsent_signature', 'id');

        $element_id = $this->createElementType("OphTrConsent", 'Additional Signatures', array(
            'class_name' => 'OEModule\\OphTrConsent\\models\\Element_OphTrConsent_AdditionalSignatures',
            'default' => true,
            'required' => 1,
            'display_order' => 100
        ));

        $this->execute("
            INSERT INTO ophtrconsent_type_assessment (element_id, type_id, display_order )
            VALUES
            (
                " . $element_id . ",
                " . self::TYPE_PATIENT_AGREEMENT_ID . ",
                10
            ),
            (
                 " . $element_id . ",
                 " . self::TYPE_PARENTAL_AGREEMENT_ID . ",
                 10
            ),
            (
                " . $element_id . ",
                " . self::TYPE_PATIENT_PARENTAL_AGREEMENT_ID . ",
                10
            )
        ");
    }

    public function safeDown()
    {
        $this->deleteElementType(
            "OphTrConsent",
            "OEModule\\OphTrConsent\\models\\Element_OphTrConsent_AdditionalSignatures"
        );

        $this->dropForeignKey('fk_et_ophtrconsent_add_signature_psid', self::ELEMENT_TBL);
        $this->dropForeignKey('fk_et_ophtrconsent_add_signature_csid', self::ELEMENT_TBL);
        $this->dropForeignKey('fk_et_ophtrconsent_add_signature_isid', self::ELEMENT_TBL);
        $this->dropForeignKey('fk_et_ophtrconsent_add_signature_wsid', self::ELEMENT_TBL);
        $this->dropForeignKey('fk_et_ophtrconsent_add_signature_gsid', self::ELEMENT_TBL);
        $this->dropForeignKey('fk_et_ophtrconsent_signature_event_id', self::ELEMENT_TBL);
        $this->dropOETable(self::ELEMENT_TBL, true);
    }
}
