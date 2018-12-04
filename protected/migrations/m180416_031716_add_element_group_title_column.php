<?php

class m180416_031716_add_element_group_title_column extends OEMigration
{
    public function safeUp()
    {
        //if the columns already exist, do nothing
        if (!isset(Yii::app()->db->schema->getTable('element_type')->columns['group_title'])) {
            // Add the group type column for labelling element groups without using the parent's name
            $this->addColumn('element_type', 'group_title', 'VARCHAR(255)');
        }
        if (!isset(Yii::app()->db->schema->getTable('element_type')->columns['group_title'])) {
            $this->addColumn('element_type_version', 'group_title', 'VARCHAR(255)');
        }
        // Set the group title to default to the element name
        $this->update('element_type', array('group_title' => new CDbExpression('name')),
            array('parent_element_type_id IS NOT NULL'));

    }

    public function safeDown()
    {
        if (isset(Yii::app()->db->schema->getTable('myTable')->columns['group_title'])) {
            $this->dropColumn('element_type_version', 'group_title');
            $this->dropColumn('element_type', 'group_title');
        }
    }
}