<?php

class m211026_000517_add_disorder_time_column_to_episode extends OEMigration
{
    public function up()
    {
        // As we're only displaying the time and not manipulating it,
        // we can just capture it as a string rather than as a DATETIME object.
        $this->addOEColumn('episode', 'disorder_time', 'varchar(10)', true);
    }

    public function down()
    {
        $this->dropOEColumn('episode', 'disorder_time', true);
    }
}
