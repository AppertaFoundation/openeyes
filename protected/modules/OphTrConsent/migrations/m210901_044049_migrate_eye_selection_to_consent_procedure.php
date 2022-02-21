<?php

class m210901_044049_migrate_eye_selection_to_consent_procedure extends OEMigration
{
    private $consent_addtional_procedure_assignment_tbl = 'ophtrconsent_procedure_add_procs_add_procs';
    private $consent_procedure_assignment_tbl = 'ophtrconsent_procedure_procedures_procedures';
    private $consent_procedure_element_tbl = 'et_ophtrconsent_procedure';
    public function up()
    {
        // add eye_id column to ophtrconsent_procedure_procedures_procedures
        $this->addOEColumn($this->consent_procedure_assignment_tbl, 'eye_id', 'int(10) unsigned NOT NULL AFTER element_id', true);
        // add eye_id column to ophtrconsent_procedure_add_procs_add_procs
        $this->addOEColumn($this->consent_addtional_procedure_assignment_tbl, 'eye_id', 'int(10) unsigned NOT NULL AFTER element_id', true);
        // migrate saved eye_id from et_ophtrconsent_procedure to ophtrconsent_procedure_procedures_procedures
        $consent_procedure_eles = $this->dbConnection->createCommand()
            ->select('*')
            ->from($this->consent_procedure_element_tbl)
            ->queryAll();
        foreach ($consent_procedure_eles as $ele) {
            if ($ele['eye_id']) {
                $this->update(
                    'ophtrconsent_procedure_procedures_procedures',
                    array('eye_id' => $ele['eye_id']),
                    'element_id = :element_id',
                    array(
                        ':element_id' => $ele['id']
                    )
                );
                $this->update(
                    'ophtrconsent_procedure_add_procs_add_procs',
                    array('eye_id' => $ele['eye_id']),
                    'element_id = :element_id',
                    array(
                        ':element_id' => $ele['id']
                    )
                );
            }
        }
        // drop the foreignkey to eye from element table
        $this->dropForeignKey('et_ophtrconsent_procedure_eye_id_fk', $this->consent_procedure_element_tbl);
        // drop the eye_id column from element table
        $this->dropOEColumn($this->consent_procedure_element_tbl, 'eye_id', true);
        // add the foreignkey to eye_id from ophtrconsent_procedure_procedures_procedures
        $this->addForeignKey('et_ophtrconsent_procedure_eye_id_fk', $this->consent_procedure_assignment_tbl, 'eye_id', 'eye', 'id');
        // add the foreignkey to eye_id from ophtrconsent_procedure_add_procs_add_procs
        $this->addForeignKey('ophtrconsent_addtional_procedure_eye_id_fk', $this->consent_addtional_procedure_assignment_tbl, 'eye_id', 'eye', 'id');
    }

    public function down()
    {
        echo "m210901_044049_migrate_eye_selection_to_consent_procedure does not support migration down.\n";
        return false;
    }
}
