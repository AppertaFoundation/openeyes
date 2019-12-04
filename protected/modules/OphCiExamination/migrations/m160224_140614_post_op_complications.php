<?php

class m160224_140614_post_op_complications extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable(
            'ophciexamination_postop_complications',
            array(
                'id' => 'pk',
                'code' => 'SMALLINT(4) UNSIGNED',
                'name' => 'VARCHAR(64) NOT NULL',
                'display_order' => 'TINYINT(3) UNSIGNED NOT NULL',
                'active' => 'TINYINT(1) NOT NULL DEFAULT 1',
            ),
            true
        );

        $defaultComplications = array(
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
            array('code' => 51, 'name' => 'accommodative difficulty'),
            array('code' => 52, 'name' => 'angle closure glaucoma'),
            array('code' => 53, 'name' => 'anterior segment ischaemia'),
            array('code' => 54, 'name' => 'bleb dysaesthesia'),
            array('code' => 55, 'name' => 'blebitis'),
            array('code' => 56, 'name' => 'bleb leak'),
            array('code' => 57, 'name' => 'burn creep under fovea'),
            array('code' => 58, 'name' => 'cataract'),
            array('code' => 59, 'name' => 'central toxic keratopathy'),
            array('code' => 60, 'name' => 'cheesewiring of tubes'),
            array('code' => 61, 'name' => 'conjunctival recession'),
            array('code' => 62, 'name' => 'choroidal neovascular membrane'),
            array('code' => 63, 'name' => 'chronic fistula'),
            array('code' => 64, 'name' => 'corneal epithelial staining'),
            array('code' => 65, 'name' => 'corneal graft rejection'),
            array('code' => 66, 'name' => 'corneal haze'),
            array('code' => 67, 'name' => 'corneal scarring'),
            array('code' => 68, 'name' => 'decentred ablation'),
            array('code' => 69, 'name' => 'dellen'),
            array('code' => 70, 'name' => 'diffuse lamellar keratitis'),
            array('code' => 71, 'name' => 'dilated pupil'),
            array('code' => 72, 'name' => 'diplopia'),
            array('code' => 73, 'name' => 'displaced tubes'),
            array('code' => 74, 'name' => 'dry cornea'),
            array('code' => 75, 'name' => 'dry eye'),
            array('code' => 76, 'name' => 'early loss of tubes'),
            array('code' => 77, 'name' => 'ectasia'),
            array('code' => 78, 'name' => 'encapsulated bleb / Tenons cyst'),
            array('code' => 79, 'name' => 'epistaxis'),
            array('code' => 80, 'name' => 'exposed scleral buckle'),
            array('code' => 81, 'name' => 'exposed suture'),
            array('code' => 82, 'name' => 'exposed Tenon\'s'),
            array('code' => 83, 'name' => 'failure'),
            array('code' => 84, 'name' => 'flap dislocation'),
            array('code' => 85, 'name' => 'flap folds'),
            array('code' => 86, 'name' => 'flap infection'),
            array('code' => 87, 'name' => 'flat AC: lens-cornea touch'),
            array('code' => 88, 'name' => 'foveal burn'),
            array('code' => 89, 'name' => 'giant bleb'),
            array('code' => 90, 'name' => 'glare'),
            array('code' => 91, 'name' => 'graft shrinkage'),
            array('code' => 92, 'name' => 'haloes'),
            array('code' => 93, 'name' => 'heavy liquid in the anterior chamber'),
            array('code' => 94, 'name' => 'hyperoleon'),
            array('code' => 95, 'name' => 'hypotonous maculopathy'),
            array('code' => 96, 'name' => 'iris incarceration in sclerostomy'),
            array('code' => 97, 'name' => 'implant or tube exposure'),
            array('code' => 98, 'name' => 'injected bleb'),
            array('code' => 99, 'name' => 'interface debris'),
            array('code' => 100, 'name' => 'loss of field of vision'),
            array('code' => 101, 'name' => 'lost / damaged corneal flap'),
            array('code' => 102, 'name' => 'lost muscle'),
            array('code' => 103, 'name' => 'macular fold'),
            array('code' => 104, 'name' => 'malignant glaucoma'),
            array('code' => 105, 'name' => 'micro-striae'),
            array('code' => 106, 'name' => 'night vision problems'),
            array('code' => 107, 'name' => 'nyctalopia'),
            array('code' => 108, 'name' => 'onset or progression of diabetic maculopathy (within 6 months of operation)'),
            array('code' => 109, 'name' => 'overcorrection'),
            array('code' => 110, 'name' => 'pain'),
            array('code' => 111, 'name' => 'peripheral anterior synaechiae'),
            array('code' => 112, 'name' => 'poor cosmetic result'),
            array('code' => 113, 'name' => 'posterior synaechiae'),
            array('code' => 114, 'name' => 'precipitation of exudate'),
            array('code' => 115, 'name' => 'progressive rapid optic neuropathy (wipe out)'),
            array('code' => 116, 'name' => 'puncatate keratatis'),
            array('code' => 117, 'name' => 'recurrence of initial problem'),
            array('code' => 118, 'name' => 'reduced colour vision'),
            array('code' => 119, 'name' => 'retained antimetabolite sponge fragment'),
            array('code' => 120, 'name' => 'retinal detachment - non rhegmatogenous'),
            array('code' => 121, 'name' => 'rhinostomy fibrosis'),
            array('code' => 122, 'name' => 'rupture of choroidal neovascular membrane'),
            array('code' => 123, 'name' => 'scarring'),
            array('code' => 124, 'name' => 'scleritis'),
            array('code' => 125, 'name' => 'shallow AC: iris-cornea touch'),
            array('code' => 126, 'name' => 'silicone oil filling anterior chamber'),
            array('code' => 127, 'name' => 'slipped muscle'),
            array('code' => 128, 'name' => 'sterile ulcers'),
            array('code' => 129, 'name' => 'sympathetic ophthalmia'),
            array('code' => 130, 'name' => 'topical glaucoma medication introduced postoperatively'),
            array('code' => 131, 'name' => 'tube misdirection'),
            array('code' => 132, 'name' => 'tumour recurrence'),
            array('code' => 133, 'name' => 'under correction'),
            array('code' => 134, 'name' => 'visual loss (worse than 6/60 within 6 months of operation)'),
            array('code' => 135, 'name' => 'vitreous haemorrhage'),
            array('code' => 136, 'name' => 'webbing of surgical scar'),
            array('code' => 137, 'name' => 'wound infection'),
            array('code' => 999, 'name' => 'not recorded'),

        );
        foreach ($defaultComplications as $complications) {
            $this->insert('ophciexamination_postop_complications', $complications);
        }

        $this->createOETable(
            'et_ophciexamination_postop_complications',
            array(
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED NOT NULL',
                'eye_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 3',
            ),
            true
        );

        $this->addForeignKey('et_ophciexamination_postop_c_eye_id_fk', 'et_ophciexamination_postop_complications', 'eye_id', 'eye', 'id');
        $this->addForeignKey('et_ophciexamination_postop_c_ev_fk', 'et_ophciexamination_postop_complications', 'event_id', 'event', 'id');

        $this->createOETable(
            'ophciexamination_postop_et_complications',
            array(
                'id' => 'pk',
                'element_id' => 'INT(11) NOT NULL',
                'complication_id' => 'INT(11) NOT NULL',
                'eye_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 3',
                'operation_note_id' => 'INT(10) UNSIGNED NOT NULL',
            ),
            true
        );
        $this->addForeignKey('ophciexamination_postop_et_complications_eye_id_fk', 'ophciexamination_postop_et_complications', 'eye_id', 'eye', 'id');
        $this->addForeignKey('ophciexamination_postop_et_complications_et_fk', 'ophciexamination_postop_et_complications', 'element_id', 'et_ophciexamination_postop_complications', 'id');
        $this->addForeignKey('ophciexamination_postop_et_complications_co_fk', 'ophciexamination_postop_et_complications', 'complication_id', 'ophciexamination_postop_complications', 'id');

        $this->createOETable(
            'ophciexamination_postop_complications_subspecialty',
            array(
                'id' => 'pk',
                'subspecialty_id' => 'INT(10) UNSIGNED NOT NULL',
                'complication_id' => 'INT(11) NOT NULL',
                'display_order' => 'TINYINT(3) UNSIGNED NOT NULL',
                'right_complication_id' => 'INT(10) UNSIGNED DEFAULT NULL',
            ),
            true
        );

        $this->addForeignKey('ophciexamination_postop_complications_complicat_c_fk', 'ophciexamination_postop_complications_subspecialty', 'complication_id', 'ophciexamination_postop_complications', 'id');
        $this->addForeignKey('ophciexamination_postop_complications_subspecialty_c_fk', 'ophciexamination_postop_complications_subspecialty', 'subspecialty_id', 'subspecialty', 'id');

        $commonComplicationElement = array(
            'name' => 'Post-Op Complications',
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_PostOpComplications',
            'event_type_id' => 27,
            'display_order' => 120,
            'default' => 1,
            'required' => 0,
        );
        $this->insert('element_type', $commonComplicationElement);
    }

    public function safeDown()
    {
        $this->dropOETable('ophciexamination_postop_complications_subspecialty', true);
        $this->dropOETable('ophciexamination_postop_et_complications', true);
        $this->dropOETable('ophciexamination_postop_complications', true);
        $this->dropOETable('et_ophciexamination_postop_complications', true);

        $where = "name = 'Post-Op Complications'";
        $this->delete('element_type', $where);
    }
}
