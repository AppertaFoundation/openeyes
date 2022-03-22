<?php

class m220114_163900_remove_old_nhs_num_settings extends OEMigration
{
    public function up()
    {
        $keys = array('nhs_num_label', 'nhs_num_label_short', 'hos_num_label', 'hos_num_label_short');
        $tables = array('setting_installation', 'setting_institution', 'setting_site', 'setting_metadata');
        foreach ($tables as $table) {
            foreach ($keys as $key) {
                $this->delete($table, '`key` = :keys', array(':keys' => $key));
            }
        }
    }

    public function down()
    {
        echo "This migration does not support down";
    }
}
