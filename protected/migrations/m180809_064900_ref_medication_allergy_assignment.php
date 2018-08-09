<?php

class m180809_064900_ref_medication_allergy_assignment extends OEMigration
{
	public function up()
	{
	    $this->createOETable('ref_medication_allergy_assignment', array(
	        'id' => 'pk',
            'ref_medication_id' => 'INT(11) NOT NULL',
            'allergy_id' => 'INT(10) UNSIGNED NOT NULL'
        ), true);

	    $this->addForeignKey('fk_rmaa_ref_medication_id', 'ref_medication_allergy_assignment', 'ref_medication_id', 'ref_medication', 'id');
	    $this->addForeignKey('fk_rmaa_allergy_id', 'ref_medication_allergy_assignment', 'allergy_id', 'archive_allergy', 'id');

	    $this->execute("INSERT INTO ref_medication_allergy_assignment (ref_medication_id, allergy_id)
                        SELECT 
                        ref_medication.id,
                        allergy_id
                        FROM drug_allergy_assignment AS daa
                        LEFT JOIN ref_medication ON CONCAT(daa.drug_id, '_drug') = ref_medication.preferred_code 
                      ");
	}

	public function down()
	{
		$this->dropForeignKey('fk_rmaa_ref_medication_id', 'ref_medication_allergy_assignment');
		$this->dropForeignKey('fk_rmaa_allergy_id', 'ref_medication_allergy_assignment');
		$this->dropOETable('ref_medication_allergy_assignment', true);
	}
}