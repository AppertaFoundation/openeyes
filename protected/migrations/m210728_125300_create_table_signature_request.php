<?php

class m210728_125300_create_table_signature_request extends OEMigration
{
    const TBL_NAME = 'signature_request';

    public function up()
    {
        $signature_request_tbl = $this->dbConnection->schema->getTable(self::TBL_NAME, true);
        if (!isset($signature_request_tbl)) {
            $this->createOETable(self::TBL_NAME, [
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED NOT NULL',
                'element_type_id' => 'INT(10) UNSIGNED DEFAULT NULL',
                'signature_type' => 'INT(10) UNSIGNED DEFAULT NULL',
                'signature_date' => 'DATETIME NULL DEFAULT NULL',
                'signatory_role' => 'VARCHAR(64) DEFAULT NULL',
                'signatory_name' => 'VARCHAR(64) DEFAULT NULL',
            ], true);

            $this->addForeignKey('sr_event_id', self::TBL_NAME, 'event_id', 'event', 'id');
            $this->addForeignKey('sr_element_type_id', self::TBL_NAME, 'element_type_id', 'element_type', 'id');
        } else {
            $this->addOEColumn(self::TBL_NAME, 'signature_type', 'INT(10) UNSIGNED DEFAULT NULL', true);
            $this->addOEColumn(self::TBL_NAME, 'signatory_role', 'VARCHAR(64) DEFAULT NULL', true);
            $this->addOEColumn(self::TBL_NAME, 'signatory_name', 'VARCHAR(64) DEFAULT NULL', true);
        }
    }

    public function down()
    {
        // We can't be sure whether the table was added in this migration or
        // it already existed so down method in not supported
        echo "Can't revert ".__CLASS__.PHP_EOL;
        return false;
    }
}
