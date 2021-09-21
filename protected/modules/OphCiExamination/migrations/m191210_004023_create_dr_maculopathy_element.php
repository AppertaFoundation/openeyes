<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Maculopathy;

class m191210_004023_create_dr_maculopathy_element extends OEMigration
{
    /**
     * @return bool|void
     */
    public function up()
    {
        $this->createOETable(
            'et_ophciexamination_dr_maculopathy',
            array(
                'id' => 'pk',
                'eye_id' => 'int(10) unsigned DEFAULT ' . Eye::BOTH,
                'event_id' => 'int(10) unsigned',
                'left_overall_grade' => 'string',
                'right_overall_grade' => 'string',
            ),
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_dr_maculopathy_event_fk',
            'et_ophciexamination_dr_maculopathy',
            'event_id',
            'event',
            'id'
        );

        $this->addForeignKey(
            'et_ophciexamination_dr_maculopathy_eye_fk',
            'et_ophciexamination_dr_maculopathy',
            'eye_id',
            'eye',
            'id'
        );

        $this->createOETable(
            'ophciexamination_dr_maculopathy_entry',
            array(
                'id' => 'pk',
                'element_id' => 'int',
                'feature_id' => 'int',
                'eye_id' => 'int(10) unsigned',
            ),
            true
        );

        $this->addForeignKey(
            'ophciexamination_dr_maculopathy_entry_element_fk',
            'ophciexamination_dr_maculopathy_entry',
            'element_id',
            'et_ophciexamination_dr_maculopathy',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_dr_maculopathy_entry_feature_fk',
            'ophciexamination_dr_maculopathy_entry',
            'feature_id',
            'ophciexamination_drgrading_feature',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_dr_maculopathy_emtry_eye_fk',
            'ophciexamination_dr_maculopathy_entry',
            'eye_id',
            'eye',
            'id'
        );

        $this->createElementType('OphCiExamination', 'DR Maculopathy', [
            'class_name' => Element_OphCiExamination_DR_Maculopathy::class,
            'display_order' => 310,
            'group_name' => 'Retina'
        ]);
    }

    public function down()
    {
        $this->delete(
            'element_type',
            'class_name = :class_name',
            [':class_name' => Element_OphCiExamination_DR_Maculopathy::class]
        );
        $this->dropOETable('ophciexamination_dr_maculopathy_entry', true);
        $this->dropOETable('et_ophciexamination_dr_maculopathy', true);
    }
}
