<?php

class m140116_084118_draft_state extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophtrconsent_type', 'draft', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophtrconsent_type', 'print', 'tinyint(1) unsigned not null');
    }

    public function down()
    {
        $this->dropColumn('et_ophtrconsent_type', 'draft');
        $this->dropColumn('et_ophtrconsent_type', 'print');
    }
}
