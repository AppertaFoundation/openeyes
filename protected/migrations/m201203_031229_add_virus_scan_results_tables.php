<?php

class m201203_031229_add_virus_scan_results_tables extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'field_type_id' => 3,
            'key' => 'enable_virus_scanning',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off',
            'name' => 'Enable virus scanning',
        ));

        $this->createTable('virus_scan', array(
            'id' => 'pk',
        ));

        $this->createTable('virus_scan_item', array(
            'id' => 'pk',
            'parent_scan_id' => 'int(11) NOT NULL',
            'file_uid' => 'text NOT NULL',
            'scan_result' => 'text NOT NULL',
            'details' => 'text',
        ));
        $this->addForeignKey('parent_scan_id_fk', 'virus_scan_item', 'parent_scan_id', 'virus_scan', 'id');

        $this->createTable('quarantined_file', array(
            'id'=>'pk',
            'original_uid' => 'text NOT NULL',
            'quarantine_reason'=>'text NOT NULL',
        ));

        $this->createTable('quarantined_placeholder_file', array(
            'id'=>'pk',
            'mimetype'=>'text NOT NULL',
            'file_contents'=>'longblob NOT NULL',
        ));

        $placeholder_dir = __DIR__ . '/../quarantined_placeholder_files';

        $this->insertMultiple('quarantined_placeholder_file', array(
            array(
                'mimetype'=>'application/pdf',
                'file_contents'=>file_get_contents($placeholder_dir . '/QuarantinedFile.pdf'),
            ),
            array(
                'mimetype'=>'application/png',
                'file_contents'=>file_get_contents($placeholder_dir . '/QuarantinedFile.png'),
            ),
            array(
                'mimetype'=>'application/jpeg',
                'file_contents'=>file_get_contents($placeholder_dir . '/QuarantinedFile.jpg'),
            ),
            array(
                'mimetype'=>'application/gif',
                'file_contents'=>file_get_contents($placeholder_dir . '/QuarantinedFile.gif'),
            ),
        ));
    }

    public function down()
    {
        $this->dropForeignKey('parent_scan_id_fk', 'virus_scan_item');
        $this->dropForeignKey('protected_file_id_fk', 'quarantined_file');
        $this->delete('virus_scan_item');
        $this->delete('virus_scan');
        $this->delete('quarantined_file');
        $this->delete('quarantined_placeholder_file');
    }
}
