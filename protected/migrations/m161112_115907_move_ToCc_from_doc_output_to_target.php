<?php

class m161112_115907_move_ToCc_from_doc_output_to_target extends CDbMigration
{
    public function safeUp()
    {
            $this->addColumn('document_target', 'ToCc', 'VARCHAR(2) NOT NULL AFTER `document_instance_id`');
            $this->addColumn('document_target_version', 'ToCc', 'VARCHAR(2) NOT NULL AFTER `document_instance_id`');

            $this->dropColumn('document_output', 'ToCc');
            $this->dropColumn('document_output_version', 'ToCc');
    }

    public function safeDown()
    {
            $this->addColumn('document_output', 'ToCc', 'VARCHAR(2) NOT NULL AFTER `output_type`');
            $this->addColumn('document_output_version', 'ToCc', 'VARCHAR(2) NOT NULL AFTER `output_type`');

            $this->dropColumn('document_target', 'ToCc');
            $this->dropColumn('document_target_version', 'ToCc');
    }
}
