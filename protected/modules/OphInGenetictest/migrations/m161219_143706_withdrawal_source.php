<?php

class m161219_143706_withdrawal_source extends CDbMigration
{
    public function up()
    {
        //@todo what if related table doesn't exist yet? Annoying modules and migration order.
        $this->addColumn('et_ophingenetictest_test', 'withdrawal_source_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophingenetictest_test_withdrawal_source_id', 'et_ophingenetictest_test', 'withdrawal_source_id', 'et_ophindnaextraction_dnatests', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophingenetictest_test_withdrawal_source_id', 'et_ophingenetictest_test');
        $this->dropColumn('et_ophingenetictest_test', 'withdrawal_source_id');
    }
}