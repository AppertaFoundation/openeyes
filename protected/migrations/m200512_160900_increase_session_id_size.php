<?php

class m200512_160900_increase_session_id_size extends CDbMigration
{
    public function up()
    {
        $this->alterColumn("user_session", "id", "varchar(255)");
    }

    public function down()
    {
        $this->alterColumn("user_session", "id", "varchar(32)");
    }
}
