<?php

class m140107_100553_fix_workflow_rule_table_name extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('ophciexamination_element_set_rule_created_user_id_fk', 'ophciexamination_element_set_rule');
        $this->dropForeignKey('ophciexamination_element_set_rule_last_modified_user_id_fk', 'ophciexamination_element_set_rule');
        $this->dropForeignKey('ophciexamination_element_set_rule_parent_id_fk', 'ophciexamination_element_set_rule');
        $this->dropForeignKey('ophciexamination_element_set_rule_workflow_id_fk', 'ophciexamination_element_set_rule');

        $this->dropIndex('ophciexamination_element_set_rule_created_user_id_fk', 'ophciexamination_element_set_rule');
        $this->dropIndex('ophciexamination_element_set_rule_last_modified_user_id_fk', 'ophciexamination_element_set_rule');
        $this->dropIndex('ophciexamination_element_set_rule_parent_id_fk', 'ophciexamination_element_set_rule');
        $this->dropIndex('ophciexamination_element_set_rule_workflow_id_fk', 'ophciexamination_element_set_rule');

        $this->renameTable('ophciexamination_element_set_rule', 'ophciexamination_workflow_rule');

        $this->addForeignKey('ophciexamination_workflow_rule_created_user_id_fk', 'ophciexamination_workflow_rule', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophciexamination_workflow_rule_last_modified_user_id_fk', 'ophciexamination_workflow_rule', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophciexamination_workflow_rule_parent_id_fk', 'ophciexamination_workflow_rule', 'parent_id', 'ophciexamination_workflow_rule', 'id');
        $this->addForeignKey('ophciexamination_workflow_rule_workflow_id_fk', 'ophciexamination_workflow_rule', 'workflow_id', 'ophciexamination_workflow', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('ophciexamination_workflow_rule_created_user_id_fk', 'ophciexamination_workflow_rule');
        $this->dropForeignKey('ophciexamination_workflow_rule_last_modified_user_id_fk', 'ophciexamination_workflow_rule');
        $this->dropForeignKey('ophciexamination_workflow_rule_parent_id_fk', 'ophciexamination_workflow_rule');
        $this->dropForeignKey('ophciexamination_workflow_rule_workflow_id_fk', 'ophciexamination_workflow_rule');

        $this->dropIndex('ophciexamination_workflow_rule_created_user_id_fk', 'ophciexamination_workflow_rule');
        $this->dropIndex('ophciexamination_workflow_rule_last_modified_user_id_fk', 'ophciexamination_workflow_rule');
        $this->dropIndex('ophciexamination_workflow_rule_parent_id_fk', 'ophciexamination_workflow_rule');
        $this->dropIndex('ophciexamination_workflow_rule_workflow_id_fk', 'ophciexamination_workflow_rule');

        $this->renameTable('ophciexamination_workflow_rule', 'ophciexamination_element_set_rule');

        $this->addForeignKey('ophciexamination_element_set_rule_created_user_id_fk', 'ophciexamination_element_set_rule', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophciexamination_element_set_rule_last_modified_user_id_fk', 'ophciexamination_element_set_rule', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophciexamination_element_set_rule_parent_id_fk', 'ophciexamination_element_set_rule', 'parent_id', 'ophciexamination_element_set_rule', 'id');
        $this->addForeignKey('ophciexamination_element_set_rule_workflow_id_fk', 'ophciexamination_element_set_rule', 'workflow_id', 'ophciexamination_workflow', 'id');
    }
}
