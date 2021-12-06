<?php

class m170320_201259_remove_external_source extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('et_ophingenetictest_test_external_source_id', 'et_ophingeneticresults_test');
        $this->dropColumn('et_ophingeneticresults_test', 'external_source_id');
        $this->dropColumn('et_ophingeneticresults_test', 'external_source_identifier');

        $this->dropColumn('et_ophingeneticresults_test_version', 'external_source_id');
        $this->dropColumn('et_ophingeneticresults_test_version', 'external_source_identifier');

        $this->dropTable('ophingeneticresults_external_source');
    }

    public function down()
    {
        $this->createOETable(
            'ophingenetictest_external_source',
            array(
                'id' => 'pk',
                'name' => 'varchar(255)',
            )
        );

        $this->addColumn('et_ophingeneticresults_test', 'external_source_id', 'int(11)');
        $this->addColumn('et_ophingeneticresults_test', 'external_source_identifier', 'varchar(128)');

        $this->addColumn('et_ophingeneticresults_test_version', 'external_source_id', 'int(11)');
        $this->addColumn('et_ophingeneticresults_test_version', 'external_source_identifier', 'varchar(128)');

        $this->addForeignKey('et_ophingenetictest_test_external_source_id', 'et_ophingenetictest_test', 'external_source_id', 'ophingenetictest_external_source', 'id');
    }
}
