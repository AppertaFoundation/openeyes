<?php

class m190719_070222_create_cat_prom5_questions_table extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Creating Table
        $this->createOETable('cat_prom5_questions', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'question' => 'varchar(65535) NOT NULL',
            'mandatory' => 'boolean NOT NULL DEFAULT false',
            'display_order' => 'int unsigned NOT NULL'
        ));

        // Inserting values
        $this->insert('cat_prom5_questions', array('question' => 'In the past month, have you felt that your bad eye is affecting or interfering with your vision overall?', 'mandatory' => true, 'display_order' => 10));

        $this->insert('cat_prom5_questions', array('question' => 'In the past month, how much has your eyesight interfered with your life in general?', 'mandatory' => true, 'display_order' => 20));

        $this->insert('cat_prom5_questions', array('question' => 'How would you describe your vision overall in the past month – with both eyes open, wearing glasses or contact lenses if you usually do?', 'mandatory' => true, 'display_order' => 30));

        $this->insert('cat_prom5_questions', array('question' => 'In the past month, how often has your eyesight prevented you from doing the things you would like to do?', 'mandatory' => true, 'display_order' => 40));

        $this->insert('cat_prom5_questions', array('question' => 'In the past month, have you had difficulty reading normal print in books or newspapers because of trouble with your eyesight?', 'mandatory' => true, 'display_order' => 50));

        $this->insert('cat_prom5_questions', array('question' => 'Please tell us who actually gave the answers to the questions and who wrote them down', 'mandatory' => true, 'display_order' => 60));

    }

    public function safeDown()
    {
        $this->dropOETable('cat_prom5_questions');
    }
}