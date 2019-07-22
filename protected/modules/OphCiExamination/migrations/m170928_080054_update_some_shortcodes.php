<?php

class m170928_080054_update_some_shortcodes extends OEMigration
{
    public function up()
    {
        $this->update(
            'patient_shortcode',
            array(
                'method' => 'getLetterLaserManagementFindings',
                'description' => 'Laser Management Findings'),
            'method = :orig_method',
            array(':orig_method' => 'getLetterLaserManagementPlan'));

        $this->update(
            'patient_shortcode',
            array(
                'event_type_id' => $this->getIdOfEventTypeByClassName('OphCiExamination')
            ),
            'method = :orig_method',
            array(':orig_method' => 'getGlaucomaManagement')
        );

    }

    public function down()
    {
        if ($correspondence_event_id = $this->getIdOfEventTypeByClassName('OphCoCorrespondence'))
        $this->update(
            'patient_shortcode',
            array(
                'event_type_id' => $correspondence_event_id
            ),
            'method = ?', array('getGlaucomaManagement')
        );

        $this->update(
            'patient_shortcode',
            array(
                'method' => 'getLetterLaserManagementPlan',
                'description' => 'Laser Management Plan'),
            'method = ?', array('getLetterLaserManagementFindings'));
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}