<?php

class m170511_105917_internal_referral_to_location_table extends OEMigration
{
	public function safeUp()
	{
        $this->dropForeignKey('et_ophcocorrespondence_letter_ibfk_5', 'et_ophcocorrespondence_letter');

        $this->renameColumn('et_ophcocorrespondence_letter', 'to_site_id', 'to_location_id');
        $this->alterColumn('et_ophcocorrespondence_letter', 'to_location_id', 'INT(11)');

        $this->createOETable("ophcocorrespondence_internal_referral_to_location",
            array('id' => 'pk',
                'site_id' => 'INT(10) UNSIGNED NULL'
            ), true);

        // to change the FK we have to remove all the exiting to_site_id values
        // as this is a new feature it won't cause any problem
        $this->execute("UPDATE et_ophcocorrespondence_letter SET to_location_id = NULL;");

        $this->addForeignKey("et_ophcocorrespondence_letter_ibfk_7", 'et_ophcocorrespondence_letter', 'to_location_id', 'ophcocorrespondence_internal_referral_to_location', 'id');
        $this->addForeignKey("ophcocorrespondence_internal_referral_to_location_to_site", 'ophcocorrespondence_internal_referral_to_location', 'site_id', 'site', 'id');

	}

	public function safeDown()
	{
        $this->dropForeignKey('et_ophcocorrespondence_letter_ibfk_7', 'et_ophcocorrespondence_letter');
        $this->dropForeignKey('ophcocorrespondence_internal_referral_to_location_to_site', 'ophcocorrespondence_internal_referral_to_location');

        $this->renameColumn('et_ophcocorrespondence_letter', 'to_location_id', 'to_site_id');
        $this->alterColumn('et_ophcocorrespondence_letter', 'to_site_id', 'INT(10) UNSIGNED');

        // have to wipe out, ids won't match
        $this->execute("UPDATE et_ophcocorrespondence_letter SET to_site_id = NULL;");
        $this->addForeignKey("et_ophcocorrespondence_letter_ibfk_5", 'et_ophcocorrespondence_letter', 'to_site_id', 'site', 'id');
        $this->dropOETable("ophcocorrespondence_internal_referral_to_location", true);
	}

}