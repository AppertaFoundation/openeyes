<?php

class m161228_153233_version_element_table extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('et_ophingenetictest_test');
    }

    public function down()
    {
        $this->dropTable('et_ophingenetictest_test_version');
    }
}
