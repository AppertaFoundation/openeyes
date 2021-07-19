<?php

class m191204_233204_add_dr_grading_features_table extends OEMigration
{
    private array $features = array(
        array(
            'active' => 1,
            'name' => 'No DR',
            'grade' => 'R0',
            'display_order' => 10
        ),
        array(
            'active' => 1,
            'name'=> 'MA',
            'grade' => 'R1',
            'display_order' => 20,
        ),
        array(
            'name'=> 'Retinal Haem(s)',
            'grade' => 'R1',
            'display_order' => 30,
        ),
        array(
            'active' => 1,
            'name'=> 'Venous loop(s)',
            'grade' => 'R1',
            'display_order' => 40,
        ),
        array(
            'active' => 1,
            'name'=> 'Exudate + other features of DR',
            'grade' => 'R1',
            'display_order' => 50,
        ),
        array(
            'active' => 1,
            'name'=> 'CWS + other features of DR',
            'grade' => 'R1',
            'display_order' => 60,
        ),
        array(
            'active' => 1,
            'name'=> 'Venous bleeding',
            'grade' => 'R2',
            'display_order' => 70,
        ),
        array(
            'active' => 1,
            'name'=> 'Venous reduplication',
            'grade' => 'R2',
            'display_order' => 80,
        ),
        array(
            'active' => 1,
            'name'=> 'Multiple blot haems',
            'grade' => 'R2',
            'display_order' => 90,
        ),
        array(
            'active' => 1,
            'name'=> 'IRMA',
            'grade' => 'R2',
            'display_order' => 100,
        ),
        array(
            'active' => 1,
            'name'=> 'Pre-retinal fibrosis + PRP',
            'grade' => 'R3s',
            'display_order' => 110,
        ),
        array(
            'active' => 1,
            'name'=> 'Fibrosis proliferation (disc or elsewhere) + PRP',
            'grade' => 'R3s',
            'display_order' => 120,
        ),
        array(
            'active' => 1,
            'name'=> 'Stable R2 + PRP',
            'grade' => 'R3s',
            'display_order' => 130,
        ),
        array(
            'active' => 1,
            'name'=> 'R1 + PRP',
            'grade' => 'R3s',
            'display_order' => 140,
        ),
        array(
            'active' => 1,
            'name'=> 'NVD',
            'grade' => 'R3a',
            'display_order' => 150,
        ),
        array(
            'active' => 1,
            'name'=> 'NVE',
            'grade' => 'R3a',
            'display_order' => 160,
        ),
        array(
            'active' => 1,
            'name'=> 'New tractional retinal detachment',
            'grade' => 'R3a',
            'display_order' => 170,
        ),
        array(
            'active' => 1,
            'name'=> 'Reactivation in R3s',
            'grade' => 'R3a',
            'display_order' => 180,
        ),
        array(
            'active' => 1,
            'name'=> 'Nil',
            'grade' => 'M0',
            'display_order' => 190,
        ),
        array(
            'active' => 1,
            'name'=> 'MA or haem within 1DD of foveal centre if best VA of <=6/12 (where cause of reduced VA is known and diabetic macular oedema)',
            'grade' => 'M1',
            'display_order' => 200,
        ),
        array(
            'active' => 1,
            'name'=> 'Exudate within 1 disc diameter (DD) of the centre of the fovea',
            'grade' => 'M1',
            'display_order' => 210,
        ),
        array(
            'active' => 1,
            'name'=> 'Group of exudates within the macula',
            'grade' => 'M1',
            'display_order' => 220,
        ),
        array(
            'active' => 1,
            'name'=> 'Retinal thickening within 1DD of the centre of the fovea',
            'grade' => 'M1',
            'display_order' => 230,
        )
    );
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createOETable('ophciexamination_drgrading_feature', array(
            'id' => 'pk',
            'active' => 'tinyint(1)',
            'name' => 'varchar(255)',
            'grade' => 'varchar(10)',
            'display_order' => 'int(11)',
        ), true);

        $this->insertMultiple('ophciexamination_drgrading_feature', $this->features);
    }

    public function safeDown()
    {
        $this->dropOETable('ophciexamination_drgrading_feature', true);
    }
}
