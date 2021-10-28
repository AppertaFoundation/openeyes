<?php

class m211025_154700_migrate_best_interest_decision extends OEMigration
{
    private const ARCHIVE_ET_BEST_INTEREST_TABLE = 'et_ophtrconsent_best_interest_decision_archive';
    private const ARCHIVE_ET_BEST_INTEREST_TABLE_V = 'et_ophtrconsent_best_interest_decision_version_archive';

    public function up()
    {

        if ($this->dbConnection->schema->getTable('et_ophtrconsent_best_interest_decision', true) && isset($this->dbConnection->schema->getTable('element_type_version')->columns['deputy_granted'])) {

            $this->execute("CREATE TABLE " . self::ARCHIVE_ET_BEST_INTEREST_TABLE . " AS SELECT * FROM et_ophtrconsent_best_interest_decision");
            $this->execute("CREATE TABLE " . self::ARCHIVE_ET_BEST_INTEREST_TABLE_V . " AS SELECT * FROM et_ophtrconsent_best_interest_decision_version");

            $this->addOEColumn('et_ophtrconsent_best_interest_decision','treatment_cannot_wait','BOOLEAN',true);
            $this->addOEColumn('et_ophtrconsent_best_interest_decision','reason_for_procedure','TEXT NULL',true);

            $this->execute("
                UPDATE et_ophtrconsent_best_interest_decision
                SET treatment_cannot_wait = 1
                WHERE treatment_cannot_wait_reason IS NOT NULL
            ");

            $this->execute("
                UPDATE et_ophtrconsent_best_interest_decision_version
                SET treatment_cannot_wait = 1
                WHERE treatment_cannot_wait_reason IS NOT NULL
            ");

            $this->execute("
                UPDATE et_ophtrconsent_best_interest_decision
                SET wishes = concat(wishes, CHAR(10), options_less_restrictive)
            ");

            $this->execute("
                UPDATE et_ophtrconsent_best_interest_decision_version
                SET wishes = concat(wishes, CHAR(10), options_less_restrictive)
            ");

            $this->execute('ALTER TABLE et_ophtrconsent_best_interest_decision
                            DROP COLUMN IF EXISTS deputy_granted,
                            DROP COLUMN IF EXISTS circumstances,
                            DROP COLUMN IF EXISTS imca_view,
                            DROP COLUMN IF EXISTS options_less_restrictive,
                            DROP COLUMN IF EXISTS views_of_colleagues');

            $this->execute('ALTER TABLE et_ophtrconsent_best_interest_decision_version
                            DROP COLUMN IF EXISTS deputy_granted,
                            DROP COLUMN IF EXISTS circumstances,
                            DROP COLUMN IF EXISTS imca_view,
                            DROP COLUMN IF EXISTS options_less_restrictive,
                            DROP COLUMN IF EXISTS views_of_colleagues');

        }

    }

    public function down()
    {
        echo "m211025_154700_migrate_old_best_interest_decision_element does not support migration down.\n";
        return true;
    }
}
