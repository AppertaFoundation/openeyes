<?php

class m210929_030026_add_discharge_option_tables extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    protected array $discharge_status_list = array(
        ['name' => 'Treatment complete', 'ecds_code' => '182992009'],
        ['name' => 'Streamed to primary care service / GP', 'ecds_code' => '1077021000000100'],
        ['name' => 'Streamed to Urgent Care Centre', 'ecds_code' => '1077031000000103'],
        ['name' => 'Streamed to Emergency Department', 'ecds_code' => '1077781000000101'],
        ['name' => 'Streamed to Ambulatory Emergency Care service', 'ecds_code' => '1077081000000104'],
        ['name' => 'Streamed to falls service', 'ecds_code' => '1077091000000102'],
        ['name' => 'Streamed to frailty service', 'ecds_code' => '1077101000000105'],
        ['name' => 'Streamed to mental health service', 'ecds_code' => '1077041000000107'],
        ['name' => 'Streamed to pharmacy service', 'ecds_code' => '1077071000000101'],
    );

    protected array $discharge_destination_list = array(
        ['name' => 'Home ', 'ecds_code' => '306689006'],
        ['name' => 'Referral to outpatients department', 'ecds_code' => '1066111000000103'],
        ['name' => 'Discharge to ward', 'ecds_code' => '306706006'],
        ['name' => 'Transfer to another facility', 'ecds_code' => '19712007', 'institution_required' => 1],
        ['name' => 'Residential home', 'ecds_code' => '306691003'],
        ['name' => 'Nursing home', 'ecds_code' => '306694006'],
        ['name' => 'Police', 'ecds_code' => '306705005'],
        ['name' => 'Custodial services', 'ecds_code' => '50861005'],
        ['name' => 'Hospital in the home service', 'ecds_code' => '1066351000000102'],
        ['name' => 'To hospice', 'ecds_code' => '183919006'],
        ['name' => 'Mortuary', 'ecds_code' => '305398007'],
        ['name' => 'Ambulatory emergency care service', 'ecds_code' => '1066341000000100'],
    );

    public function safeUp()
    {
        $this->addOEColumn(
            'ophciexamination_clinicoutcome_status',
            'discharge',
            'tinyint(1) DEFAULT 0',
            true
        );
        $this->createOETable(
            'ophciexamination_discharge_status',
            array(
                'id' => 'pk',
                'name' => 'varchar(80) NOT NULL',
                'ecds_code' => 'varchar(255)',
            ),
            true
        );
        $this->createOETable(
            'ophciexamination_discharge_destination',
            array(
                'id' => 'pk',
                'name' => 'varchar(80) NOT NULL',
                'ecds_code' => 'varchar(255)',
                'institution_required' => 'tinyint(1) DEFAULT 0',
            ),
            true
        );
        $this->addOEColumn(
            'ophciexamination_clinicoutcome_entry',
            'discharge_status_id',
            'int',
            true
        );
        $this->addOEColumn(
            'ophciexamination_clinicoutcome_entry',
            'discharge_destination_id',
            'int',
            true
        );
        $this->addOEColumn(
            'ophciexamination_clinicoutcome_entry',
            'transfer_institution_id',
            'int(10) unsigned',
            true
        );
        $this->addForeignKey(
            'ophciexamination_clinicoutcome_entry_ds_fk',
            'ophciexamination_clinicoutcome_entry',
            'discharge_status_id',
            'ophciexamination_discharge_status',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_clinicoutcome_entry_dd_fk',
            'ophciexamination_clinicoutcome_entry',
            'discharge_destination_id',
            'ophciexamination_discharge_destination',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_clinicoutcome_entry_ti_fk',
            'ophciexamination_clinicoutcome_entry',
            'transfer_institution_id',
            'institution',
            'id'
        );

        // Initial data insert.
        $this->insertMultiple(
            'ophciexamination_discharge_status',
            $this->discharge_status_list
        );

        $this->insertMultiple(
            'ophciexamination_discharge_destination',
            $this->discharge_destination_list
        );

        $this->update(
            'ophciexamination_clinicoutcome_status',
            array('discharge' => 1),
            'name = \'Discharge\'',
        );
    }

    public function down()
    {
        $this->dropOEColumn(
            'ophciexamination_clinicoutcome_status',
            'discharge',
            true
        );
        $this->dropForeignKey(
            'ophciexamination_clinicoutcome_entry_ds_fk',
            'ophciexamination_clinicoutcome_entry',
        );
        $this->dropForeignKey(
            'ophciexamination_clinicoutcome_entry_dd_fk',
            'ophciexamination_clinicoutcome_entry',
        );
        $this->dropForeignKey(
            'ophciexamination_clinicoutcome_entry_ti_fk',
            'ophciexamination_clinicoutcome_entry',
        );
        $this->dropOEColumn(
            'ophciexamination_clinicoutcome_entry',
            'discharge_status_id',
            true
        );
        $this->dropOEColumn(
            'ophciexamination_clinicoutcome_entry',
            'discharge_destination_id',
            true
        );
        $this->dropOEColumn(
            'ophciexamination_clinicoutcome_entry',
            'transfer_institution_id',
            true
        );
        $this->dropOETable(
            'ophciexamination_discharge_status',
            true
        );
        $this->dropOETable(
            'ophciexamination_discharge_destination',
            true
        );
    }
}
