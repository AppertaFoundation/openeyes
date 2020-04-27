<?php

class m190327_153817_add_aliases_to_disorder_table extends CDbMigration
{
    public function up()
    {
        $this->addColumn('disorder', 'aliases', 'text');
        $this->addColumn('disorder_version', 'aliases', 'text');
    }

    public function down()
    {
        $this->dropColumn('disorder', 'aliases');
        $this->dropColumn('disorder_version', 'aliases');
    }
}
