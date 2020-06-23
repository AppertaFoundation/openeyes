<?php

class m180910_030837_letter_macros_to_html extends OEMigration
{
    public function up()
    {
        foreach ($this->dbConnection->createCommand()
                     ->select('*')->from('ophcocorrespondence_letter_macro')
                     ->queryAll() as $i => $macro
        ) {
            $altered_body = preg_replace("/\r?\n/", "<br/>", $macro['body']);

            $this->update(
                'ophcocorrespondence_letter_macro',
                array('body' => $altered_body),
                'id = '.($macro['id'])
            );
        }
    }

    public function down()
    {
        foreach ($this->dbConnection->createCommand()
                     ->select('*')->from('ophcocorrespondence_letter_macro')
                     ->queryAll() as $i => $macro
        ) {
            $altered_body = preg_replace("/<br\\>/", "\n", $macro['body']);

            $this->update(
                'ophcocorrespondence_letter_macro',
                array('body' => $altered_body),
                'id = '.($macro['id'])
            );
        }
    }
}
