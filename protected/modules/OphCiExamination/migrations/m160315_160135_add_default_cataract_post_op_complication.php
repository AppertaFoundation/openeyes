<?php

class m160315_160135_add_default_cataract_post_op_complication extends CDbMigration
{
    private $defaultCataractComplications = array(
                array('code' => 0, 'name' => 'none'),
                array('code' => 1, 'name' => 'Anterior capsulophimosis'),
                array('code' => 2, 'name' => 'choroidal effusion / detachment'),
                array('code' => 3, 'name' => 'corneal decompensation'),
                array('code' => 4, 'name' => 'corneal epithelial defect'),
                array('code' => 5, 'name' => 'corneal oedema / striae / Descemet\'s folds'),
                array('code' => 6, 'name' => 'cystoid macular oedema'),
                array('code' => 7, 'name' => 'endophthalmitis'),
                array('code' => 8, 'name' => 'epithelial ingrowth'),
                array('code' => 9, 'name' => 'floaters after operation'),
                array('code' => 10, 'name' => 'globe perforation'),
                array('code' => 11, 'name' => 'hyphaema'),
                array('code' => 12, 'name' => 'hypotony'),
                array('code' => 13, 'name' => 'External eye infection'),
                array('code' => 14, 'name' => 'IOL decentred'),
                array('code' => 15, 'name' => 'IOL in vitreous cavity'),
                array('code' => 16, 'name' => 'iris prolapse'),
                array('code' => 17, 'name' => 'iris to wound'),
                array('code' => 18, 'name' => 'leaking wound (Seidel +ve)'),
                array('code' => 19, 'name' => 'phthisis'),
                array('code' => 20, 'name' => 'posterior capsule opacification - YAG indicated'),
                array('code' => 21, 'name' => 'posterior capsule opacification'),
                array('code' => 22, 'name' => 'post-operative eyelid bruising'),
                array('code' => 23, 'name' => 'post-operative eyelid oedema'),
                array('code' => 24, 'name' => 'post-operative ptosis'),
                array('code' => 25, 'name' => 'post-operative uveitis'),
                array('code' => 26, 'name' => 'progression of diabetic retinopathy'),
                array('code' => 27, 'name' => 'pupil block'),
                array('code' => 28, 'name' => 'raised IOP (>21 mmHg)'),
                array('code' => 29, 'name' => 'reduction in vision'),
                array('code' => 30, 'name' => 'retained soft lens matter'),
                array('code' => 31, 'name' => 'retinal detachment'),
                array('code' => 32, 'name' => 'retinal tear'),
                array('code' => 33, 'name' => 'ruptured section'),
                array('code' => 34, 'name' => 'suture granuloma'),
                array('code' => 35, 'name' => 'suture induced corneal abscess'),
                array('code' => 36, 'name' => 'suprachoroidal haemorrhage'),
                array('code' => 37, 'name' => 'unexpected refractive outcome'),
                array('code' => 38, 'name' => 'vitreous in the AC'),
                array('code' => 39, 'name' => 'vitreous to the section'),
                array('code' => 40, 'name' => 'vitreous proplase'),
                array('code' => 41, 'name' => 'wound dehiscence'),
                array('code' => 42, 'name' => 'wrong operation performed'),
                array('code' => 43, 'name' => 'other'),
                array('code' => 999, 'name' => 'not recorded'),
            );

    public function up()
    {
        $subspecialty = \Subspecialty::model()->findByAttributes(array('name' => 'Cataract'));
        if ($subspecialty) {
            foreach ($this->defaultCataractComplications as $step => $complications) {
                $complication = \OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications::model()->findByAttributes(array('code' => $complications['code']));

                $this->insert('ophciexamination_postop_complications_subspecialty', array(
                                'subspecialty_id' => $subspecialty->id,
                                'complication_id' => $complication->id,
                                'display_order' => ($step * 2),
                            )
                    );
            }
        }
    }

    public function down()
    {
        $subspecialty = \Subspecialty::model()->findByAttributes(array('name' => 'Cataract'));

        foreach ($this->defaultCataractComplications as $defaultCataractComplication) {
            $complication = \OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications::model()->findByAttributes(array('code' => $defaultCataractComplication['code']));
            $where = "complication_id = '".$complication->id."' AND subspecialty_id = '".$subspecialty->id."'";
            $this->delete('ophciexamination_postop_complications_subspecialty', $where);
        }
    }
}
