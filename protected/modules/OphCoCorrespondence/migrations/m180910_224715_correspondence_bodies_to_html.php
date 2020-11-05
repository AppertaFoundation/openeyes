<?php

class m180910_224715_correspondence_bodies_to_html extends CDbMigration
{
    public function up()
    {
        $this->dbConnection->createCommand("UPDATE et_ophcocorrespondence_letter SET body = REGEXP_REPLACE(body, '\\\\r?\\\\n', '<br/>');")->execute();
    }

    public function down()
    {
        $this->dbConnection->createCommand("UPDATE et_ophcocorrespondence_letter SET body = REGEXP_REPLACE(body, '<br/>', '\\\\r\\\\n);")->execute();
    }
}
