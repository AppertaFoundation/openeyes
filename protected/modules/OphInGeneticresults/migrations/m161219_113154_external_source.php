<?php

class m161219_113154_external_source extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophingenetictest_external_source',
            array(
                'id' => 'pk',
                'name' => 'varchar(255)',
            )
        );

        $this->addColumn('et_ophingenetictest_test', 'external_source_id', 'int(11)');
        $this->addForeignKey('et_ophingenetictest_test_external_source_id', 'et_ophingenetictest_test', 'external_source_id', 'ophingenetictest_external_source', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophingenetictest_test_external_source_id', 'et_ophingenetictest_test');
        $this->dropColumn('et_ophingenetictest_test', 'external_source_id');
        $this->dropOETable('ophingenetictest_external_source');
    }
}
