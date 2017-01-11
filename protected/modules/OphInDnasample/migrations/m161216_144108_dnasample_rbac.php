<?php

class m161216_144108_dnasample_rbac extends CDbMigration
{
	public function up()
	{
        
        $this->delete('authitemchild', "parent = 'Genetics Admin' AND child = 'TaskEditBloodSample'");
        $this->delete('authitemchild', "parent = 'Genetics Admin' AND child = 'TaskViewBloodSample'");
        $this->delete('authitemchild', "parent = 'Genetics User' AND child = 'TaskViewBloodSample'");
        
        $this->delete('authitemchild', "parent = 'TaskEditBloodSample' AND child = 'OprnEditBloodSample'");
        $this->delete('authitemchild', "parent = 'TaskViewBloodSample' AND child = 'OprnViewBloodSample'");
        
        
        $this->update('authitem', array('name' => 'TaskEditDnaSample', 'type' => 1), "name = 'TaskEditBloodSample'");
        $this->update('authitem', array('name' => 'TaskViewDnaSample', 'type' => 1), "name = 'TaskViewBloodSample'");
        
        $this->update('authitem', array('name' => 'OprnViewDnaSample', 'type' => 0), "name = 'OprnViewBloodSample'");
        $this->update('authitem', array('name' => 'OprnEditDnaSample', 'type' => 0), "name = 'OprnEditBloodSample'");
        
        
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskEditDnaSample'));
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskViewDnaSample'));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskViewDnaSample'));
        
        $this->insert('authitemchild', array('parent' => 'TaskEditDnaSample', 'child' => 'OprnEditDnaSample'));
        $this->insert('authitemchild', array('parent' => 'TaskViewDnaSample', 'child' => 'OprnViewDnaSample'));
	}

	public function down()
	{
		return true;
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