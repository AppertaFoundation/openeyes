<?php

class m200813_050735_group_IOP_exam_elements extends OEMigration
{
    public function safeUp()
    {
        // First check if group_title column exists (for some reason it doesn't in the Cardiff database and possibly others)
        //if the columns already exist, do nothing
        if (!isset($this->dbConnection->schema->getTable('element_type')->columns['group_title'])) {
            // Add the group type column for labelling element groups without using the parent's name
            $this->addColumn('element_type', 'group_title', 'VARCHAR(255) AFTER `tile_size`');
        }
        // Check the version table separately - as in one DB, the column existed in version, but not the main table!
        if (!isset($this->dbConnection->schema->getTable('element_type')->columns['group_title'])) {
            $this->addColumn('element_type_version', 'group_title', 'VARCHAR(255) AFTER `tile_size`');
        }

        $IOPGroup = $this->dbConnection->createCommand('SELECT id FROM element_group WHERE name = "Intraocular Pressure"')->queryScalar();

        $this->update('element_type', array('element_group_id' => $IOPGroup, 'group_title' => 'Intraocular Pressure'), "name = 'IOP History'");
    }

    public function safeDown()
    {
        $IOPHistoryGroup = $this->dbConnection->createCommand('SELECT id FROM element_group WHERE name = "IOP History"')->queryScalar();

        $this->update('element_type', array('element_group_id' => $IOPHistoryGroup, 'group_title' => 'IOP History'), "name = 'IOP History'");
    }
}
