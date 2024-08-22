<?php

class m220307_095000_make_proc_aliases_non_mandatory extends OEMigration
{
    public function safeUp()
    {
        $this->alterOEColumn('proc', 'aliases', 'TEXT NULL', true);
    }

    public function safeDown()
    {
        echo "Down not required";
    }
}
