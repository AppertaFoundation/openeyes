<?php

class m220906_113419_remove_add_preset_oprn_from_drug_admin extends OEMigration
{
    const TARGET_TASK = 'TaskDrugAdminMedAdmin';
    const TARGET_OPRN = 'OprnAddPresets';

    public function safeUp()
    {
        $this->removeOperationFromTask(self::TARGET_OPRN, self::TARGET_TASK);
    }

    public function safeDown()
    {
        $this->addOperationToTask(self::TARGET_OPRN, self::TARGET_TASK);
    }
}
