<?php

class m210706_011441_add_extra_field_to_pgd_psd_setting extends OEMigration
{
    private const EXTRA_FIELDS = array(
        'frequency_id' => array(
            'type' => 'int(11)',
            'ref_tbl' => 'medication_frequency',
        ),
        'duration_id' => array(
            'type' => 'int(11)',
            'ref_tbl' => 'medication_duration',
        ),
        'dispense_condition_id' => array(
            'type' => 'int(11)',
            'ref_tbl' => 'ophdrprescription_dispense_condition'
        ),
        'dispense_location_id' => array(
            'type' => 'int(11)',
            'ref_tbl' => 'ophdrprescription_dispense_location',
        ),
        'comments' => array(
            'type' => 'text',
            'ref_tbl' => null,
        )
    );
    private const TABLE_NAME = 'ophdrpgdpsd_pgdpsd_meds';
    private const VERSION_TABLE_NAME = 'ophdrpgdpsd_pgdpsd_meds_version';
    public function up()
    {
        $table_obj = $this->dbConnection->schema->getTable(self::TABLE_NAME, true);
        $version_table_obj = $this->dbConnection->schema->getTable(self::VERSION_TABLE_NAME, true);
        $tbl_cols = $table_obj->getColumnNames();
        $version_tbl_cols = $version_table_obj->getColumnNames();
        $foreign_keys = $table_obj->foreignKeys;
        if ($table_obj && $version_table_obj) {
            foreach (self::EXTRA_FIELDS as $key => $val) {
                if (in_array($key, $tbl_cols) === false) {
                    $this->addColumn(self::TABLE_NAME, $key, $val['type']);
                    if ($val['ref_tbl'] && !isset($foreign_keys[$key])) {
                        $this->addForeignKey(self::TABLE_NAME . '_' . $key . '_fk', self::TABLE_NAME, $key, $val['ref_tbl'], 'id');
                    }
                }
                if (in_array($key, $version_tbl_cols) === false) {
                    $this->addColumn(self::VERSION_TABLE_NAME, $key, $val['type']);
                }
            }
        }
    }

    public function down()
    {
        $table_obj = $this->dbConnection->schema->getTable(self::TABLE_NAME, true);
        $version_table_obj = $this->dbConnection->schema->getTable(self::VERSION_TABLE_NAME, true);
        $tbl_cols = $table_obj->getColumnNames();
        $version_tbl_cols = $version_table_obj->getColumnNames();
        $foreign_keys = $table_obj->foreignKeys;
        if ($table_obj && $version_table_obj) {
            foreach (self::EXTRA_FIELDS as $key => $val) {
                if (in_array($key, $tbl_cols) !== false) {
                    if ($val['ref_tbl'] && isset($foreign_keys[$key])) {
                        $this->dropForeignKey(self::TABLE_NAME . '_' . $key . '_fk', self::TABLE_NAME);
                    }
                    $this->dropColumn(self::TABLE_NAME, $key);
                }
                if (in_array($key, $version_tbl_cols) !== false) {
                    $this->dropColumn(self::VERSION_TABLE_NAME, $key);
                }
            }
        }
    }
}
