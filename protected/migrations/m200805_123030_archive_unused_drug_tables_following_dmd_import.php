<?php

class m200805_123030_archive_unused_drug_tables_following_dmd_import extends OEMigration
{
    public function safeUp()
    {
        $this->renameOETable('medication_allergy_assignment', 'archive_medication_allergy_assignment', true);
        $this->renameOETable('medication_common', 'archive_medication_common', true);
    }

    public function safeDown()
    {
        $this->renameOETable('archive_medication_allergy_assignment', 'medication_allergy_assignment', true);
        $this->renameOETable('archive_medication_common', 'medication_common', true);
    }
}
