<?php

class m190424_131625_add_type_to_lab_results_result_timed_numeric extends CDbMigration
{
    public function up()
    {
        $default_result_type = $this->dbConnection->createCommand()->select('id')->from('ophinlabresults_type')->where('type = :type', [':type' => 'INR'])->queryScalar();

        $this->addColumn(
            'et_ophinlabresults_result_timed_numeric',
            'type',
            'int(11) NOT NULL DEFAULT ' . $default_result_type
        );
        $this->addColumn(
            'et_ophinlabresults_result_timed_numeric_version',
            'type',
            'int(11) unsigned NOT NULL DEFAULT ' . $default_result_type
        );
        $this->addForeignKey(
            'lab_results_fk_lab_results_type',
            'et_ophinlabresults_result_timed_numeric',
            'type',
            'ophinlabresults_type',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('lab_results_fk_lab_results_type', 'et_ophinlabresults_result_timed_numeric');
        $this->dropColumn('et_ophinlabresults_result_timed_numeric_version', 'type');
        $this->dropColumn('et_ophinlabresults_result_timed_numeric', 'type');
    }
}
