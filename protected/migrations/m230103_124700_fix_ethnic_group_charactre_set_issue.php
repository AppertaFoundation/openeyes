<?php

class m230103_124700_fix_ethnic_group_charactre_set_issue extends OEMigration
{
    public function safeUp()
    {
        $this->execute(
            "UPDATE
                ethnic_group
            SET `name` = REPLACE(`name`, :find, :replace)",
            [ ':find' => '?', ':replace' => '/']
        );
    }

    public function safeDown()
    {
        $this->execute(
            "UPDATE
                ethnic_group
            SET `name` = REPLACE(`name`, :find, :replace)",
            [ ':find' => '/', ':replace' => '?']
        );
    }
}
