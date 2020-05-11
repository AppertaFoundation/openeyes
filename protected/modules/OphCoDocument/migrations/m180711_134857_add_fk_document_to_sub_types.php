<?php

class m180711_134857_add_fk_document_to_sub_types extends CDbMigration
{
    public function up()
    {
        $this->addForeignKey(
            'fk_document_event_sub_type',
            'et_ophcodocument_document',
            'event_sub_type',
            'ophcodocument_sub_types',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_document_event_sub_type', 'et_ophcodocument_document');
    }
}
