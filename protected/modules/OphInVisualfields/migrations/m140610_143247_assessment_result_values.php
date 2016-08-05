<?php

class m140610_143247_assessment_result_values extends CDbMigration
{
    public function up()
    {
        $this->insert('ophinvisualfields_result_assessment', array('id' => 1, 'active' => 1, 'name' => 'Nasal step'));
        $this->insert('ophinvisualfields_result_assessment', array('id' => 2, 'active' => 1, 'name' => 'Arcuate defect'));
        $this->insert('ophinvisualfields_result_assessment', array('id' => 3, 'active' => 1, 'name' => 'Paracentral defect'));
        $this->insert('ophinvisualfields_result_assessment', array('id' => 4, 'active' => 1, 'name' => 'Hemianopic defect'));
        $this->insert('ophinvisualfields_result_assessment', array('id' => 5, 'active' => 1, 'name' => 'Bitemporal defect'));
        $this->insert('ophinvisualfields_result_assessment', array('id' => 6, 'active' => 1, 'name' => 'Homonymous hemianopia'));
        $this->insert('ophinvisualfields_result_assessment', array('id' => 7, 'active' => 1, 'name' => 'Other'));
    }

    public function down()
    {
        $this->delete('ophinvisualfields_result_assessment');
    }
}
