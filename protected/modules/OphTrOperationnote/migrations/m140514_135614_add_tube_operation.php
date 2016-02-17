<?php

class m140514_135614_add_tube_operation extends OEMigration
{
	public function up()
	{
		$event_type = $this->dbConnection->createCommand()->select("*")->from("event_type")->where("class_name = :class_name",array(":class_name"=>"OphTrOperationnote"))->queryRow();

		$ets = $this->insertOEElementType(array('Element_OphTrOperationnote_GlaucomaTube' =>
						array(
								'name' => 'Glaucoma Tube' ,
								'display_order' => 15,
								'parent_element_type_id' => 'Element_OphTrOperationnote_ProcedureList',
								'required' => false,
								'default' => false
						)), $event_type['id']);

		$element_type_id = $ets[0];

		$this->createOETable('ophtroperationnote_gt_plateposition', array(
					'id' => 'pk',
						'name' => 'string NOT NULL',
						'active' => 'boolean NOT NULL DEFAULT true',
						'display_order' => 'integer NOT NULL',
						'eyedraw_value' => 'string NOT NULL'
				), true);


		$this->createOETable('ophtroperationnote_gt_tubeposition', array(
						'id' => 'pk',
						'name' => 'string NOT NULL',
						'active' => 'boolean NOT NULL DEFAULT true',
						'display_order' => 'integer NOT NULL',
				), true);

		$this->createOETable('et_ophtroperationnote_glaucomatube', array(
						'id' => 'pk',
						'event_id' => 'int(10) unsigned NOT NULL',
						'plate_position_id' => 'integer NOT NULL',
						'plate_limbus' => 'integer NOT NULL',
						'tube_position_id' => 'integer NOT NULL',
						'stent' => 'boolean',
						'slit' => 'boolean',
						'visco_in_ac' => 'boolean',
						'flow_tested' => 'boolean',
						'eyedraw' => 'text',
						'description' => 'text NOT NULL',
				), true);

		$this->addForeignKey('et_ophtroperationnote_glautub_ev_fk',
			'et_ophtroperationnote_glaucomatube', 'event_id', 'event', 'id');
		$this->addForeignKey('et_ophtroperationnote_glaucomatube_ppos_fk',
				'et_ophtroperationnote_glaucomatube',
				'plate_position_id', 'ophtroperationnote_gt_plateposition', 'id');
		$this->addForeignKey('et_ophtroperationnote_glaucomatube_tpos_fk',
				'et_ophtroperationnote_glaucomatube',
				'tube_position_id', 'ophtroperationnote_gt_tubeposition', 'id');

		$procs = $this->dbConnection->createCommand()->select("id")->from("proc")->where(array("in", "snomed_code", array(265291005,440587008)))->queryAll();

		foreach ($procs as $p) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$p['id'],'element_type_id'=>$element_type_id));
		}

		$migrations_path = dirname(__FILE__);
		$this->initialiseData($migrations_path);
	}

	public function down()
	{
		$this->dropOETable('et_ophtroperationnote_glaucomatube', true);
		$this->dropOETable('ophtroperationnote_gt_plateposition', true);
		$this->dropOETable('ophtroperationnote_gt_tubeposition', true);

		$element_type_id = $this->getIdOfElementTypeByClassName('Element_OphTrOperationnote_GlaucomaTube');
		$this->delete('ophtroperationnote_procedure_element', 'element_type_id = ?', array($element_type_id));
		$this->delete('element_type', 'class_name = ?', array('Element_OphTrOperationnote_GlaucomaTube'));
	}

}