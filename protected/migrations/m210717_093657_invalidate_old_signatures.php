<?php

class m210717_093657_invalidate_old_signatures extends OEMigration
{
    public function up()
    {
        $this->execute("UPDATE `user` SET signature_file_id = NULL WHERE 1 = 1");
    }

    public function down()
    {
        echo "m210717_093657_invalidate_old_signatures does not support migration down.\n";
        return false;
    }
}

