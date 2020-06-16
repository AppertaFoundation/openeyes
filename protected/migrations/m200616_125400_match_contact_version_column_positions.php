<?php

class m200616_125400_match_contact_version_column_positions extends OEMigration
{
    public function safeUp()
    {
        $this->alterColumn("contact_version", "maiden_name", "varchar(200) AFTER last_name");
        $this->alterColumn("contact_version", "fax", "varchar(225) AFTER national_code");
    }

    public function down()
    {
        $this->alterColumn("contact_version", "maiden_name", "varchar(200) AFTER version_id");
        $this->alterColumn("contact_version", "fax", "varchar(225) AFTER maiden_name");
    }
}
