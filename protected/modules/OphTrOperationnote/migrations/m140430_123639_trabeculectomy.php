<?php

class m140430_123639_trabeculectomy extends OEMigration
{
	public function up()
	{
		$this->createOETable('ophtroperationnote_trabeculectomy_conjunctival_flap_type', array('id' => 'pk', 'name' => 'string'), true);

		$this->insert('ophtroperationnote_trabeculectomy_conjunctival_flap_type', array('name' => 'Fornix-based'));
		$this->insert('ophtroperationnote_trabeculectomy_conjunctival_flap_type', array('name' => 'Limbus-based'));

		$this->createOETable('ophtroperationnote_trabeculectomy_site', array('id' => 'pk', 'name' => 'string'), true);

		$this->insert('ophtroperationnote_trabeculectomy_site', array('name' => 'Superior'));
		$this->insert('ophtroperationnote_trabeculectomy_site', array('name' => 'Superonasal'));
		$this->insert('ophtroperationnote_trabeculectomy_site', array('name' => 'Superotemporal'));

		$this->createOETable('ophtroperationnote_trabeculectomy_size', array('id' => 'pk', 'name' => 'string'), true);

		$this->insert('ophtroperationnote_trabeculectomy_site', array('name' => '4x3'));
		$this->insert('ophtroperationnote_trabeculectomy_site', array('name' => '5x2'));

		$this->createOETable('ophtroperationnote_trabeculectomy_sclerostomy_type', array('id' => 'pk', 'name' => 'string'), true);

		$this->insert('ophtroperationnote_trabeculectomy_sclerostomy_type', array('name' => 'Punch'));
		$this->insert('ophtroperationnote_trabeculectomy_sclerostomy_type', array('name' => 'Block'));
		$this->insert('ophtroperationnote_trabeculectomy_sclerostomy_type', array('name' => 'Ex-Press shunt'));

		$this->createOETable('ophtroperationnote_trabeculectomy_viscoelastic_type', array('id' => 'pk', 'name' => 'string'), true);

		$this->insert('ophtroperationnote_trabeculectomy_viscoelastic_type', array('name' => 'HPMC'));
		$this->insert('ophtroperationnote_trabeculectomy_viscoelastic_type', array('name' => 'Healon'));
		$this->insert('ophtroperationnote_trabeculectomy_viscoelastic_type', array('name' => 'Provisc'));

		$this->createOETable('ophtroperationnote_trabeculectomy_viscoelastic_flow', array('id' => 'pk', 'name' => 'string'), true);

		$this->insert('ophtroperationnote_trabeculectomy_viscoelastic_flow', array('name' => 'Slow'));
		$this->insert('ophtroperationnote_trabeculectomy_viscoelastic_flow', array('name' => 'With pressure'));

		$this->createOETable('ophtroperationnote_trabeculectomy_difficulty', array('id' => 'pk', 'name' => 'string'), true);

		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Big pupil'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Ciliary Body bleeding'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Conjunctival button hole'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Corneal Opacity'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Deep-set eye'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Excessive bleeding'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Flap button hole'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Free Flap'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Myopia'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'No iridotomy'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Partial iridotomy'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Previous scarring'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Shallow anterior chamber'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Small pupil'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Thin Conjunctiva'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Thin Flap'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Thin Sclera'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Uncontrolled eye movement'));
		$this->insert('ophtroperationnote_trabeculectomy_difficulty', array('name' => 'Uncooperative patient'));

		$this->createOETable('ophtroperationnote_trabeculectomy_complication', array('id' => 'pk', 'name' => 'string'), true);

		$this->insert('ophtroperationnote_trabeculectomy_complication', array('name' => 'Conjunctival tear'));
		$this->insert('ophtroperationnote_trabeculectomy_complication', array('name' => 'Haemorrhage'));
		$this->insert('ophtroperationnote_trabeculectomy_complication', array('name' => 'Endothelial damage'));
		$this->insert('ophtroperationnote_trabeculectomy_complication', array('name' => 'Iris damage'));
		$this->insert('ophtroperationnote_trabeculectomy_complication', array('name' => 'Lens damage'));
		$this->insert('ophtroperationnote_trabeculectomy_complication', array('name' => 'Vitreous loss'));

		$this->createOETable(
			'et_ophtroperationnote_trabeculectomy',
			array(
				'id' => 'pk',
				'eyedraw' => 'text',
				'conjunctival_flap_type_id' => 'integer not null',
				'stay_suture' => 'boolean',
				'site_id' => 'integer not null',
				'size_id' => 'integer not null',
				'sclerostomy_type_id' => 'integer not null',
				'27_guage_needle' => 'boolean',
				'ac_maintainer' => 'boolean',
				'viscoelastic_type_id' => 'integer',
				'viscoelastic_removed' => 'boolean',
				'viscoelastic_flow_id' => 'integer',
				'report' => 'text',
				'difficulty_other' => 'string',
				'complication_other' => 'string',
				'constraint et_ophtroperationnote_trabeculectomy_cftid_fk foreign key (conjunctival_flap_type_id) references ophtroperationnote_trabeculectomy_conjunctival_flap_type (id)',
				'constraint et_ophtroperationnote_trabeculectomy_site_id_fk foreign key (site_id) references ophtroperationnote_trabeculectomy_site (id)',
				'constraint et_ophtroperationnote_trabeculectomy_size_id_fk foreign key (site_id) references ophtroperationnote_trabeculectomy_size (id)',
				'constraint et_ophtroperationnote_trabeculectomy_sctid_fk foreign key (sclerostomy_type_id) references ophtroperationnote_trabeculectomy_sclerostomy_type (id)',
				'constraint et_ophtroperationnote_trabeculectomy_vetid_fk foreign key (viscoelastic_type_id) references ophtroperationnote_trabeculectomy_viscoelastic_type (id)',
				'constraint et_ophtroperationnote_trabeculectomy_vefid_fl foreign key (viscoelastic_flow_id) references ophtroperationnote_trabeculectomy_viscoelastic_flow (id)',
			),
			true
		);

		$this->createOETable(
			'ophtroperationnote_trabeculectomy_difficulties',
			array(
				'id' => 'pk',
				'element_id' => 'integer not null',
				'difficulty_id' => 'integer not null',
				'constraint ophtroperationnote_trabeculectomy_difficulties_element_id_fk foreign key (element_id) references et_ophtroperationnote_trabeculectomy (id)',
				'constraint ophtroperationnote_trabeculectomy_difficulties_difficulty_id_fk foreign key (difficulty_id) references ophtroperationnote_trabeculectomy_difficulty (id)',
			),
			true
		);

		$this->createOETable(
			'ophtroperationnote_trabeculectomy_complications',
			array(
				'id' => 'pk',
				'element_id' => 'integer not null',
				'complication_id' => 'integer not null',
				'constraint ophtroperationnote_trabeculectomy_complications_element_id_fk foreign key (element_id) references et_ophtroperationnote_trabeculectomy (id)',
				'constraint ophtroperationnote_trabeculectomy_complications_difficulty_id_fk foreign key (complication_id) references ophtroperationnote_trabeculectomy_complication (id)',
			),
			true
		);

		$this->createElementType('OphTrOperationnote', 'Trabeculectomy', array('display_order' => 20, 'parent_name' => 'ProcedureList'));
	}

