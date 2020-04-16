<?php

class m191016_141643_change_result_type_to_longtext extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophinlabresults_result_timed_numeric', 'result', 'LONGTEXT');
        $this->alterColumn('et_ophinlabresults_result_timed_numeric_version', 'result', 'LONGTEXT');
    }

    public function down()
    {
        $this->alterColumn('et_ophinlabresults_result_timed_numeric', 'result', 'VARCHAR(255)');
        $this->alterColumn('et_ophinlabresults_result_timed_numeric_version', 'result', 'VARCHAR(255)');
    }
}
