<?php

class m180510_093822_add_column_emu_comments extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('event_medication_use', 'comments', 'TINYTEXT NULL', true);
    }

    public function down()
    {
        $this->dropOEColumn('event_medication_use', 'comments', true);
    }
}
