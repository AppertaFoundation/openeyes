<?php

class m131029_110215_fix_risk_classes extends CDbMigration
{
    public function up()
    {
        $this->update('ophciexamination_glaucomarisk_risk', array('class' => 'low'), "class='low_risk'");
        $this->update('ophciexamination_glaucomarisk_risk', array('class' => 'moderate'), "class='moderate_risk'");
        $this->update('ophciexamination_glaucomarisk_risk', array('class' => 'high'), "class='high_risk'");
    }

    public function down()
    {
        $this->update('ophciexamination_glaucomarisk_risk', array('class' => 'low_risk'), "class='low'");
        $this->update('ophciexamination_glaucomarisk_risk', array('class' => 'moderate_risk'), "class='moderate'");
        $this->update('ophciexamination_glaucomarisk_risk', array('class' => 'high_risk'), "class='high'");
    }
}
