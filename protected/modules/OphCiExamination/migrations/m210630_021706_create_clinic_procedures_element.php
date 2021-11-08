<?php

class m210630_021706_create_clinic_procedures_element extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable(
            'et_ophciexamination_clinic_procedures',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned not null',
            ],
            true
        );

        // Create Clinic Procedures Group right below PCR Risks
        $pcr_risk_display_order = $this->dbConnection->createCommand()
            ->select(array('display_order'))
            ->from('element_group')
            ->where("name = 'PCR Risk'")
            ->queryScalar();
        $pcr_risk_display_order += 5;

        $this->createElementGroupForEventType(
            'Clinic Procedures',
            'OphCiExamination',
            $pcr_risk_display_order
        );

        // Create Clinic Procedures Element in the Clinic Procedures Group
        $pcr_risk_display_order = $this->dbConnection->createCommand()
            ->select(array('display_order'))
            ->from('element_type')
            ->where("name = 'PCR Risk'")
            ->queryScalar();
        $pcr_risk_display_order += 5;

        $this->createElementType(
            'OphCiExamination',
            'Clinic Procedures',
            [
                'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicProcedures',
                'display_order' => $pcr_risk_display_order,
                'group_name' => 'Clinic Procedures'
            ]
        );

        $this->createOETable(
            'ophciexamination_clinic_procedures_entry',
            [
                'id' => 'pk',
                'element_id' => 'int(11)',
                'procedure_id' => 'int(10) unsigned not null',
                'eye_id' => 'int(10) unsigned not null',
                'outcome_time' => 'time not null',
                'date' => 'datetime NOT NULL',
                'comments' => 'text',
                'subspecialty_id' => 'int(10) unsigned',
            ],
            true
        );

        $this->addForeignKey(
            'ophciexamination_clinic_procedures_entry_fk',
            'ophciexamination_clinic_procedures_entry',
            'element_id',
            'et_ophciexamination_clinic_procedures',
            'id'
        );

        $this->addOEColumn('proc', 'is_clinic_proc', 'boolean', true);

        // Create Clinic Procedures Options list for reference mapping
        $this->createOETable(
            'ophciexamination_clinic_procedure',
            [
                'id' => 'pk',
                'proc_id' => 'int(10) unsigned',
                'institution_id' => 'int(10) unsigned',
                'firm_id' => 'int(10) unsigned',
                'subspecialty_id' => 'int(10) unsigned',
            ],
            true
        );

        $this->addForeignKey(
            'ophciexamination_clinic_procedure_proc_fk',
            'ophciexamination_clinic_procedure',
            'proc_id',
            'proc',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_clinic_procedure_inst_fk',
            'ophciexamination_clinic_procedure',
            'institution_id',
            'institution',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_clinic_procedure_firm_fk',
            'ophciexamination_clinic_procedure',
            'firm_id',
            'firm',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_clinic_procedure_subsp_fk',
            'ophciexamination_clinic_procedure',
            'subspecialty_id',
            'subspecialty',
            'id'
        );
    }

    public function safeDown()
    {
        $this->delete('element_type', "name = 'Clinic Procedures'");
        $this->delete('element_group', "name = 'Clinic Procedures'");
        $this->dropForeignKey('ophciexamination_clinic_procedures_entry_fk', 'ophciexamination_clinic_procedures_entry');
        $this->dropOETable('ophciexamination_clinic_procedures_entry', true);
        $this->dropOETable('et_ophciexamination_clinic_procedures', true);
        $this->dropOEColumn('proc', 'is_clinic_proc', true);
        $this->dropForeignKey('ophciexamination_clinic_procedure_proc_fk', 'ophciexamination_clinic_procedure');
        $this->dropForeignKey('ophciexamination_clinic_procedure_inst_fk', 'ophciexamination_clinic_procedure');
        $this->dropForeignKey('ophciexamination_clinic_procedure_firm_fk', 'ophciexamination_clinic_procedure');
        $this->dropForeignKey('ophciexamination_clinic_procedure_subsp_fk', 'ophciexamination_clinic_procedure');
        $this->dropOETable('ophciexamination_clinic_procedure', true);
    }
}
