<?php

class m161221_113237_create_external_source_id extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophingenetictest_test', 'external_source_identifier', 'varchar(128)');
    }

    public function down()
    {
        $this->dropColumn('et_ophingenetictest_test', 'external_source_identifier');
    }
}
