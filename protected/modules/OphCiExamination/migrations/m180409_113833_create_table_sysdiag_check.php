<?php

class m180409_113833_create_table_sysdiag_check extends OEMigration
{
	public function up()
	{
	    $this->createOETable('ophciexamination_systemic_diagnoses_required_diagnosis_check', array(
	        'id' => 'pk',
            'element_id' => 'int',
            'side_id' => 'int(10) unsigned',
            'disorder_id' => 'bigint(20) unsigned',
            'secondary_diagnosis_id' => 'int(10) unsigned',
            'date' => 'varchar(10)',
            'has_disorder' => 'tinyint'
        ));

	    $this->createIndex('idx_ophciexamination_sysdiag_check_eid', 'ophciexamination_systemic_diagnoses_required_diagnosis_check', 'element_id');
	    $this->createIndex('idx_ophciexamination_sysdiag_check_did', 'ophciexamination_systemic_diagnoses_required_diagnosis_check', 'disorder_id');
	    $this->createIndex('idx_ophciexamination_sysdiag_check_sdid', 'ophciexamination_systemic_diagnoses_required_diagnosis_check', 'secondary_diagnosis_id');

	    $this->addForeignKey('fk_ophciexamination_sysdiag_check_eid', 'ophciexamination_systemic_diagnoses_required_diagnosis_check', 'element_id', 'et_ophciexamination_systemic_diagnoses', 'id');
	    $this->addForeignKey('fk_ophciexamination_sysdiag_check_did', 'ophciexamination_systemic_diagnoses_required_diagnosis_check', 'disorder_id',  'disorder', 'id');
	    $this->addForeignKey('fk_ophciexamination_sysdiag_check_sdid', 'ophciexamination_systemic_diagnoses_required_diagnosis_check', 'secondary_diagnosis_id',  'secondary_diagnosis', 'id');
	}

	public function down()
	{
	    $this->dropForeignKey('fk_ophciexamination_sysdiag_check_eid', 'ophciexamination_systemic_diagnoses_required_diagnosis_check');
	    $this->dropForeignKey('fk_ophciexamination_sysdiag_check_did', 'ophciexamination_systemic_diagnoses_required_diagnosis_check');
	    $this->dropForeignKey('fk_ophciexamination_sysdiag_check_sdid', 'ophciexamination_systemic_diagnoses_required_diagnosis_check');
		$this->dropTable('ophciexamination_systemic_diagnoses_required_diagnosis_check');
	}
}