<?php

class m170502_132513_add_deleted_col_genetics_patient extends OEMigration
{
    public function up()
    {
        $this->addColumn("genetics_patient", "deleted", "TINYINT(1) DEFAULT 0");
    }

    public function down()
    {
        $this->dropColumn("genetics_patient", "deleted");
    }
}