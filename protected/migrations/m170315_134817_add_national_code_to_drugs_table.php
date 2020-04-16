<?php

class m170315_134817_add_national_code_to_drugs_table extends CDbMigration
{
    public function up()
    {
        $this->addColumn('drug', 'national_code', 'VARCHAR(25)');
        $this->addColumn('drug_version', 'national_code', 'VARCHAR(25)');
    }

    public function down()
    {
        $this->dropColumn('drug', 'national_code');
        $this->dropColumn('drug_version', 'national_code');
    }
}
