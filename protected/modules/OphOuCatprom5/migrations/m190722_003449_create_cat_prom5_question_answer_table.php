<?php

class m190722_003449_create_cat_prom5_question_answer_table extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createOETable('cat_prom5_answers', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'question_id' => 'int(10) unsigned NOT NULL',
            'answer' => 'varchar(65535) NOT NULL',
            'score' => 'int unsigned NOT NULL DEFAULT 0',
        ));
        $this->addForeignKey('cat_prom5_answers_qid_fk','cat_prom5_answers','question_id','cat_prom5_questions','id');

        // Question 1
        $this->insert('cat_prom5_answers', array('question_id' => 1, 'answer' => 'No, never', 'score' => 0));

        $this->insert('cat_prom5_answers', array('question_id' => 1, 'answer' => 'Yes, some of the time', 'score' => 1));

        $this->insert('cat_prom5_answers', array('question_id' => 1, 'answer' => 'Yes, most of the time', 'score' => 2));

        $this->insert('cat_prom5_answers', array('question_id' => 1, 'answer' => 'Yes, all of the time', 'score' => 3));

        // Question 2

        $this->insert('cat_prom5_answers', array('question_id' => 2, 'answer' => 'Not at all', 'score' => 0));

        $this->insert('cat_prom5_answers', array('question_id' => 2, 'answer' => 'Hardly at all', 'score' => 1));

        $this->insert('cat_prom5_answers', array('question_id' => 2, 'answer' => 'A little', 'score' => 2));

        $this->insert('cat_prom5_answers', array('question_id' => 2, 'answer' => 'A fair amount', 'score' => 3));

        $this->insert('cat_prom5_answers', array('question_id' => 2, 'answer' => 'A lot', 'score' => 4));

        $this->insert('cat_prom5_answers', array('question_id' => 2, 'answer' => 'An extremely large amount', 'score' => 5));

        // Question 3

        $this->insert('cat_prom5_answers', array('question_id' => 3, 'answer' => 'Excellent', 'score' => 0));

        $this->insert('cat_prom5_answers', array('question_id' => 3, 'answer' => 'Very good', 'score' => 1));

        $this->insert('cat_prom5_answers', array('question_id' => 3, 'answer' => 'Quite good', 'score' => 2));

        $this->insert('cat_prom5_answers', array('question_id' => 3, 'answer' => 'Average', 'score' => 3));

        $this->insert('cat_prom5_answers', array('question_id' => 3, 'answer' => 'Quite poor', 'score' => 4));

        $this->insert('cat_prom5_answers', array('question_id' => 3, 'answer' => 'Very poor', 'score' => 5));

        $this->insert('cat_prom5_answers', array('question_id' => 3, 'answer' => 'Appalling', 'score' => 6));

        // Question 4

        $this->insert('cat_prom5_answers', array('question_id' => 4, 'answer' => 'Never', 'score' => 0));

        $this->insert('cat_prom5_answers', array('question_id' => 4, 'answer' => 'Some of the time', 'score' => 1));

        $this->insert('cat_prom5_answers', array('question_id' => 4, 'answer' => 'Most of the time', 'score' => 2));

        $this->insert('cat_prom5_answers', array('question_id' => 4, 'answer' => 'All of the time', 'score' => 3));

        // Question 5

        $this->insert('cat_prom5_answers', array('question_id' => 5, 'answer' => 'No difficulty', 'score' => 0));

        $this->insert('cat_prom5_answers', array('question_id' => 5, 'answer' => 'Yes, a little difficulty', 'score' => 1));

        $this->insert('cat_prom5_answers', array('question_id' => 5, 'answer' => 'Yes, some difficulty', 'score' => 2));

        $this->insert('cat_prom5_answers', array('question_id' => 5, 'answer' => 'Yes, a great deal of difficulty', 'score' => 3));

        $this->insert('cat_prom5_answers', array('question_id' => 5, 'answer' => 'I cannot read any more because of my eyesight', 'score' => 4));

        //Question 6

        $this->insert('cat_prom5_answers', array('question_id' => 6, 'answer' => 'I gave all the answers and wrote them down myself', 'score' => 0));

        $this->insert('cat_prom5_answers', array('question_id' => 6, 'answer' => 'I gave all the answers and someone else wrote them down as I spoke', 'score' => 0));

        $this->insert('cat_prom5_answers', array('question_id' => 6, 'answer' => 'A friend or relative gave some of the answers on my behalf', 'score' => 0));
    }

    public function safeDown()
    {
        $this->dropForeignKey('cat_prom5_answers_qid_fk','cat_prom5_answers');

        $this->dropOETable('cat_prom5_answers');
    }
}