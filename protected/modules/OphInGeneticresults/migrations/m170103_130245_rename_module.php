<?php

class m170103_130245_rename_module extends OEMigration
{
	public function up()
	{
        
        if ($this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInGenetictest'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Investigation events'))->queryRow();
            $rowID = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInGenetictest'))->queryRow();
            $this->update('event_type', array('class_name' => 'OphInGeneticresults', 'name' => 'Genetic Results') , 'id = '.$rowID['id'].' AND event_group_id = '.$group['id']);
        }
        
        $this->update("element_type", array( "class_name" => "Element_OphInGeneticresults_Test"), "class_name = 'Element_OphInGenetictest_Test'");
        
        $this->renameTable('ophingenetictest_test_method', 'ophingeneticresults_test_method');
        $this->renameTable('ophingenetictest_test_effect', 'ophingeneticresults_test_effect');
        $this->renameTable('et_ophingenetictest_test', 'et_ophingeneticresults_test');
        
        $this->delete('authitemchild', "parent = 'Genetics Admin' AND child = 'TaskEditGeneticTest'");
        $this->delete('authitemchild', "parent = 'Genetics Admin' AND child = 'TaskViewGeneticTest'");
        $this->delete('authitemchild', "parent = 'Genetics User' AND child = 'TaskViewGeneticTest'");
        
        $this->delete('authitemchild', "parent = 'TaskEditGeneticTest' AND child = 'OprnEditGeneticTest'");
        $this->delete('authitemchild', "parent = 'TaskViewGeneticTest' AND child = 'OprnViewGeneticTest'");
        
        
        $this->update('authitem', array('name' => 'OprnViewGeneticResults', 'type' => 1), "name = 'OprnViewGeneticTest'");
        $this->update('authitem', array('name' => 'OprnEditGeneticResults', 'type' => 1), "name = 'OprnEditGeneticTest'");
      
        
        $this->update('authitem', array('name' => 'TaskEditGeneticResults', 'type' => 1), "name = 'TaskEditGeneticTest'");
        $this->update('authitem', array('name' => 'TaskViewGeneticResults', 'type' => 1), "name = 'TaskViewGeneticTest'");
        
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskEditGeneticResults'));
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskViewGeneticResults'));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskViewGeneticResults'));
        
        $this->insert('authitemchild', array('parent' => 'TaskEditGeneticResults', 'child' => 'OprnEditGeneticResults'));
        $this->insert('authitemchild', array('parent' => 'TaskViewGeneticResults', 'child' => 'OprnViewGeneticResults'));
        
        
        $this->setEventTypeRBACSuffix('OphInGeneticresults', 'GeneticResults');
        
        
        $this->delete('authitemchild', "parent = 'Genetics User' AND child = 'TaskCreateGeneticTest'");    
        $this->delete('authitemchild', "parent = 'TaskCreateGeneticTest' AND child = 'OprnCreateGeneticTest'");
        
        $this->update('authitem', array('name' => 'TaskCreateGeneticResults', 'type' => 1), "name = 'TaskCreateGeneticTest'");
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskCreateGeneticResults'));
        $this->update('authitem', array('name' => 'OprnCreateGeneticResults', 'type' => 0), "name = 'OprnCreateGeneticTest'");
        $this->insert('authitemchild', array('parent' => 'TaskCreateGeneticResults', 'child' => 'OprnCreateGeneticResults'));
       
        $this->renameTable('ophingenetictest_external_source', 'ophingeneticresults_external_source');
        
        $this->versionExistingTable('ophingeneticresults_test_method');
        $this->versionExistingTable('ophingeneticresults_test_effect');
        
        $this->renameTable('et_ophingenetictest_test_version', 'et_ophingeneticresults_test_version');
	}

	public function down()
	{
		
		if ($this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInGeneticresults'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Investigation events'))->queryRow();
            $rowID = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInGeneticresults'))->queryRow();
            $this->update('event_type', array('class_name' => 'OphInGenetictest', 'name' => 'Genetic Results') , 'id = '.$rowID['id'].' AND event_group_id = '.$group['id']);
        }
        
        $this->update("element_type", array( "class_name" => "Element_OphInGenetictest_Test"), "class_name = 'Element_OphInGeneticresults_Test'");
        
        $this->renameTable('ophingeneticresults_test_method', 'ophingenetictest_test_method');
        $this->renameTable('ophingeneticresults_test_effect', 'ophingenetictest_test_effect');
        $this->renameTable('et_ophingeneticresults_test', 'et_ophingenetictest_test');
        
        $this->delete('authitemchild', "parent = 'Genetics Admin' AND child = 'TaskEditGeneticResults'");
        $this->delete('authitemchild', "parent = 'Genetics Admin' AND child = 'TaskViewGeneticResults'");
        $this->delete('authitemchild', "parent = 'Genetics User' AND child = 'TaskViewGeneticResults'");
        
        $this->delete('authitemchild', "parent = 'TaskEditGeneticResults' AND child = 'OprnEditGeneticResults'");
        $this->delete('authitemchild', "parent = 'TaskViewGeneticResults' AND child = 'OprnViewGeneticResults'");
        
        $this->update('authitem', array('name' => 'OprnViewGeneticTest', 'type' => 1), "name = 'OprnViewGeneticResults'");
        $this->update('authitem', array('name' => 'OprnEditGeneticTest', 'type' => 1), "name = 'OprnEditGeneticResults'");
        
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskEditGeneticTest'));
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskViewGeneticTest'));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskViewGeneticTest'));
        
        $this->insert('authitemchild', array('parent' => 'TaskEditGeneticTest', 'child' => 'OprnEditGeneticTest'));
        $this->insert('authitemchild', array('parent' => 'TaskViewGeneticTest', 'child' => 'OprnViewGeneticTest'));
        
        $this->setEventTypeRBACSuffix('OphInGenetictest', 'GeneticTest');
        
        $this->delete('authitemchild', "parent = 'Genetics User' AND child = 'TaskCreateGeneticResults'");    
        $this->delete('authitemchild', "parent = 'TaskCreateGeneticResults' AND child = 'OprnCreateGeneticResults'");
        
        $this->update('authitem', array('name' => 'TaskCreateGeneticTest', 'type' => 1), "name = 'TaskCreateGeneticResults'");
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskCreateGeneticTest'));
        $this->update('authitem', array('name' => 'OprnCreateGeneticTest', 'type' => 0), "name = 'OprnCreateGeneticResults'");
        $this->insert('authitemchild', array('parent' => 'TaskCreateGeneticTest', 'child' => 'OprnCreateGeneticTest'));
        
        $this->renameTable('ophingeneticresults_external_source', 'ophingenetictest_external_source');
        
        $this->versionExistingTable('ophingenetictest_test_method');
        $this->versionExistingTable('ophingenetictest_test_effect');
        
        $this->renameTable('et_ophingeneticresults_test_version', 'et_ophingenetictest_test_version');
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