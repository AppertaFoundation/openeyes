<?php

class m190808_045047_create_cat_prom5_answer_results extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('cat_prom5_answer_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11) NOT NULL',
            'answer_id' => 'int(11) NOT NULL',
        ), true);
        $this->addForeignKey('cat_prom5_results_fk_ques', 'cat_prom5_answer_results', 'question_id', 'cat_prom5_questions', 'id');
        $this->addForeignKey('cat_prom5_results_fk_ans', 'cat_prom5_answer_results', 'answer_id', 'cat_prom5_answers', 'id');
        $this->addForeignKey('cat_prom5_results_fk_ele', 'cat_prom5_answer_results', 'element_id', 'cat_prom5_event_result', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('cat_prom5_results_fk_ques', 'cat_prom5_answer_results');
        $this->dropForeignKey('cat_prom5_results_fk_ans', 'cat_prom5_answer_results');
        $this->dropForeignKey('cat_prom5_results_fk_ele', 'cat_prom5_answer_results');
        $this->dropOETable('cat_prom5_answer_results', true);
    }
}
