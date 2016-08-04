<?php

class m150508_152907_patientRisks extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'risk',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) not null',
                'active' => 'tinyint(1) not null default 1',
            ),
            true
        );
        $defaultRisks = array(
            array('name' => 'Cannot Lie Flat'),
            array('name' => 'Extreme fear'),
            array('name' => 'Learning Difficulty'),
            array('name' => 'Inability to co-operate adequately'),
            array('name' => 'Other'),
            array('name' => 'MRSA Risk'),
            array('name' => 'CJD Risk'),
            array('name' => 'Dementia'),
            array('name' => 'CPR (Child Protection Risk)'),
        );
        foreach ($defaultRisks as $riskRow) {
            $this->insert('risk', $riskRow);
        }

        $this->createOETable(
            'patient_risk_assignment',
            array(
                'id' => 'pk',
                'patient_id' => 'int(10) unsigned not null',
                'risk_id' => 'int(11) not null',
                'comments' => 'text',
                'other' => 'varchar(255)',
            ),
            true
        );
        $this->addForeignKey('patient_fk', 'patient_risk_assignment', 'patient_id', 'patient', 'id');
        $this->addForeignKey('risk_fk', 'patient_risk_assignment', 'risk_id', 'risk', 'id');

        $this->addColumn('patient', 'no_risks_date', 'datetime default NULL');
        $this->addColumn('patient_version', 'no_risks_date', 'datetime default NULL');
    }

    public function down()
    {
        $this->dropTable('patient_risk_assignment');
        $this->dropTable('patient_risk_assignment_version');
        $this->dropTable('risk');
        $this->dropTable('risk_version');

        $this->dropColumn('patient', 'no_risks_date');
        $this->dropColumn('patient_version', 'no_risks_date');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
