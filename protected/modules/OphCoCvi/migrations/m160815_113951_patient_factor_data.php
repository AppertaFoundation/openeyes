<?php

class m160815_113951_patient_factor_data extends CDbMigration
{

    public function up()
    {
        $this->insert('ophcocvi_clinicinfo_patient_factor', array('name' => 'Does the patient live alone?', 'code' => 'PF1', 'display_order' => 1));
        $this->insert('ophcocvi_clinicinfo_patient_factor', array('name' => 'Does the patient also have a hearing impairment', 'code' => 'PF2', 'display_order' => 2));
        $this->insert('ophcocvi_clinicinfo_patient_factor', array('name' => 'Does the patient have poor physical mobility?', 'code' => 'PF3', 'display_order' => 3));
        $this->insert('ophcocvi_clinicinfo_patient_factor', array('name' => 'Does the patient have any other medical conditions that may be relevant? ', 'require_comments' => '1', 'comments_label' => 'Please specify', 'code' => 'PF4', 'display_order' => 4));
        $this->insert('ophcocvi_clinicinfo_patient_factor', array('name' => 'Are there any concerns about how the sight problem or the prospect of registration is affecting the patient emotionally?', 'require_comments' => '1', 'comments_label' => 'Please make a note of the concerns here:', 'code' => 'PF5', 'display_order' => 5));
        $this->insert('ophcocvi_clinicinfo_patient_factor', array('name' => 'Would the patient benefit from a discussion with a rehabilitation worker about practical matters such as mobility?', 'code' => 'PF6', 'display_order' => 6));
        $this->insert('ophcocvi_clinicinfo_patient_factor', array('name' => 'Would the patient benefit from a discussion with a rehabilitation worker about practical matters such as daily living skills?', 'code' => 'PF7', 'display_order' => 7));
        $this->insert('ophcocvi_clinicinfo_patient_factor', array('name' => 'Would the patient benefit from a discussion with a rehabilitation worker about practical matters such as employment?', 'code' => 'PF8', 'display_order' => 8));
        $this->insert('ophcocvi_clinicinfo_patient_factor', array('name' => 'In the case of a child, would the parent/s or guardian/s welcome guidance about child development, schooling, social implications or parenting?', 'code' => 'PF9', 'display_order' => 9));
    }

    public function down()
    {
		$this->truncateTable('ophcocvi_clinicinfo_patient_factor');
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
