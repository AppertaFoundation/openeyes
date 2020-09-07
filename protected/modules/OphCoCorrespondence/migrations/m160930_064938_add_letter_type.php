<?php

class m160930_064938_add_letter_type extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocorrespondence_letter', 'letter_type', 'int(1) not null default 0');
        $this->addColumn('et_ophcocorrespondence_letter_version', 'letter_type', 'int(1) not null default 0');
        $this->addColumn('ophcocorrespondence_letter_macro', 'letter_type', 'int(1) not null default 0');
        $this->addColumn('ophcocorrespondence_letter_macro_version', 'letter_type', 'int(1) not null default 0');
    }

    public function down()
    {
        $this->dropColumn('et_ophcocorrespondence_letter', 'letter_type');
        $this->dropColumn('et_ophcocorrespondence_letter_version', 'letter_type');
        $this->dropColumn('ophcocorrespondence_letter_macro', 'letter_type');
        $this->dropColumn('ophcocorrespondence_letter_macro_version', 'letter_type');
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
