<?php

class m211104_102720_migrate_extra_procedures extends OEMigration
{

    protected const EXTRA_PROCEDURE_ET = "et_ophtrconsent_extraprocedure";
    //protected const EXTRA_PROCEDURE_ASSIGN = "et_ophtrconsent_extraprocedure";

    protected function addExtraProcedureElement($element)
    {
        ob_start();
        $this->insert("et_ophtrconsent_extraprocedure", [
            'event_id' => $element['event_id'],
            'last_modified_user_id' => $element['last_modified_user_id'],
            'last_modified_date' => $element['last_modified_date'],
            'created_user_id' => $element['created_user_id'],
            'created_date' => $element['created_date']
        ]);
        ob_clean();
        return Yii::app()->db->getLastInsertID();
    }

    protected function addAdditionalProcedures($new_extra_procedure_element_id,$old_extra_procedure_element_id)
    {
        $this->execute("INSERT INTO ophtrconsent_procedure_extra_assignment 
            (
                element_id,
                extra_proc_id,
                last_modified_user_id,
                last_modified_date,
                created_user_id,
                created_date
            )( 
            SELECT
                {$new_extra_procedure_element_id} as element_id,
                procedure_id as extra_proc_id,
                last_modified_user_id,
                last_modified_date,
                created_user_id,
                created_date
            FROM ophtrconsent_extra_procedure ep
            WHERE ep.element_id = {$old_extra_procedure_element_id} 
            );
        ");
    }

    protected function addExtraProcedures($new_extra_procedure_element_id,$old_extra_procedure_element_id)
    {
        $this->execute("INSERT INTO ophtrconsent_procedure_extra_assignment 
            (
                element_id,
                extra_proc_id,
                last_modified_user_id,
                last_modified_date,
                created_user_id,
                created_date
            )( 
            SELECT
                {$new_extra_procedure_element_id} as element_id,
                proc_id as extra_proc_id,
                last_modified_user_id,
                last_modified_date,
                created_user_id,
                created_date
            FROM ophtrconsent_procedure_add_procs_add_procs ap
            WHERE ap.element_id = {$old_extra_procedure_element_id} 
            );
        ");
    }

    public function safeUp()
    {
        $this->setVerbose(false);
        if ($this->dbConnection->schema->getTable('ophtrconsent_extra_procedure')) {
            $extra_procedure_elements = $this->dbConnection->createCommand("SELECT * FROM et_ophtrconsent_procedure")->queryAll();

            echo "  Migrate " . count($extra_procedure_elements) . " 'extra procedure element'..." . PHP_EOL;
            foreach ($extra_procedure_elements as $element) {
                $new_extra_procedure_element_id = $this->addExtraProcedureElement($element);
                $old_extra_procedure_element_id = $element['id'];

                $this->addAdditionalProcedures($new_extra_procedure_element_id,$old_extra_procedure_element_id);
                $this->addExtraProcedures($new_extra_procedure_element_id,$old_extra_procedure_element_id);
            }
        }
        die('INTERRUPT');
    }

    public function safeDown()
    {
        return false;
    }
}
