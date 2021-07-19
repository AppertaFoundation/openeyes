<?php

class m210503_045822_add_pgdpsd_column_to_prescription_item extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('event_medication_use', 'pgdpsd_id', 'int(11) DEFAULT NULL AFTER usage_subtype', true);
        $this->addForeignKey('event_medication_use_pgdpsd_fk', 'event_medication_use', 'pgdpsd_id', 'ophdrpgdpsd_pgdpsd', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('event_medication_use_pgdpsd_fk', 'event_medication_use');
        $this->dropOEColumn('event_medication_use', 'pgdpsd_id', true);
    }
}
