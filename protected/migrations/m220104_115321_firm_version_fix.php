<?php

class m220104_115321_firm_version_fix extends OEMigration
{
    public function safeUp()
    {
        $firm_version_table = $this->dbConnection->schema->getTable('firm_version', true);
        if (isset($firm_version_table->columns['pas_code'])) {
            $this->execute('ALTER TABLE firm_version MODIFY COLUMN pas_code VARCHAR(20);');
        }
    }

    public function safeDown()
    {
        echo "m220104_115321_firm_version_fix does not support migration down.\n";
        return false;
    }
}