	public function down()
	{
		$this->delete('element_type', 'class_name = ?', array('Element_OphTrOperationnote_Trabeculectomy'));

		$this->dropTable('ophtroperationnote_trabeculectomy_difficulties');
		$this->dropTable('ophtroperationnote_trabeculectomy_difficulties_version');
		$this->dropTable('ophtroperationnote_trabeculectomy_complications');
		$this->dropTable('ophtroperationnote_trabeculectomy_complications_version');

		$this->dropTable('et_ophtroperationnote_trabeculectomy');
		$this->dropTable('et_ophtroperationnote_trabeculectomy_version');

		$this->dropTable('ophtroperationnote_trabeculectomy_conjunctival_flap_type');
		$this->dropTable('ophtroperationnote_trabeculectomy_conjunctival_flap_type_version');
		$this->dropTable('ophtroperationnote_trabeculectomy_site');
		$this->dropTable('ophtroperationnote_trabeculectomy_site_version');
		$this->dropTable('ophtroperationnote_trabeculectomy_size');
		$this->dropTable('ophtroperationnote_trabeculectomy_size_version');
		$this->dropTable('ophtroperationnote_trabeculectomy_sclerostomy_type');
		$this->dropTable('ophtroperationnote_trabeculectomy_sclerostomy_type_version');
		$this->dropTable('ophtroperationnote_trabeculectomy_viscoelastic_type');
		$this->dropTable('ophtroperationnote_trabeculectomy_viscoelastic_type_version');
		$this->dropTable('ophtroperationnote_trabeculectomy_viscoelastic_flow');
		$this->dropTable('ophtroperationnote_trabeculectomy_viscoelastic_flow_version');

		$this->dropTable('ophtroperationnote_trabeculectomy_difficulty');
		$this->dropTable('ophtroperationnote_trabeculectomy_difficulty_version');
		$this->dropTable('ophtroperationnote_trabeculectomy_complication');
		$this->dropTable('ophtroperationnote_trabeculectomy_complication_version');
	}
}
