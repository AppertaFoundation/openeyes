<?php

class m140703_153630_currentman_lookuptable_active extends OEMigration
{
    private $tables = array(
        'ophciexamination_managementglaucomastatus',
        'ophciexamination_managementrelproblem',
        'ophciexamination_managementdrops',
        'ophciexamination_managementsurgery',
    );
    public function up()
    {
        foreach ($this->tables as $table) {
            $this->addColumn($table, 'active', 'tinyint(1) unsigned DEFAULT 1');
            $this->addColumn($table.'_version', 'active', 'tinyint(1) unsigned DEFAULT 1');
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            $this->dropColumn($table, 'active');
            $this->dropColumn($table.'_version', 'active');
        }
    }
}
