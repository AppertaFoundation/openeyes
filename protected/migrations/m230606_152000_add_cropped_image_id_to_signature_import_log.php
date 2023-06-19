<?php

class m230606_152000_add_cropped_image_id_to_signature_import_log extends OEMigration
{
    public $table_name = 'signature_import_log';

    public function safeUp()
    {

        $this->addColumn("{$this->table_name}", 'cropped_file_id', 'int(10) unsigned DEFAULT NULL');
        $this->addForeignKey("{$this->table_name}_protected_file_fk", "{$this->table_name}", 'cropped_file_id', 'protected_file', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey("{$this->table_name}_protected_file_fk","{$this->table_name}");
        $this->dropColumn("{$this->table_name}", 'cropped_file_id');
    }
}
