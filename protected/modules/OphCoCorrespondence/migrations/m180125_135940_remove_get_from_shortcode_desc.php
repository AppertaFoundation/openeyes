<?php

class m180125_135940_remove_get_from_shortcode_desc extends CDbMigration
{
    public function up()
    {
        $this->update('patient_shortcode', array('description' => 'Date of Last Examination'), 'default_code="eld"');
        $this->update('patient_shortcode', array('description' => 'Full Patient Name'), 'default_code="pna"');
        $this->update('patient_shortcode', array('description' => 'Patient First Name'), 'default_code="pnf"');
        $this->update('patient_shortcode', array('description' => 'Patient Last Name'), 'default_code="pnl"');
        $this->update('patient_shortcode', array('description' => 'Patient Title'), 'default_code="pnt"');
        $this->update('patient_shortcode', array('description' => 'Most recent Glaucoma Risk value for the patient'), 'default_code="glr"');
    }

    public function down()
    {
        $this->update('patient_shortcode', array('description' => 'Get Date of Last Examination Date'), 'default_code="eld"');
        $this->update('patient_shortcode', array('description' => 'Get Full Patient Name'), 'default_code="pna"');
        $this->update('patient_shortcode', array('description' => 'Get Patient First Name'), 'default_code="pnf"');
        $this->update('patient_shortcode', array('description' => 'Get Patient Last Name'), 'default_code="pnl"');
        $this->update('patient_shortcode', array('description' => 'Get Patient Title'), 'default_code="pnt"');
        $this->update('patient_shortcode', array('description' => 'Get the most recent Glaucoma Risk value for the patient'), 'default_code="glr"');
    }
}
