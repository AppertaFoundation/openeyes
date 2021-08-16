<?php

class m161107_131809_rejectable_study_participation extends OEMigration
{
    public function up()
    {
        $statuses = array(
            'Approved',
            'Rejected',
        );

        $this->createOETable('study_participation_status', array(
            'id' => 'pk',
            'status' => 'varchar(50)',
        ));

        foreach ($statuses as $status) {
            $this->insert('study_participation_status', array('status' => $status));
        }

        $this->addColumn('genetics_study_subject', 'participation_status_id', 'int(11)');
        $this->addForeignKey('genetics_study_subject_participation_status', 'genetics_study_subject', 'participation_status_id', 'study_participation_status', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('genetics_study_subject_participation_status', 'genetics_study_subject');
        $this->dropColumn('genetics_study_subject', 'participation_status_id');
        $this->dropOETable('study_participation_status');
    }
}
