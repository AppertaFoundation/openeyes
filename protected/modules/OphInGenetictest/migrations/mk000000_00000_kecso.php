<?php

class m140917_133025_event_create_rbac extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophingenetictest_test', 'dna_quality', 'FLOAT NULL AFTER exon');
        $this->addColumn('et_ophingenetictest_test', 'dna_quantity', 'FLOAT NULL AFTER dna_quality');
    }

    public function down()
    {
    }
}
