<?php

class m210720_030613_create_triage_element extends OEMigration
{
    public function safeUp()
    {
        $this->createElementGroupForEventType(
            'Triage',
            'OphCiExamination',
            25
        );

        $this->createElementType('OphCiExamination', 'Triage', [
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Triage',
            'display_order' => 550,
            'group_name' => 'Triage',
        ]);

        $this->createOETable(
            'et_ophciexamination_triage',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned not null',
            ],
            true
        );

        $this->createOETable(
            'ophciexamination_triage_priority',
            [
                'id' => 'pk',
                'description' => 'varchar(50)',
                'label_colour' => 'varchar(10)',
                'snomed_code' => 'varchar(20)',
            ],
            false
        );

        // Add triage priority options
        $this->insertMultiple('ophciexamination_triage_priority', [
            ['id' => 1, 'description' => 'Immediate', 'label_colour' => 'red', 'snomed_code' => '1064891000000107'],
            ['id' => 2, 'description' => 'Very urgent', 'label_colour' => 'orange', 'snomed_code' => '1064911000000105'],
            ['id' => 3, 'description' => 'Urgent', 'label_colour' => 'yellow', 'snomed_code' => '1064901000000108'],
            ['id' => 4, 'description' => 'Standard', 'label_colour' => 'green', 'snomed_code' => '1077241000000103'],
            ['id' => 5, 'description' => 'Low', 'label_colour' => 'grey', 'snomed_code' => '1077251000000100'],
        ]);

        $this->createOETable(
            'ophciexamination_triage_chief_complaint',
            [
                'id' => 'pk',
                'description' => 'varchar(50)',
                'snomed_code' => 'varchar(20)',
            ],
            false
        );

        // Add triage chief complaint options
        $this->insertMultiple('ophciexamination_triage_chief_complaint', [
            ['id' => 1, 'description' => 'Red eye', 'snomed_code' => '75705005'],
            ['id' => 2, 'description' => 'Foreign body on eye', 'snomed_code' => '55899000'],
            ['id' => 3, 'description' => 'Pain in/around eye', 'snomed_code' => '41652007'],
            ['id' => 4, 'description' => 'Discharge from eye', 'snomed_code' => '246679005'],
            ['id' => 5, 'description' => 'Visual disturbance', 'snomed_code' => '63102001'],
            ['id' => 6, 'description' => 'Photophobia', 'snomed_code' => '409668002'],
            ['id' => 7, 'description' => 'Eye injury', 'snomed_code' => '282752000'],
            ['id' => 8, 'description' => 'Eye review', 'snomed_code' => '170720001'],
            ['id' => 9, 'description' => 'Acute Visual Disturbance', 'snomed_code' => '63102001'],
            ['id' => 10, 'description' => 'Chronic Visual Disturbance	', 'snomed_code' => '63102001'],
            ['id' => 11, 'description' => 'Lid Lump', 'snomed_code' => '248515009'],
            ['id' => 12, 'description' => 'Lid Swelling', 'snomed_code' => '193967004'],
            ['id' => 13, 'description' => 'Flashes', 'snomed_code' => '162277006'],
            ['id' => 14, 'description' => 'Floaters', 'snomed_code' => '162278001'],
        ]);

        $this->createOETable(
            'ophciexamination_triage_eye_injury',
            [
                'id' => 'pk',
                'description' => 'varchar(50)',
            ],
            false
        );

        // Add triage eye injury options
        $this->insertMultiple('ophciexamination_triage_eye_injury', [
            ['id' => 1, 'description' => 'Blunt Injury'],
            ['id' => 2, 'description' => 'Sharp Injury'],
            ['id' => 3, 'description' => 'Chemical Injury'],
            ['id' => 4, 'description' => 'Penetrating Injury'],
            ['id' => 5, 'description' => 'Sports Injury'],
            ['id' => 6, 'description' => 'Traumatic Injury'],
            ['id' => 7, 'description' => 'Garden Injury'],
            ['id' => 8, 'description' => 'Other'],
        ]);

        $this->createOETable(
            'ophciexamination_triage',
            [
                'id' => 'pk',
                'element_id' => 'int(11)',
                'time' => 'time not null',
                'priority_id' => 'int(11)',
                'treat_as_adult' => 'boolean not null',
                'site_id' => 'int(10) unsigned',
                'chief_complaint_id' => 'int(11)',
                'eye_injury_id' => 'int(11)',
                'eye_id' => 'int(10) unsigned not null',
                'comments' => 'text',
            ],
            true
        );

        $this->addForeignKey(
            'ophciexamination_triage_element_fk',
            'ophciexamination_triage',
            'element_id',
            'et_ophciexamination_triage',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_triage_priority_fk',
            'ophciexamination_triage',
            'priority_id',
            'ophciexamination_triage_priority',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_triage_chief_complaint_fk',
            'ophciexamination_triage',
            'chief_complaint_id',
            'ophciexamination_triage_chief_complaint',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_triage_eye_injury_fk',
            'ophciexamination_triage',
            'eye_injury_id',
            'ophciexamination_triage_eye_injury',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_triage_site_fk',
            'ophciexamination_triage',
            'site_id',
            'site',
            'id'
        );
    }

    public function safeDown()
    {
        $this->delete('element_type', "name = 'Triage'");
        $this->delete('element_group', "name = 'Triage'");
        $this->dropOETable('ophciexamination_triage', true);
        $this->dropOETable('et_ophciexamination_triage', true);
        $this->dropOETable('ophciexamination_triage_priority', false);
        $this->dropOETable('ophciexamination_triage_chief_complaint', false);
        $this->dropOETable('ophciexamination_triage_eye_injury', false);
    }
}
