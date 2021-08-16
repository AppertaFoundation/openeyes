<?php

class m210531_090800_add_language_to_patient_table extends OEMigration
{
    public function safeUp()
    {
        $result = $this->dbConnection->createCommand("SHOW COLUMNS FROM `language` LIKE 'pas_term'")->queryScalar();
        echo "RESULT = " . $result;
        $not_exists = empty($result);

        if ($not_exists) {
            $this->addColumn(
                'language',
                'pas_term',
                'varchar(4)'
            );
        }

        $result = $this->dbConnection->createCommand("SHOW COLUMNS FROM `language_version` LIKE 'pas_term'")->queryScalar();
        echo "RESULT = " . $result;
        $not_exists = empty($result);

        if ($not_exists) {
            $this->addColumn(
                'language_version',
                'pas_term',
                'varchar(4)'
            );
        }
    }

    public function safeDown()
    {
        $this->dropOEColumn('language', 'pas_term', true);
    }
}
