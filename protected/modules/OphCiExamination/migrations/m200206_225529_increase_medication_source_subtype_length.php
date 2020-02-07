<?php

class m200206_225529_increase_medication_source_subtype_length extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('medication_version', 'source_subtype', 'VARCHAR(45) NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->alterColumn('medication_version', 'source_subtype', 'VARCHAR(10) NULL DEFAULT NULL');
    }
}
