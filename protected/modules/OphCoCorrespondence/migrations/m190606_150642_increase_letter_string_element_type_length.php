<?php

class m190606_150642_increase_letter_string_element_type_length extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('ophcocorrespondence_letter_string', 'element_type', 'varchar(255)');
    }

    public function down()
    {
        $this->alterColumn('ophcocorrespondence_letter_string', 'element_type', 'varchar(64)');
    }
}