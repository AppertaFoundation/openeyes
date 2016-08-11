<?php

class m160622_094034_alter_settings_value_size extends CDbMigration
{
    public function up()
    {
        foreach (array('setting_firm', 'setting_installation', 'setting_institution', 'setting_site', 'setting_specialty', 'setting_subspecialty', 'setting_user') as $table) {
            $this->alterColumn($table, 'value', 'varchar(255) not null');
            $this->alterColumn($table.'_version', 'value', 'varchar(255) not null');
        }
    }

    public function down()
    {
        foreach (array('setting_firm', 'setting_installation', 'setting_institution', 'setting_site', 'setting_specialty', 'setting_subspecialty', 'setting_user') as $table) {
            $this->alterColumn($table, 'value', 'varchar(64) not null');
            $this->alterColumn($table.'_version', 'value', 'varchar(64) not null');
        }
    }
}
