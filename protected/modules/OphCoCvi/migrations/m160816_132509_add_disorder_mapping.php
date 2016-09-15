<?php

class m160816_132509_add_disorder_mapping extends CDbMigration
{

    public function insert($table, $params)
    {
        if (array_key_exists('disorder_id', $params)) {
            $disorder = $this->dbConnection->createCommand()->select('id')->from('disorder')->where('id=:disorder_id', array(':disorder_id'=>$params['disorder_id']))->queryRow();
            if (!$disorder) {
                unset($params['disorder_id']);
            }
        }
        parent::insert($table, $params);
    }

	public function up()
	{
        $this->addColumn('ophcocvi_clinicinfo_disorder', 'disorder_id','int(10) unsigned');
        $this->addForeignKey('ophcocvi_clinicinfo_disorder_disorder_fk',
            'ophcocvi_clinicinfo_disorder','disorder_id',
            'disorder', 'id');
        $this->addColumn('ophcocvi_clinicinfo_disorder_version', 'disorder_id','int(10) unsigned');

        $this->addForeignKey('ophcocvi_clinicinfo_disorder_section_fk',
            'ophcocvi_clinicinfo_disorder','section_id',
            'ophcocvi_clinicinfo_disorder_section', 'id');

        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'age-related macular degeneration - subretinal neovascularisation',
            'code' => 'H35.3', 'section_id' => 1, 'active' => 1,'display_order' => 1, 'disorder_id' => 267718000));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'age-related macular degeneration - atrophic / geographic macular atrophy',
            'code' => 'H35.3', 'section_id' => 1, 'active' => 1,'display_order' => 2, 'disorder_id' => 267718000));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'diabetic retinopathy',
            'code' => 'E10.3 – E14.3 H36.0', 'section_id' => 1, 'active' => 1,'display_order' => 3, 'disorder_id' => 4855003));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'hereditary retinal dystrophy',
            'code' => 'H35.5', 'section_id' => 1, 'active' => 1,'display_order' => 4, 'disorder_id' => 41799005));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'retinal vascular occlusions',
            'code' => 'H34.', 'section_id' => 1, 'active' => 1,'display_order' => 5, 'disorder_id' => 73757007));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'primary open angle',
            'code' => 'H40.1', 'section_id' => 2, 'active' => 1,'display_order' => 6, 'disorder_id' => 77075001));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'primary angle closure',
            'code' => 'H40.2', 'section_id' => 2, 'active' => 1,'display_order' => 7, 'disorder_id' => 392288006));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'secondary',
            'code' => 'H40.5', 'section_id' => 2, 'active' => 1,'display_order' => 8, 'disorder_id' => 95717004));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'degenerative myopia',
            'code' => 'H44.2', 'section_id' => 3, 'active' => 1,'display_order' => 9, 'disorder_id' => 32022003));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'optic atrophy',
            'code' => 'H47.2', 'section_id' => 4, 'active' => 1,'display_order' => 10, 'disorder_id' => 76976005));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'visual cortex disorder',
            'code' => 'H47.6', 'section_id' => 4, 'active' => 1,'display_order' => 11, 'disorder_id' => 128329001));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'cerebrovascular disease',
            'code' => 'H47.6', 'section_id' => 4, 'active' => 1,'display_order' => 12, 'disorder_id' => 62914000));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'chorioretinitis',
            'code' => 'H30.9', 'section_id' => 5, 'active' => 1,'display_order' => 13, 'disorder_id' => 46627006));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'choroidal degeneration',
            'code' => 'H31.1', 'section_id' => 5, 'active' => 1,'display_order' => 14, 'disorder_id' => 406446000));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'cataract (excludes congenital)',
            'code' => 'H25.9', 'section_id' => 6, 'active' => 1,'display_order' => 15, 'disorder_id' => 193570009));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'corneal scars and opacities',
            'code' => 'H17.', 'section_id' => 7, 'active' => 1,'display_order' => 16, 'disorder_id' => 193795008));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'keratitis',
            'code' => 'H16.7', 'section_id' => 7, 'active' => 1,'display_order' => 17, 'disorder_id' => 5888003));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'retinopathy of prematurity',
            'code' => 'H35.1', 'section_id' => 8, 'active' => 1,'display_order' => 18, 'disorder_id' => 415297005));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'congenital CNS malformations',
            'code' => 'Q00-Q07', 'section_id' => 8, 'active' => 1,'display_order' => 19, 'disorder_id' => 253193002));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'congenital eye malformations',
            'code' => 'Q10-Q15', 'section_id' => 8, 'active' => 1,'display_order' => 20, 'disorder_id' => 19416009));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'eye',
            'code' => 'C69', 'section_id' => 9, 'active' => 1,'display_order' => 21, 'disorder_id' => 371486001));
        $this->insert('ophcocvi_clinicinfo_disorder',array('name' => 'brain & CNS',
            'code' => 'C71-72', 'section_id' => 9, 'active' => 1,'display_order' => 22, 'disorder_id' => 126952004));
	}

	public function down()
	{
        $this->truncateTable('ophcocvi_clinicinfo_disorder');

        $this->dropForeignKey('ophcocvi_clinicinfo_disorder_section_fk',
            'ophcocvi_clinicinfo_disorder');

        $this->dropForeignKey('ophcocvi_clinicinfo_disorder_disorder_fk',
            'ophcocvi_clinicinfo_disorder');
        $this->dropColumn('ophcocvi_clinicinfo_disorder', 'disorder_id');
		$this->dropColumn('ophcocvi_clinicinfo_disorder_version', 'disorder_id');
	}

}
