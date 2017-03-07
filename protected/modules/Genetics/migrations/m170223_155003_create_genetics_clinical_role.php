<?php

class m170223_155003_create_genetics_clinical_role extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        //New role required
        $this->addRole("Genetics Clinical");
        
        //Remove all Genetics Admin tasks as it will inherit from other roles
        $this->delete("authitemchild", "parent = 'Genetics Admin'");
        
        $this->addTaskToRole('TaskEditGeneticsWithdrawals', 'Genetics Laboratory Technician');
        $this->addTaskToRole("Genetics Laboratory Technician","Genetics Admin");
        $this->addTaskToRole("Genetics Clinical","Genetics Admin");
        $this->addTaskToRole("Genetics User","Genetics Laboratory Technician");
        $this->addTaskToRole("Genetics User","Genetics Clinical");
        $this->addTaskToRole("TaskViewGeneticStudy","Genetics User");
        
        $this->addTask("TaskEditGeneticStudy");
        $this->addTaskToRole("Genetics Admin","TaskEditGeneticStudy");
        
        // User has only read access
        $this->delete("authitemchild", "parent = 'Genetics User' AND child = 'TaskCreateDnaExtraction'");
        $this->delete("authitemchild", "parent = 'Genetics User' AND child = 'TaskCreateDnaSample'");
        $this->delete("authitemchild", "parent = 'Genetics User' AND child = 'TaskCreateGeneticResults'");
        
        // Not used any longer
        $this->delete("authitemchild", "parent = 'Genetics User' AND child = 'TaskViewGeneticStudy'");
        
        $this->addTaskToRole("TaskCreateDnaSample","Genetics Clinical");
        $this->addTaskToRole("TaskCreateGeneticResults","Genetics Clinical");
        
        $this->addTaskToRole("TaskEditGeneticResults","Genetics Clinical");
        $this->addTaskToRole("TaskEditDnaSample","Genetics Clinical");
        
        //genes
        $this->addTaskToRole("TaskEditGeneData","Genetics Admin");
        
        $this->addTaskToRole("TaskEditGeneticPatient","Genetics Admin");
        
        //missing FK
        $this->addForeignKey('genetics_patient_relationship_ibfk_1', 'genetics_patient_relationship', 'patient_id', 'genetics_patient', 'id');
        $this->addForeignKey('genetics_patient_relationship_ibfk_2', 'genetics_patient_relationship', 'relationship_id', 'genetics_relationship', 'id');
        $this->addForeignKey('genetics_patient_relationship_ibfk_4', 'genetics_patient_relationship', 'related_to_id', 'genetics_relationship', 'id');
    }

    public function safeDown()
    {
    }
	
}