<?php

class m131129_153827_correct_element_type_attributes extends CDbMigration
{
	public function up()
	{
		$opnote = $this->dbConnection->createCommand()->select("id")->from("event_type")
			->where("class_name = :class_name",array(":class_name" => "OphTrOperationnote"))->queryRow();

		$proclist = $this->dbConnection->createCommand()->select("id")->from("element_type")
			->where("event_type_id = :event_type_id and class_name = :proc_list_cls",
				array(":event_type_id"=>$opnote['id'],":proc_list_cls" => "Element_OphTrOperationnote_ProcedureList"))->queryRow();

		foreach (array(
			'Element_OphTrOperationnote_Vitrectomy',
			'Element_OphTrOperationnote_MembranePeel',
			'Element_OphTrOperationnote_Tamponade',
			'Element_OphTrOperationnote_Buckle',
			'Element_OphTrOperationnote_Cataract',
			'Element_OphTrOperationnote_GenericProcedure',
			'Element_OphTrOperationnote_Personnel'
			) as $class_name) {
			# not default elements
			$this->update('element_type', array('default' => false, 'parent_element_type_id' => $proclist['id']), 'class_name = :cname', array(':cname' => $class_name));
		}

		foreach (array(
					 'Element_OphTrOperationnote_Surgeon',
					 'Element_OphTrOperationnote_ProcedureList',
					 'Element_OphTrOperationnote_Anaesthetic',
					 'Element_OphTrOperationnote_PostOpDrugs',
					 'Element_OphTrOperationnote_Comments',
				 ) as $class_name) {
			# required
			$this->update('element_type', array('required' => true), 'class_name = :cname', array(':cname' => $class_name));
		}

	}

	public function down()
	{
		foreach (array(
			 'Element_OphTrOperationnote_Vitrectomy',
			 'Element_OphTrOperationnote_MembranePeel',
			 'Element_OphTrOperationnote_Tamponade',
			 'Element_OphTrOperationnote_Buckle',
			 'Element_OphTrOperationnote_Cataract',
			 'Element_OphTrOperationnote_GenericProcedure',
			 'Element_OphTrOperationnote_Personnel'
			 ) as $class_name) {
			# reset back to default
			$this->update('element_type', array('default' => true, 'parent_element_type_id' => null), 'class_name = :cname', array(':cname' => $class_name));
		}

		foreach (array(
					 'Element_OphTrOperationnote_Surgeon',
					 'Element_OphTrOperationnote_ProcedureList',
					 'Element_OphTrOperationnote_Anaesthetic',
					 'Element_OphTrOperationnote_PostOpDrugs',
					 'Element_OphTrOperationnote_Comments',
				 ) as $class_name) {
			# required back to null
			$this->update('element_type', array('required' => null), 'class_name = :cname', array(':cname' => $class_name));
		}
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
