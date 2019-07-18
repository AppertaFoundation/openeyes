<?php

class m181219_015650_add_comment_field extends OEMigration
{
    public function up()
    {
        $this->addColumn('trial_patient', 'comment', 'text');
        $this->addColumn('trial_patient_version', 'comment', 'text');


    }

    public function down()
    {
        $this->dropColumn('trial_patient','comment');
        $this->dropColumn('trial_patient_version','comment');
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
