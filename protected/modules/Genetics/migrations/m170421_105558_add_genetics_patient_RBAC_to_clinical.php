<?php

class m170421_105558_add_genetics_patient_RBAC_to_clinical extends OEMigration
{
    public function up()
    {
        $this->removeTaskFromRole("TaskEditGeneticPatient", "Genetics Admin");
        $this->removeTaskFromRole("TaskEditPedigreeData", "Genetics Admin");

        $this->addTaskToRole("TaskEditGeneticPatient", "Genetics Clinical");
        $this->addTaskToRole("TaskEditPedigreeData", "Genetics Clinical");
    }

    public function down()
    {
        $this->addTaskToRole("TaskEditGeneticPatient", "Genetics Admin");
        $this->addTaskToRole("TaskEditPedigreeData", "Genetics Admin");

        $this->removeTaskFromRole("TaskEditGeneticPatient", "Genetics Clinical");
        $this->removeTaskFromRole("TaskEditPedigreeData", "Genetics Clinical");
    }
}
