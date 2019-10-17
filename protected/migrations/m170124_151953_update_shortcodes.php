<?php

class m170124_151953_update_shortcodes extends CDbMigration
{
    public function up()
    {
        $this->update('patient_shortcode', array('description'=>'Patient as object (him/her)'), 'default_code="obj"');
        $this->update('patient_shortcode', array('description'=>'Patient possessive (his/hers)'), 'default_code="pos"');
        $this->update('patient_shortcode', array('description'=>'Patient as subject (man/woman)'), 'default_code="sub"');
        $this->update('patient_shortcode', array('description'=>'Patient pronoun (he/she)'), 'default_code="pro"');
        $this->update('patient_shortcode', array('description'=>'Patient Unique Code for portal'), 'default_code="puc"');
    }

    public function down()
    {
        $this->update('patient_shortcode', array('description'=>'Patient as object'), 'default_code="obj"');
        $this->update('patient_shortcode', array('description'=>'Patient possessive'), 'default_code="pos"');
        $this->update('patient_shortcode', array('description'=>'Patient as subject'), 'default_code="sub"');
        $this->update('patient_shortcode', array('description'=>'Patient pronoun'), 'default_code="pro"');
        $this->update('patient_shortcode', array('description'=>'Patient Unique Code'), 'default_code="puc"');
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