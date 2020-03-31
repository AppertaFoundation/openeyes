<?php

class m200331_075744_archive_drug_duration_table extends CDbMigration
{
    public function up()
    {
        $this->renameTable('drug_duration', 'archive_drug_duration');
    }

    public function down()
    {
        $this->renameTable('archive_drug_duration', 'drug_duration');
    }

}
