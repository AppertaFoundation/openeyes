<?php

class m190808_045047_create_cat_prom5_answer_results extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('cat_prom5_answer_results', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'answer_id' => 'int(10) unsigned',
        ),true);
        $this->addForeignKey('cat_prom5_results_fk_ans','cat_prom5_answer_results','answer_id','cat_prom5_answers','id');
         $this->addForeignKey('cat_prom5_results_fk_eve','cat_prom5_answer_results','event_id','event','id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('cat_prom5_results_fk_ans','cat_prom5_answer_results');
        $this->dropForeignKey('cat_prom5_results_fk_eve','cat_prom5_answer_results');
        $this->dropOETable('cat_prom5_answer_results',true);
    }
}