<?php

class m170515_104205_add_location_code_to_site extends OEMigration
{
    public function up()
    {
        $this->addColumn("site", "location_code", "VARCHAR(5) AFTER short_name");
        $this->addColumn("site_version", "location_code", "VARCHAR(5) AFTER short_name");

        $this->addColumn("ophcocorrespondence_internal_referral_to_location", "is_active", "TINYINT(1) AFTER site_id");
        $this->addColumn("ophcocorrespondence_internal_referral_to_location_version", "is_active", "TINYINT(1) AFTER site_id");

        $this->dropColumn("ophcocorrespondence_internal_referral_to_location", "location_name");
        $this->dropColumn("ophcocorrespondence_internal_referral_to_location_version", "location_name");

        $this->dropColumn("ophcocorrespondence_internal_referral_to_location", "location");
        $this->dropColumn("ophcocorrespondence_internal_referral_to_location_version", "location");
    }

    public function down()
    {
        $this->dropColumn('site', 'location_code');
        $this->dropColumn('site_version', 'location_code');

        $this->dropColumn('ophcocorrespondence_internal_referral_to_location', 'is_active');
        $this->dropColumn('ophcocorrespondence_internal_referral_to_location_version', 'is_active');

        $this->addColumn('ophcocorrespondence_internal_referral_to_location', 'location', "VARCHAR(10) AFTER site_id");
        $this->addColumn('ophcocorrespondence_internal_referral_to_location_version', 'location', "VARCHAR(10) AFTER site_id");

        $this->addColumn('ophcocorrespondence_internal_referral_to_location', 'location_name', "VARCHAR(50) AFTER location");
        $this->addColumn('ophcocorrespondence_internal_referral_to_location_version', 'location_name', "VARCHAR(50) AFTER location");
    }
}
