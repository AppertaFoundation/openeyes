<?php

class m170408_132957_grant_gr_creation_to_lab_users extends CDbMigration
{
    public function up()
    {
        $this->execute("INSERT INTO authitemchild (`parent`, `child`) VALUES ('Genetics Laboratory Technician', 'TaskCreateGeneticResults')");
        $this->execute("INSERT INTO authitemchild (`parent`, `child`) VALUES ('Genetics Laboratory Technician', 'TaskEditGeneticResults')");
    }

    public function down()
    {
        $this->execute("DELETE FROM authitemchild WHERE `parent` = 'Genetics Laboratory Technician' AND `child` = 'TaskCreateGeneticResults')");
        $this->execute("DELETE FROM authitemchild WHERE `parent` = 'Genetics Laboratory Technician' AND `child` = 'TaskEditGeneticResults')");
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}