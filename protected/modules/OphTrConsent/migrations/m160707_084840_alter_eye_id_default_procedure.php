<?php

class m160707_084840_alter_eye_id_default_procedure extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophtrconsent_procedure', 'eye_id', 'int(10) unsigned NOT NULL');
    }

    public function down()
    {
        $this->alterColumn('et_ophtrconsent_procedure', 'eye_id', "int(10) unsigned NOT NULL DEFAULT '2'");
    }
}
