<?php

class m180611_155900_add_pas_visit_id_to_event extends \OEMigration
{
    public function up()
    {
        $this->addColumn('event', 'pas_visit_id', 'VARCHAR(40) NULL');
        $this->addColumn('event_version', 'pas_visit_id', 'VARCHAR(40) NULL');
    }

    public function down()
    {
        $this->dropColumn('event', 'pas_visit_id');
        $this->dropColumn('event_version', 'pas_visit_id');
    }
}
