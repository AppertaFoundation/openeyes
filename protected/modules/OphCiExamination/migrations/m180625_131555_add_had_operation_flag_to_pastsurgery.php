<?php

class m180625_131555_add_had_operation_flag_to_pastsurgery extends \OEMigration
{
    public function up()
    {
        $this->addColumn("ophciexamination_pastsurgery_op", "had_operation", "tinyint(1) NOT NULL DEFAULT -9");
        $this->addColumn("ophciexamination_pastsurgery_op_version", "had_operation", "tinyint(1) NOT NULL DEFAULT -9");
    }

    public function down()
    {
        $this->dropColumn("ophciexamination_pastsurgery_op", "had_operation");
        $this->dropColumn("ophciexamination_pastsurgery_op_version", "had_operation");
    }
}