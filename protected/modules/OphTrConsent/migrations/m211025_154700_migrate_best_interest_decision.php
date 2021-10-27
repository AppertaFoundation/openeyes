<?php

class m211025_154700_migrate_best_interest_decision extends OEMigration
{
    private const ARCHIVE_ET_BEST_INTEREST_TABLE = 'et_ophtrconsent_best_interest_decision_archive';

    public function up()
    {
        /*$this->createOETable("et_ophtrconsent_best_interest_decision_test", array(
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED',
            'patient_has_not_refused' => 'BOOLEAN',
            'deputy_granted' => 'BOOLEAN',
            'treatment_cannot_wait_reason' => 'TEXT NULL',
            'circumstances' => 'TEXT NULL',
            'wishes' => 'TEXT NULL',
            'imca_view' => 'TEXT NULL',
            'options_less_restrictive' => 'TEXT NULL',
            'views_of_colleagues' => 'TEXT NULL'
        ),
        true);

        $this->addColumn("et_ophtrconsent_best_interest_decision_test", "decision", "TEXT NULL");
        
        $this->addColumn("et_ophtrconsent_best_interest_decision_test", "protected_file_id", "INT(10) UNSIGNED NULL");
        
        $this->addColumn("et_ophtrconsent_best_interest_decision_test", "no_decision_made", "BOOLEAN NOT NULL DEFAULT 0");*/
	    


        if ($this->dbConnection->schema->getTable('et_ophtrconsent_best_interest_decision_test', true)) {

            //$this->execute("CREATE TABLE " . self::ARCHIVE_ET_BEST_INTEREST_TABLE . " AS SELECT * FROM et_ophtrconsent_best_interest_decision_test");

            $this->execute("CREATE TABLE et_ophtrconsent_best_interest_decision_2 AS SELECT * FROM et_ophtrconsent_best_interest_decision_test");

            /*$this->execute('ALTER TABLE et_ophtrconsent_best_interest_decision_version
                                DROP COLUMN IF EXISTS deputy_granted,
                                DROP COLUMN IF EXISTS circumstances,
                                DROP COLUMN IF EXISTS imca_view,
                                DROP COLUMN IF EXISTS options_less_restrictive,
                                DROP COLUMN IF EXISTS views_of_colleagues');*/

            $this->addOEColumn('et_ophtrconsent_best_interest_decision_2','treatment_cannot_wait','BOOLEAN',true);
            $this->addOEColumn('et_ophtrconsent_best_interest_decision_2','reason_for_procedure','TEXT NULL',true);

            $this->execute("
                UPDATE et_ophtrconsent_best_interest_decision_2
                SET treatment_cannot_wait = 1
                WHERE treatment_cannot_wait_reason IS NOT NULL
            ");

            $this->execute("
                UPDATE et_ophtrconsent_best_interest_decision_2
                SET wishes = concat(wishes, CHAR(10), options_less_restrictive)
            ");

            $this->execute('ALTER TABLE et_ophtrconsent_best_interest_decision_2
                            DROP COLUMN IF EXISTS deputy_granted,
                            DROP COLUMN IF EXISTS circumstances,
                            DROP COLUMN IF EXISTS imca_view,
                            DROP COLUMN IF EXISTS options_less_restrictive,
                            DROP COLUMN IF EXISTS views_of_colleagues');

        } else {

            $this->createOETable("et_ophtrconsent_best_interest_decision", array(
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED',
                'patient_has_not_refused' => 'BOOLEAN',
                'reason_for_procedure' => 'TEXT NULL',
                'treatment_cannot_wait' => 'BOOLEAN',
                'treatment_cannot_wait_reason' => 'TEXT NULL',
                'wishes' => 'TEXT NULL'
            ), true);

            $this->addForeignKey("fk_et_ophtrconsent_best_interest_decision_event", "et_ophtrconsent_best_interest_decision", "event_id", "event", "id");
        }



    }

    public function down()
    {
        //$this->dropOETable("et_ophtrconsent_best_interest_decision_test", true);
        echo "m211015_154700_migrate_best_interest_decision does not support migration down.\n";
        return true;
    }
}
