<?php

class m221121_154600_add_indexes_for_audit extends OEMigration
{
    public function safeUp()
    {
        $this->execute("CREATE INDEX et_ophciexamination_visualacuity_version_event_id_IDX USING BTREE ON et_ophciexamination_visualacuity_version (event_id)");
        $this->execute("CREATE INDEX ophciexamination_visualacuity_reading_version_element_id_IDX USING BTREE ON ophciexamination_visualacuity_reading_version (element_id)");
    }

    public function safeDown()
    {
        $this->execute("DROP INDEX et_ophciexamination_visualacuity_version_event_id_IDX ON et_ophciexamination_visualacuity_version");
        $this->execute("DROP INDEX ophciexamination_visualacuity_reading_version_element_id_IDX ON ophciexamination_visualacuity_reading_version");
    }
}
