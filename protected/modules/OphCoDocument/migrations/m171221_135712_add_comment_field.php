<?php

class m171221_135712_add_comment_field extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophcodocument_document', 'comment', 'text');
        $this->addColumn('et_ophcodocument_document_version', 'comment', 'text');

    }

    public function down()
    {
        $this->dropColumn('et_ophcodocument_document', 'comment');
        $this->dropColumn('et_ophcodocument_document_version', 'comment');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
