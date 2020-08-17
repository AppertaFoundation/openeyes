<?php

class m200813_050735_group_IOP_exam_elements extends OEMigration
{
    public function safeUp()
    {
        $IOPGroup = $this->dbConnection->createCommand('SELECT id FROM element_group WHERE name = "Intraocular Pressure"')->queryScalar();

        $this->update('element_type', array('element_group_id' => $IOPGroup, 'group_title' => 'Intraocular Pressure'), "name = 'IOP History'");
    }

    public function safeDown()
    {
        $IOPHistoryGroup = $this->dbConnection->createCommand('SELECT id FROM element_group WHERE name = "IOP History"')->queryScalar();

        $this->update('element_type', array('element_group_id' => $IOPHistoryGroup, 'group_title' => 'IOP History'), "name = 'IOP History'");
    }
}
