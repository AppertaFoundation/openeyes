<?php

class m170427_111005_introduce_change_episode extends OEMigration
{
    public function up()
    {
        $this->addColumn('episode', 'change_tracker', 'boolean');
        $this->addColumn('episode_version', 'change_tracker', 'boolean');
    }

    public function down()
    {
        $this->dropColumn('episode_version', 'change_tracker');
        $this->dropColumn('episode', 'change_tracker');
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
