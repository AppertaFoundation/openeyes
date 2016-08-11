<?php

class m160510_142306_ophcocorrespondence_letter_macro_version extends CDbMigration
{
    public function up()
    {

        // was originally in the wrong place, so check the change hasn't already been applied
        $res = $this->dbConnection->createCommand("SHOW COLUMNS FROM `ophcocorrespondence_letter_macro_version` LIKE 'short_code'")->queryAll();
        if (!count($res)) {
            $this->addColumn('ophcocorrespondence_letter_macro_version', 'short_code', 'varchar(3)');
            $this->createIndex('short_code_UNIQUE', 'ophcocorrespondence_letter_macro_version', 'short_code', $unique = true);
        }
    }

    public function down()
    {
        $this->dropColumn('ophcocorrespondence_letter_macro_version', 'short_code');
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
