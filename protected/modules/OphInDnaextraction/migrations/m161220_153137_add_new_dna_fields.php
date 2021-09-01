<?php

class m161220_153137_add_new_dna_fields extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophindnaextraction_dnaextraction', 'dna_quality', 'FLOAT NULL');
        $this->addColumn('et_ophindnaextraction_dnaextraction', 'dna_quantity', 'FLOAT NULL');

        $this->addColumn('et_ophindnaextraction_dnaextraction_version', 'dna_quality', 'FLOAT NULL');
        $this->addColumn('et_ophindnaextraction_dnaextraction_version', 'dna_quantity', 'FLOAT NULL');
    }

    public function down()
    {
        $this->dropColumn('et_ophindnaextraction_dnaextraction', 'dna_quality');
        $this->dropColumn('et_ophindnaextraction_dnaextraction', 'dna_quantity');

        $this->dropColumn('et_ophindnaextraction_dnaextraction_version', 'dna_quality');
        $this->dropColumn('et_ophindnaextraction_dnaextraction_version', 'dna_quantity');
    }
}
