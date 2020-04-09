<?php

class m170313_091135_adding_internal_referral_fields_to_letter extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocorrespondence_letter', 'to_subspecialty_id', 'int(10) unsigned DEFAULT NULL COMMENT "Internal Referral"');
        $this->addColumn('et_ophcocorrespondence_letter_version', 'to_subspecialty_id', 'int(10) unsigned DEFAULT NULL COMMENT "Internal Referral"');
        $this->addForeignKey('et_ophcocorrespondence_letter_ibfk_3', 'et_ophcocorrespondence_letter', 'to_subspecialty_id', 'subspecialty', 'id');

        $this->addColumn('et_ophcocorrespondence_letter', 'to_firm_id', 'int(10) unsigned DEFAULT NULL COMMENT "Internal Referral"');
        $this->addColumn('et_ophcocorrespondence_letter_version', 'to_firm_id', 'int(10) unsigned DEFAULT NULL COMMENT "Internal Referral"');
        $this->addForeignKey("et_ophcocorrespondence_letter_ibfk_to_firm_id", 'et_ophcocorrespondence_letter', 'to_firm_id', 'firm', 'id');

        $this->addColumn('et_ophcocorrespondence_letter', 'is_urgent', 'tinyint(1) DEFAULT "0" COMMENT "Internal Referral"');
        $this->addColumn('et_ophcocorrespondence_letter_version', 'is_urgent', 'tinyint(1) DEFAULT "0" COMMENT "Internal Referral"');

        $this->addColumn('et_ophcocorrespondence_letter', 'is_same_condition', 'tinyint(1) DEFAULT NULL');
        $this->addColumn('et_ophcocorrespondence_letter_version', 'is_same_condition', 'tinyint(1) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocorrespondence_letter_ibfk_to_firm_id', 'et_ophcocorrespondence_letter');
        $this->dropForeignKey('et_ophcocorrespondence_letter_ibfk_3', 'et_ophcocorrespondence_letter');

        $this->dropColumn('et_ophcocorrespondence_letter', 'to_subspecialty_id');
        $this->dropColumn('et_ophcocorrespondence_letter', 'to_firm_id');
        $this->dropColumn('et_ophcocorrespondence_letter', 'is_urgent');
        $this->dropColumn('et_ophcocorrespondence_letter', 'is_same_condition');

        $this->dropColumn('et_ophcocorrespondence_letter_version', 'to_subspecialty_id');
        $this->dropColumn('et_ophcocorrespondence_letter_version', 'to_firm_id');
        $this->dropColumn('et_ophcocorrespondence_letter_version', 'is_urgent');
        $this->dropColumn('et_ophcocorrespondence_letter_version', 'is_same_condition');
    }
}
