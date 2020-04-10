<?php

class m190501_093905_change_lab_results_value_to_text_from_number extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophinlabresults_result_timed_numeric', 'result' , 'VARCHAR(255)');
        $this->alterColumn('et_ophinlabresults_result_timed_numeric_version', 'result' , 'VARCHAR(255)');
    }

    public function down()
    {
        $this->alterColumn('et_ophinlabresults_result_timed_numeric', 'result' , 'float');
        $this->alterColumn('et_ophinlabresults_result_timed_numeric_version', 'result' , 'float');
    }
}
