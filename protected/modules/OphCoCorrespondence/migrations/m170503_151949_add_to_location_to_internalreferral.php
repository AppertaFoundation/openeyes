<?php

class m170503_151949_add_to_location_to_internalreferral extends OEMigration
{
    public function up()
    {
        //fix previous site_id type
        $this->alterColumn('et_ophcocorrespondence_letter', 'site_id', 'INT(10) UNSIGNED NULL');
        $this->alterColumn('et_ophcocorrespondence_letter_version', 'site_id', 'INT(10) UNSIGNED NULL');
        $this->addForeignKey("et_ophcocorrespondence_letter_ibfk_4", 'et_ophcocorrespondence_letter', 'site_id', 'site', 'id');

        $this->addColumn('et_ophcocorrespondence_letter', 'to_location_id', 'INT(11) DEFAULT NULL');
        $this->addColumn('et_ophcocorrespondence_letter_version', 'to_location_id', 'INT(11) DEFAULT NULL');

        $this->createOETable("ophcocorrespondence_internal_referral_to_location",
            array('id' => 'pk',
                'site_id' => 'INT(10) UNSIGNED NULL'
            ), true);

        $this->addForeignKey("et_ophcocorrespondence_letter_ibfk_to_location", 'et_ophcocorrespondence_letter', 'to_location_id', 'ophcocorrespondence_internal_referral_to_location', 'id');
        $this->addForeignKey("ophcocorrespondence_internal_referral_to_location_to_site", 'ophcocorrespondence_internal_referral_to_location', 'site_id', 'site', 'id');

    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocorrespondence_letter_ibfk_4', 'et_ophcocorrespondence_letter');

        $this->dropForeignKey("et_ophcocorrespondence_letter_ibfk_to_location", "et_ophcocorrespondence_letter");
        $this->dropColumn('et_ophcocorrespondence_letter', 'to_location_id');
        $this->dropColumn('et_ophcocorrespondence_letter_version', 'to_location_id');

        $this->dropForeignKey("ophcocorrespondence_internal_referral_to_location_to_site", "ophcocorrespondence_internal_referral_to_location");
        $this->dropOETable("ophcocorrespondence_internal_referral_to_location", true);

    }
}