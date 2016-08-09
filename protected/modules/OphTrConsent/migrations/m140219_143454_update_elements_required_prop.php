<?php

class m140219_143454_update_elements_required_prop extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrConsent_Type'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrConsent_Procedure'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrConsent_BenefitsAndRisks'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrConsent_Permissions'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrConsent_Other'");
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphTrConsent_Leaflets'");
    }

    public function down()
    {
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrConsent_Type'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrConsent_Procedure'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrConsent_BenefitsAndRisks'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrConsent_Permissions'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrConsent_Other'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphTrConsent_Leaflets'");
    }
}
