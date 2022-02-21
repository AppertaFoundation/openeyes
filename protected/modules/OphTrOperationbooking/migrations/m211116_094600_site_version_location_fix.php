<?php

class m211116_094600_site_version_location_fix extends CDbMigration
{
    public function safeUp()
    {
        $site_version_table = $this->dbConnection->schema->getTable('site_version', true);
        if (isset($site_version_table->columns['location_of_preassessment'])) {
            $this->dropColumn('site_version', 'location_of_preassessment');
        }
    }

    public function safeDown()
    {
        echo "m211116_094600_site_version_location_fix does not support migration down.\n";
        return false;
    }
}
