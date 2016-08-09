<?php

class m131015_125648_proc_aliases extends CDbMigration
{
    public function up()
    {
        $this->addColumn('proc', 'aliases', 'text not null after snomed_term');
    }

    public function down()
    {
        $this->dropColumn('proc', aliases);
    }
}
