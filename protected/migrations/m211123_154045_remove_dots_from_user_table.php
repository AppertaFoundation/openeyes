<?php

class m211123_154045_remove_dots_from_user_table extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE `user` SET title=REPLACE(title,'.','')");
    }

    public function down()
    {
        echo "m211123_154045_remove_dots_from_user_table does not support migration down.\n";
        return false;
    }
}
