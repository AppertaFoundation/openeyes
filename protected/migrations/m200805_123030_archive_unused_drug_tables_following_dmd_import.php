<?php

class m200805_123030_archive_unused_drug_tables_following_dmd_import extends OEMigration
{
    public function safeUp()
    {
        $this->renameOETable('medication_allergy_assignment', 'archive_medication_allergy_assignment', true);
        $this->renameOETable('medication_common', 'archive_medication_common', true);
        $this->dropFKs('archive_medication_allergy_assignment');
        $this->dropFKs('archive_medication_common');
    }

    public function safeDown()
    {
        $this->renameOETable('archive_medication_allergy_assignment', 'medication_allergy_assignment', true);
        $this->renameOETable('archive_medication_common', 'medication_common', true);
        echo "***** Foreign Keys not recreated ******";
    }

    private function getFKs($table)
    {
        $sql = "select fks.table_name as foreign_table,
        fks.constraint_name
        from information_schema.referential_constraints fks
        where fks.constraint_schema = (SELECT DATABASE())
            and fks.TABLE_NAME like '$table'
        group by fks.constraint_schema,
          fks.table_name,
          fks.unique_constraint_schema,
          fks.referenced_table_name,
          fks.constraint_name;";

        return $this->dbConnection->createCommand($sql)->query();
    }

    private function dropFKs($table)
    {
        if ($foreign_keys = $this->getFKs($table)) {
            foreach ($foreign_keys as $foreign_key) {
                $this->dropForeignKey($foreign_key['constraint_name'], $foreign_key['foreign_table']);
            }
        }

        return 0;
    }
}
