<?php

class m180822_234054_add_specialty_short_name_column extends OEMigration
{
    public function safeUp()
    {
        foreach (['subspecialty', 'subspecialty_version'] as $table_name) {
            $this->addColumn($table_name, 'short_name', 'varchar(40) AFTER name');
            $this->update($table_name, array('short_name' => new CDbExpression('name')));
            $this->alterColumn($table_name, 'short_name', 'varchar(40) NOT NULL');

            $this->update($table_name, array('short_name' => 'General'), 'name = "General Ophthalmology"');
        }
    }

    public function safeDown()
    {
        foreach (['subspecialty', 'subspecialty_version'] as $table_name) {
            $this->dropColumn($table_name, 'short_name');
        }
    }
}
