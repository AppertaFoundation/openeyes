<?php

class m140513_101425_add_trabectome_element extends OEMigration
{
	public function up()
	{

		$event_type_id = $this->insertOEEventType( 'Operation Note', 'OphTrOperationnote', 'Ci');
		$et_ids = $this->insertOEElementType(array('Element_OphTrOperationnote_Trabectome' =>
						array(
								'name' => 'Trabectome' ,
								'parent_element_type_id' => 'Element_OphTrOperationnote_ProcedureList',
								'display_order' => 20,
								'required' => false
						)), $event_type_id);
		$element_type_id = $et_ids[0];

		$proc = $this->dbConnection->createCommand()->select("id")->from("proc")->where("snomed_code = :code",array(":code" => "11000163100"))->queryRow();

		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type_id));
		}
		else {
			echo "***** WARNING *****\nCreating Trabectome element, but no procedure found to associate with the the element\n";
		}

		$this->createOETable('ophtroperationnote_trabectome_power', array(
						'id' => 'pk',
						'name' => 'string NOT NULL',
						'active' => 'boolean NOT NULL DEFAULT true',
						'display_order' => 'integer NOT NULL',
				), true);

		$this->createOETable('ophtroperationnote_trabectome_complication', array(
						'id' => 'pk',
						'name' => 'string NOT NULL',
						'active' => 'boolean NOT NULL DEFAULT true',
						'other' => 'boolean NOT NULL DEFAULT false',
						'display_order' => 'integer NOT NULL',
				), true);

		$this->createOETable('et_ophtroperationnote_trabectome', array(
						'id' => 'pk',
						'event_id' => 'int(10) unsigned NOT NULL',
						'power_id' => 'integer NOT NULL',
						'blood_reflux' => 'boolean',
						'hpmc' => 'boolean',
						'eyedraw' => 'text',
						'description' => 'text',
						'complication_other' => 'text',
				), true);

		$this->addForeignKey('et_ophtroperationnote_trabectome_ev_fk',
			'et_ophtroperationnote_trabectome', 'event_id', 'event', 'id');

		$this->addForeignKey('et_ophtroperationnote_trabectome_power_id',
			'et_ophtroperationnote_trabectome', 'power_id',
			'ophtroperationnote_trabectome_power', 'id');

		$this->createOETable('ophtroperationnote_trabectome_comp_ass', array(
						'id' => 'pk',
						'element_id' => 'integer NOT NULL',
						'complication_id' => 'integer NOT NULL',
				), true);

		$this->addForeignKey('ophtroperationnote_trabectome_comp_ass_elui_fk',
				'ophtroperationnote_trabectome_comp_ass',
				'element_id', 'et_ophtroperationnote_trabectome', 'id');
		$this->addForeignKey('ophtroperationnote_trabectome_comp_ass_cmpui_fk',
				'ophtroperationnote_trabectome_comp_ass',
				'complication_id', 'ophtroperationnote_trabectome_complication', 'id');

		$migrations_path = dirname(__FILE__);
		$this->initialiseData($migrations_path);
	}

	public function down()
	{
		$this->dropOETable('ophtroperationnote_trabectome_comp_ass', true);
		$this->dropOETable('et_ophtroperationnote_trabectome', true);
		$this->dropOETable('ophtroperationnote_trabectome_complication', true);
		$this->dropOETable('ophtroperationnote_trabectome_power', true);

		$element_type_id = $this->getIdOfElementTypeByClassName('Element_OphTrOperationnote_Trabectome');
		$this->delete('ophtroperationnote_procedure_element', 'element_type_id = ?', array($element_type_id));
		$this->delete('element_type', 'id = ?', array($element_type_id));
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}