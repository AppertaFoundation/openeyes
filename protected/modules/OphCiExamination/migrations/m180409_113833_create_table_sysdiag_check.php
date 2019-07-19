<?php

class m180409_113833_create_table_sysdiag_check extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophciexamination_systemic_diagnoses_req_diag_check', array(
            'id' => 'pk',
            'element_id' => 'int',
            'side_id' => 'int(10) unsigned',
            'disorder_id' => 'bigint(20) unsigned',
            'secondary_diagnosis_id' => 'int(10) unsigned',
            'date' => 'varchar(10)',
            'has_disorder' => 'tinyint'
        ), true);

        $this->createIndex('idx_ophciexamination_sysdiag_check_eid', 'ophciexamination_systemic_diagnoses_req_diag_check', 'element_id');
        $this->createIndex('idx_ophciexamination_sysdiag_check_did', 'ophciexamination_systemic_diagnoses_req_diag_check', 'disorder_id');

        $this->addForeignKey('fk_ophciexamination_sysdiag_check_eid', 'ophciexamination_systemic_diagnoses_req_diag_check', 'element_id', 'et_ophciexamination_systemic_diagnoses', 'id');
        $this->addForeignKey('fk_ophciexamination_sysdiag_check_did', 'ophciexamination_systemic_diagnoses_req_diag_check', 'disorder_id', 'disorder', 'id');

    }

    public function down()
    {
        $this->dropForeignKey('fk_ophciexamination_sysdiag_check_eid', 'ophciexamination_systemic_diagnoses_req_diag_check');
        $this->dropForeignKey('fk_ophciexamination_sysdiag_check_did', 'ophciexamination_systemic_diagnoses_req_diag_check');
        //$this->dropForeignKey('fk_ophciexamination_sysdiag_check_sdid', 'ophciexamination_systemic_diagnoses_req_diag_check');
        $this->dropOETable('ophciexamination_systemic_diagnoses_req_diag_check', true);
    }
}