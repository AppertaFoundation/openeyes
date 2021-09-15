<?php

class m160825_130251_default_signature_to_patient extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophcocvi_consentsig', 'is_patient', 'tinyint(1) unsigned DEFAULT 1');
    }

    public function down()
    {
        $this->alterColumn('et_ophcocvi_consentsig', 'is_patient', 'tinyint(1) unsigned NOT NULL DEFAULT 1');
    }
}
