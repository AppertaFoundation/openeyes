<?php

class m211209_112400_rename_Operation_Checklist_case extends CDbMigration
{
    public function safeUp()
    {
        $this->dbConnection->createCommand("UPDATE `event_type` SET `name` = 'Operation checklists' WHERE `name` = 'Operation Checklists'")->execute();
    }

    public function safeDown()
    {
        $this->dbConnection->createCommand("UPDATE `event_type` SET `name` = 'Operation Checklists' WHERE `name` = 'Operation checklists'")->execute();
    }
}
