<?php

class m210713_162420_protected_file_title_default_value extends CDbMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE `protected_file` MODIFY `title` VARCHAR(64) DEFAULT NULL");
    }

    public function down()
    {
        echo "This migration does not support down migration.\n";
        return false;
    }
}
