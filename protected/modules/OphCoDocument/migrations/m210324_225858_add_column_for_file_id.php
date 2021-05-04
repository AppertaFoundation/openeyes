<?php

class m210324_225858_add_column_for_file_id extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('ophcodocument_sub_types', 'document_id', 'int(10) unsigned null', true);

        $this->addForeignKey(
            'ophcodocument_sub_types_document_id_fk',
            'ophcodocument_sub_types',
            'document_id',
            'protected_file',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'ophcodocument_sub_types_document_id_fk',
            'ophcodocument_sub_types',
        );

        $this->dropOEColumn('ophcodocument_sub_types', 'document_id', true);
    }
}
