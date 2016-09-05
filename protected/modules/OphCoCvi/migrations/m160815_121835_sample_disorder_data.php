<?php

class m160815_121835_sample_disorder_data extends CDbMigration
{
	public function up()
	{
        $this->insert('ophcocvi_clinicinfo_disorder_section',array('name'=>'Retina', 'comments_allowed' => 1,
            'comments_label' => 'other retinal : please specify', 'display_order' => 1, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section',array('name'=>'Glaucoma', 'comments_allowed' => 1,
            'comments_label' => 'other glaucoma : please specify', 'display_order' => 2, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section',array('name'=>'Globe', 'comments_allowed' => 0,
            'comments_label' => '', 'display_order' => 3, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section',array('name'=>'Neurological', 'comments_allowed' => 0,
            'comments_label' => '', 'display_order' => 4, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section',array('name'=>'Choroid', 'comments_allowed' => 0,
            'comments_label' => '', 'display_order' => 5, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section',array('name'=>'Lens', 'comments_allowed' => 0,
            'comments_label' => '', 'display_order' => 6, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section',array('name'=>'Cornea', 'comments_allowed' => 0,
            'comments_label' => '', 'display_order' => 7, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section',array('name'=>'Paediatric', 'comments_allowed' => 1,
            'comments_label' => 'congenital: please specify syndrome or nature of the malformation',
            'display_order' => 8, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section',array('name'=>'Neoplasia', 'comments_allowed' => 1,
            'comments_label' => 'other neoplasia: please specify', 'display_order' => 9, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'age-related macular degeneration - subretinal neovascularisation',
            'code' => 'H35.3', 'section_id' => 1, 'active' => 1,'display_order' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'age-related macular degeneration - atrophic / geographic macular atrophy',
            'code' => 'H35.3', 'section_id' => 1, 'active' => 1,'display_order' => 2));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'diabetic retinopathy',
            'code' => 'E10.3 – E14.3 H36.0', 'section_id' => 1, 'active' => 1,'display_order' => 3));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'hereditary retinal dystrophy',
            'code' => 'H35.5', 'section_id' => 1, 'active' => 1,'display_order' => 4));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'retinal vascular occlusions',
            'code' => 'H34.', 'section_id' => 1, 'active' => 1,'display_order' => 5));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'primary open angle',
            'code' => 'H40.1', 'section_id' => 2, 'active' => 1,'display_order' => 6));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'primary open closure',
            'code' => 'H40.2', 'section_id' => 2, 'active' => 1,'display_order' => 7));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'secondary',
            'code' => 'H40.5', 'section_id' => 2, 'active' => 1,'display_order' => 8));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'degenerative myopia',
            'code' => 'H44.2', 'section_id' => 3, 'active' => 1,'display_order' => 9));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'optic atrophy',
            'code' => 'H47.2', 'section_id' => 4, 'active' => 1,'display_order' => 10));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'visual cortex disorder',
            'code' => 'H47.6', 'section_id' => 4, 'active' => 1,'display_order' => 11));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'chorioretinitis',
            'code' => 'H30.9', 'section_id' => 5, 'active' => 1,'display_order' => 12));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'choroidal degeneration',
            'code' => 'H31.1', 'section_id' => 5, 'active' => 1,'display_order' => 13));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'cataract (excludes congenital)',
            'code' => 'H25.9', 'section_id' => 6, 'active' => 1,'display_order' => 14));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'corneal scars and opacities',
            'code' => 'H17.', 'section_id' => 7, 'active' => 1,'display_order' => 15));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'keratitis',
            'code' => 'H16.7', 'section_id' => 7, 'active' => 1,'display_order' => 16));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'retinopathy of prematurity',
            'code' => 'H35.1', 'section_id' => 8, 'active' => 1,'display_order' => 17));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'congenital CNS malformations',
            'code' => 'Q00-Q07', 'section_id' => 8, 'active' => 1,'display_order' => 18));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'congenital eye malformations',
            'code' => 'Q10-Q15', 'section_id' => 8, 'active' => 1,'display_order' => 19));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'eye',
            'code' => 'C69', 'section_id' => 9, 'active' => 1,'display_order' => 20));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'brain & CNS',
            'code' => 'C71-72', 'section_id' => 9, 'active' => 1,'display_order' => 21));
	}

	public function down()
	{
        $this->truncateTable('ophcocvi_clinicinfo_disorder');
        $this->truncateTable('ophcocvi_clinicinfo_disorder_section');
	}

}