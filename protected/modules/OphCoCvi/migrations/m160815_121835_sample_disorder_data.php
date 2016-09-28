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
	}

	public function down()
	{
        $this->truncateTable('ophcocvi_clinicinfo_disorder_section');
	}

}