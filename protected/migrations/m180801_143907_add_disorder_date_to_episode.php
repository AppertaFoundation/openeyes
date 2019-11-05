<?php

class m180801_143907_add_disorder_date_to_episode extends CDbMigration
{
    public function up()
    {
        $this->addColumn('episode', 'disorder_date', 'varchar(10) DEFAULT NULL');
        $this->addColumn('episode_version', 'disorder_date', 'varchar(10) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('episode', 'disorder_date');
        $this->dropColumn('episode_version', 'disorder_date');
    }
}