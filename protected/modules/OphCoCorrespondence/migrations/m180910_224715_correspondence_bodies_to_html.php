<?php

class m180910_224715_correspondence_bodies_to_html extends CDbMigration
{
    public function up()
    {
        foreach ($this->dbConnection->createCommand()
                     ->select('*')->from('et_ophcocorrespondence_letter')
                     ->queryAll() as $i => $letter
        ) {
            $altered_body = preg_replace("/\r?\n/", "<br/>", $letter ['body']);

            $this->update('et_ophcocorrespondence_letter',
                array('body' => $altered_body),
                'id = ' . ($letter ['id'])
            );
        }
    }

    public function down()
    {
        foreach ($this->dbConnection->createCommand()
                     ->select('*')->from('et_ophcocorrespondence_letter')
                     ->queryAll() as $i => $letter
        ){
            $altered_body = preg_replace("/<br\\>/", "\n", $letter['body']);

            $this->update('et_ophcocorrespondence_letter',
                array('body' => $altered_body),
                'id = '.($letter['id'])
            );
        }
    }
}