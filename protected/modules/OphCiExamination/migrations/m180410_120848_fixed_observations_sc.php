<?php

class m180410_120848_fixed_observations_sc extends CDbMigration
{
    public function up()
    {
        $this->update('patient_shortcode', array('default_code' => 'lst' , 'code' => 'lst' ), "default_code = 'lo2' AND code = 'lo2'");
        $this->update('patient_shortcode', array('default_code' => 'lhb' , 'code' => 'lhb'), "default_code = 'lh1' AND code = 'lh1'");
    }

    public function down()
    {
        $this->update('patient_shortcode', array('default_code' => 'lo2', 'code' => 'lo2'), "default_code = 'lst' AND code = 'lst'");
        $this->update('patient_shortcode', array('default_code' => 'lh1', 'code' => 'lh1'), "default_code = 'lhb' AND code = 'lhb'");
    }
}
