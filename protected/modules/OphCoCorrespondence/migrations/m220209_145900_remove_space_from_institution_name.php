<?php

class m220209_145900_remove_space_from_institution_name extends OEMigration
{
    public function safeUp()
    {
        $this->execute("UPDATE
                    institution
                    SET `name` = 'Rivers Ace Centre - Specialist Teaching Team'
                    WHERE `name` = ' Rivers Ace Centre - Specialist Teaching Team'"
                );
    }

    public function safeDown()
    {
        $this->execute("UPDATE
                    institution
                    SET `name` = ' Rivers Ace Centre - Specialist Teaching Team'
                    WHERE `name` = 'Rivers Ace Centre - Specialist Teaching Team'"
                );
    }
}
