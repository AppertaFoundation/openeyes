<?php

class m190513_102027_add_modality_code_to_attachment_type extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('attachment_type', 'dicom_modality_code', 'VARCHAR(45) NOT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('attachment_type', 'dicom_modality_code');
    }
}
