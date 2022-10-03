<?php

class m221003_121900_et_ophcodocument_document_event_fk extends CDbMigration
{
    public function safeUp()
    {
        $this->addForeignKey('et_ophcodocument_document_ev_fk',
            'et_ophcodocument_document',
            'event_id',
            'event',
            'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('et_ophcodocument_document_ev_fk',
            'et_ophcodocument_document');
    }
}
