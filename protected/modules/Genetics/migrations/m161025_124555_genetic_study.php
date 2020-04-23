<?php

class m161025_124555_genetic_study extends OEMigration
{
    public function up()
    {
        $this->createOETable('genetics_study', array(
            'id' => 'pk',
            'name' => 'varchar(255) NOT NULL',
            'criteria' => 'text',
            'end_date' => 'datetime',
        ), true);

        $this->createTable('genetics_study_subject', array(
            'id' => 'pk',
            'study_id' => 'int(11)',
            'subject_id' => 'int(11)',
        ));
        $this->addForeignKey('genetics_study_subject_study', 'genetics_study_subject', 'study_id', 'genetics_study', 'id');
        $this->addForeignKey('genetics_study_subject_subject', 'genetics_study_subject', 'subject_id', 'genetics_patient', 'id');

        $this->createTable('genetics_study_proposer', array(
            'id' => 'pk',
            'study_id' => 'int(11)',
            'user_id' => 'int(10) unsigned',
        ));
        $this->addForeignKey('genetics_study_proposer_study', 'genetics_study_proposer', 'study_id', 'genetics_study', 'id');
        $this->addForeignKey('genetics_study_proposer_user', 'genetics_study_proposer', 'user_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropOETable('genetics_study', true);
        $this->dropOETable('genetics_study_patient');
        $this->dropOETable('genetics_study_subject');
    }
}
