<?php

class m210611_012138_altering_pupillary_abnormalities_version_table_to_match_base extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('ophciexamination_pupillaryabnormalities_abnormality_version', 'active', 'tinyint(1) unsigned not null AFTER name');
    }

    public function down()
    {
        $this->alterOEColumn('ophciexamination_pupillaryabnormalities_abnormality_version', 'active', 'tinyint(1) unsigned not null AFTER created_date');
    }
}
