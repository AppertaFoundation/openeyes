<?php

class m180320_131405_set_document_event_printable extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE event_type SET is_printable=1 where class_name ='OphCoDocument'");

    }

    public function down()
    {
        $this->execute("UPDATE event_type SET is_printable=0 where class_name ='OphCoDocument'");

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
