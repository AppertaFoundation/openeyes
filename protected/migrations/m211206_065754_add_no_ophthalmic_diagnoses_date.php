<?php

class m211206_065754_add_no_ophthalmic_diagnoses_date extends OEMigration
{
    public function up()
    {
        // Add no ophthalmic diagnoses column to the Diagnoses table
        $this->addOEColumn('et_ophciexamination_diagnoses', 'no_ophthalmic_diagnoses_date', 'datetime', true);
    }

    public function down()
    {
        // Drop no ophthalmic diagnoses column from Diagnoses table
        $this->dropOEColumn('et_ophciexamination_diagnoses', 'no_ophthalmic_diagnoses_date', true);
    }
}
