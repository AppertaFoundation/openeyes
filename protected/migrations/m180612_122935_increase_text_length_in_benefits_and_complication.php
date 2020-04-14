<?php

class m180612_122935_increase_text_length_in_benefits_and_complication extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('benefit', 'name', 'TEXT DEFAULT NULL');
        $this->alterColumn('benefit_version', 'name', 'TEXT DEFAULT NULL');
        $this->alterColumn('complication', 'name', 'TEXT DEFAULT NULL');
        $this->alterColumn('complication_version', 'name', 'TEXT DEFAULT NULL');
    }

    public function down()
    {
        $this->alterColumn('benefit', 'name', 'VARCHAR(255)');
        $this->alterColumn('benefit_version', 'name', 'VARCHAR(255)');
        $this->alterColumn('complication', 'name', 'VARCHAR(255)');
        $this->alterColumn('complication_version', 'name', 'VARCHAR(255)');
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
