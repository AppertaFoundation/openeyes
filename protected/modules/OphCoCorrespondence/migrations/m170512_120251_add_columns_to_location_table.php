<?php

class m170512_120251_add_columns_to_location_table extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophcocorrespondence_internal_referral_to_location', 'location', "VARCHAR(10) AFTER site_id");
        $this->addColumn('ophcocorrespondence_internal_referral_to_location', 'location_name', "VARCHAR(50) AFTER location");

        $this->addColumn('ophcocorrespondence_internal_referral_to_location_version', 'location', "VARCHAR(10) AFTER site_id");
        $this->addColumn('ophcocorrespondence_internal_referral_to_location_version', 'location_name', "VARCHAR(50) AFTER location");
    }

    public function down()
    {
        $this->dropColumn("ophcocorrespondence_internal_referral_to_location", "location");
        $this->dropColumn("ophcocorrespondence_internal_referral_to_location", "location_name");

        $this->dropColumn("ophcocorrespondence_internal_referral_to_location_version", "location");
        $this->dropColumn("ophcocorrespondence_internal_referral_to_location_version", "location_name");
    }
}