<?php

class m220118_144400_fix_benefit_name extends OEMigration
{
    public function safeUp()
    {
        $this->execute("UPDATE benefit SET `name`='investigation and further treatment planning' WHERE `name` = 'invesigation and further treatment planning';");
    }

    public function safeDown()
    {
        $this->execute("UPDATE benefit SET `name`='invesigation and further treatment planning' WHERE `name` = 'investigation and further treatment planning';");
    }
}
