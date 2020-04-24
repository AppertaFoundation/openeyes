<?php

class m180809_064900_medication_allergy_assignment extends OEMigration
{
    public function up()
    {
        $this->createOETable('medication_allergy_assignment', array(
            'id' => 'pk',
            'medication_id' => 'INT NOT NULL',
            'allergy_id' => 'INT NOT NULL'
        ), true);

        $this->addForeignKey('fk_rmaa_ref_medication_id', 'medication_allergy_assignment', 'medication_id', 'medication', 'id');
        $this->addForeignKey('fk_rmaa_allergy_id', 'medication_allergy_assignment', 'allergy_id', 'ophciexamination_allergy', 'id');

        $this->execute("INSERT INTO medication_allergy_assignment (medication_id, allergy_id)
                        SELECT 
                        medication.id,
                        allergy_id
                        FROM drug_allergy_assignment AS daa
                        LEFT JOIN medication ON daa.id = medication.source_old_id
                         WHERE medication.source_subtype = 'drug' 
                      ");
    }

    public function down()
    {
        $this->dropForeignKey('fk_rmaa_ref_medication_id', 'medication_allergy_assignment');
        $this->dropForeignKey('fk_rmaa_allergy_id', 'medication_allergy_assignment');
        $this->dropOETable('medication_allergy_assignment', true);
    }
}
