<?php

class m170306_131710_drop_prime_fields extends OEMigration
{
    public function up()
    {
        $this->dropColumn('et_ophingeneticresults_test', 'prime_rf');
        $this->dropColumn('et_ophingeneticresults_test_version', 'prime_rf');

        $this->dropColumn('et_ophingeneticresults_test', 'prime_rr');
        $this->dropColumn('et_ophingeneticresults_test_version', 'prime_rr');
    }

    public function down()
    {
        $this->addColumn('et_ophingeneticresults_test', 'prime_rf', 'varchar(64) COLLATE utf8_bin DEFAULT NULL');
        $this->addColumn('et_ophingeneticresults_test_version', 'prime_rf', 'varchar(64) COLLATE utf8_bin DEFAULT NULL');

        $this->addColumn('et_ophingeneticresults_test', 'prime_rr', 'varchar(64) COLLATE utf8_bin DEFAULT NULL');
        $this->addColumn('et_ophingeneticresults_test_version', 'prime_rr', 'varchar(64) COLLATE utf8_bin DEFAULT NULL');
    }
}