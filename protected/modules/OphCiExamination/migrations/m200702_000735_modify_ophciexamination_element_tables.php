<?php

class m200702_000735_modify_ophciexamination_element_tables extends OEMigration
{
    public function up()
    {
        // Add the temperature column to observations table
        $this->addOEColumn('et_ophciexamination_observations', 'temperature', 'DECIMAL(5,2)', true);

        // Add the comments column to the colour vision readings table
        $this->addOEColumn('et_ophciexamination_colourvision', 'left_notes', 'VARCHAR(4096)', true);
        $this->addOEColumn('et_ophciexamination_colourvision', 'right_notes', 'VARCHAR(4096)', true);

        // Add comments columns to the Van Herick table
        $this->addOEColumn('et_ophciexamination_van_herick', 'left_notes', 'VARCHAR(4096)', true);
        $this->addOEColumn('et_ophciexamination_van_herick', 'right_notes', 'VARCHAR(4096)', true);

        // Add comments columns to the CCT table
        $this->addOEColumn('et_ophciexamination_anteriorsegment_cct', 'left_notes', 'VARCHAR(4096)', true);
        $this->addOEColumn('et_ophciexamination_anteriorsegment_cct', 'right_notes', 'VARCHAR(4096)', true);

        // Add comments columns to the Bleb Assessment table
        $this->addOEColumn('et_ophciexamination_bleb_assessment', 'left_notes', 'VARCHAR(4096)', true);
        $this->addOEColumn('et_ophciexamination_bleb_assessment', 'right_notes', 'VARCHAR(4096)', true);

        // Add no systemic diagnoses column to the Systemic Diagnoses table
        $this->addOEColumn('et_ophciexamination_systemic_diagnoses', 'no_systemic_diagnoses_date', 'datetime', true);

        // Add no systemic surgery column to the Systemic Surgery table
        $this->addOEColumn('et_ophciexamination_systemicsurgery', 'no_systemicsurgery_date', 'datetime', true);

        // Add no ophthalmic surgery column to the Systemic Surgery table
        $this->addOEColumn('et_ophciexamination_pastsurgery', 'no_pastsurgery_date', 'datetime', true);

        // Add no systemic and no ophthalmic medication history columns to the History Medication table
        $this->addOEColumn('et_ophciexamination_history_medications', 'no_systemic_medications_date', 'datetime', true);
        $this->addOEColumn('et_ophciexamination_history_medications', 'no_ophthalmic_medications_date', 'datetime', true);
    }

    public function down()
    {
        // Drop temperature column from observations table
        $this->dropOEColumn('et_ophciexamination_observations', 'temperature', true);

        // Drop the comments column from the colour vision readings table
        $this->dropOEColumn('et_ophciexamination_colourvision', 'left_notes', true);
        $this->dropOEColumn('et_ophciexamination_colourvision', 'right_notes', true);

        //Drop comments columns from Van Herick table
        $this->dropOEColumn('et_ophciexamination_van_herick', 'left_notes', true);
        $this->dropOEColumn('et_ophciexamination_van_herick', 'right_notes', true);

        // Drop comments columns from CCT table
        $this->dropOEColumn('et_ophciexamination_anteriorsegment_cct', 'left_notes', true);
        $this->dropOEColumn('et_ophciexamination_anteriorsegment_cct', 'right_notes', true);

        // Drop comments columns from Bleb Assessment table
        $this->dropOEColumn('et_ophciexamination_bleb_assessment', 'left_notes', true);
        $this->dropOEColumn('et_ophciexamination_bleb_assessment', 'right_notes', true);

        // Drop no systemic diagnoses column from Systemic Diagnoses table
        $this->dropOEColumn('et_ophciexamination_systemic_diagnoses', 'no_systemic_diagnoses_date', true);

        // Drop no systemic surgery column from Systemic Diagnoses table
        $this->dropOEColumn('et_ophciexamination_systemicsurgery', 'no_systemicsurgery_date', true);

        // Drop no ophthalmic surgery column from Systemic Diagnoses table
        $this->dropOEColumn('et_ophciexamination_pastsurgery', 'no_pastsurgery_date', true);

        // Drop no systemic and no ophthalmic medication history columns from History Medication table
        $this->dropOEColumn('et_ophciexamination_history_medications', 'no_systemic_medications_date', true);
        $this->dropOEColumn('et_ophciexamination_history_medications', 'no_ophthalmic_medications_date', true);
    }
}
