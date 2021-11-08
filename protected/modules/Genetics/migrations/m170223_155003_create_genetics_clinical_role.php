<?php

class m170223_155003_create_genetics_clinical_role extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
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

        $this->delete('authitemchild', "parent = 'Genetics User' AND child = 'TaskCreateGeneticTest'");
        $this->delete('authitemchild', "parent = 'TaskCreateGeneticTest' AND child = 'OprnCreateGeneticTest'");

        $this->update('authitem', array('name' => 'TaskCreateGeneticResults', 'type' => 1), "name = 'TaskCreateGeneticTest'");
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskCreateGeneticResults'));
        $this->update('authitem', array('name' => 'OprnCreateGeneticResults', 'type' => 0), "name = 'OprnCreateGeneticTest'");
        $this->insert('authitemchild', array('parent' => 'TaskCreateGeneticResults', 'child' => 'OprnCreateGeneticResults'));


        //New role required
        $this->addRole("Genetics Clinical");

        //Remove all Genetics Admin tasks as it will inherit from other roles
        $this->delete("authitemchild", "parent = 'Genetics Admin'");

        $this->addTaskToRole('TaskEditGeneticsWithdrawals', 'Genetics Laboratory Technician');
        $this->addTaskToRole("Genetics Laboratory Technician", "Genetics Admin");
        $this->addTaskToRole("Genetics Clinical", "Genetics Admin");
        $this->addTaskToRole("Genetics User", "Genetics Laboratory Technician");
        $this->addTaskToRole("Genetics User", "Genetics Clinical");

        $this->addTask("TaskEditGeneticStudy");
        $this->addTaskToRole("Genetics Admin", "TaskEditGeneticStudy");

        // User has only read access
        $this->delete("authitemchild", "parent = 'Genetics User' AND child = 'TaskCreateDnaExtraction'");
        $this->delete("authitemchild", "parent = 'Genetics User' AND child = 'TaskCreateDnaSample'");
        $this->delete("authitemchild", "parent = 'Genetics User' AND child = 'TaskCreateGeneticResults'");

        $this->addTaskToRole("TaskCreateDnaSample", "Genetics Clinical");
        $this->addTaskToRole("TaskCreateGeneticResults", "Genetics Clinical");

        $this->addTaskToRole("TaskEditGeneticResults", "Genetics Clinical");
        $this->addTaskToRole("TaskEditDnaSample", "Genetics Clinical");

        //genes
        $this->addTaskToRole("TaskEditGeneData", "Genetics Admin");

        $this->addTaskToRole("TaskEditGeneticPatient", "Genetics Admin");

        //missing FK
        $this->addForeignKey('genetics_patient_relationship_ibfk_1', 'genetics_patient_relationship', 'patient_id', 'genetics_patient', 'id');
        $this->addForeignKey('genetics_patient_relationship_ibfk_2', 'genetics_patient_relationship', 'relationship_id', 'genetics_relationship', 'id');
        $this->addForeignKey('genetics_patient_relationship_ibfk_4', 'genetics_patient_relationship', 'related_to_id', 'genetics_relationship', 'id');
    }

    public function safeDown()
    {
    }
}
