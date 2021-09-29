<?php

class m210224_020708_create_clinic_outcome_risk_status extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophciexamination_clinicoutcome_risk_status',
            array(
                'id' => 'pk',
                'name' => 'varchar(10) NOT NULL',
                'alias' => 'varchar(20) NOT NULL',
                'description' => 'string NOT NULL',
                'score' => 'int NOT NULL',
            ),
            true
        );

        $this->insert(
            'ophciexamination_clinicoutcome_risk_status',
            array(
                'name' => 'High',
                'alias' => 'Patient Risk 1',
                'description' => 'Irreversible hard from delayed appointment. Do NOT reschedule patient.',
                'score' => '30',
            )
        );
        $this->insert(
            'ophciexamination_clinicoutcome_risk_status',
            array(
                'name' => 'Medium',
                'alias' => 'Patient Risk 2',
                'description' => 'Reversible hard from delayed appointment.',
                'score' => '20',
            )
        );
        $this->insert(
            'ophciexamination_clinicoutcome_risk_status',
            array(
                'name' => 'Low',
                'alias' => 'Patient Risk 3',
                'description' => 'Mild consequences from delayed appointment. ',
                'score' => '10',
            )
        );
        $this->addOEColumn('ophciexamination_clinicoutcome_entry', 'risk_status_id', 'int(11) default NULL AFTER status_id', true);
        $this->addForeignKey('clinicoutcome_entry_risk_status_id_fk', 'ophciexamination_clinicoutcome_entry', 'risk_status_id', 'ophciexamination_clinicoutcome_risk_status', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('clinicoutcome_entry_risk_status_id_fk', 'ophciexamination_clinicoutcome_entry');
        $this->dropOEColumn('ophciexamination_clinicoutcome_entry', 'risk_status_id', true);
        $this->dropOETable('ophciexamination_clinicoutcome_risk_status', true);
    }
}
