<?php

class m140219_143514_update_elements_required_prop extends CDbMigration
{
	public function up()
	{
		$this->update('element_type', array('required' => 0), "class_name = 'Element_OphTrOperationnote_Preparation'");
		$this->update('element_type', array('required' => 0), "class_name = 'Element_OphTrOperationnote_GenericProcedure'");
		$this->update('element_type', array('required' => 0), "class_name = 'Element_OphTrOperationnote_Cataract'");
		$this->update('element_type', array('required' => 0), "class_name = 'Element_OphTrOperationnote_Buckle'");
		$this->update('element_type', array('required' => 0), "class_name = 'Element_OphTrOperationnote_Tamponade'");
		$this->update('element_type', array('required' => 0), "class_name = 'Element_OphTrOperationnote_MembranePeel'");
		$this->update('element_type', array('required' => 0), "class_name = 'Element_OphTrOperationnote_Vitrectomy'");
		$this->update('element_type', array('required' => 0), "class_name = 'Element_OphTrOperationnote_Personnel'");

	}

	public function down()
	{
		$this->update('element_type', array('required' => NULL), "class_name = 'Element_OphTrOperationnote_Preparation'");
		$this->update('element_type', array('required' => NULL), "class_name = 'Element_OphTrOperationnote_GenericProcedure'");
		$this->update('element_type', array('required' => NULL), "class_name = 'Element_OphTrOperationnote_Cataract'");
		$this->update('element_type', array('required' => NULL), "class_name = 'Element_OphTrOperationnote_Buckle'");
		$this->update('element_type', array('required' => NULL), "class_name = 'Element_OphTrOperationnote_Tamponade'");
		$this->update('element_type', array('required' => NULL), "class_name = 'Element_OphTrOperationnote_MembranePeel'");
		$this->update('element_type', array('required' => NULL), "class_name = 'Element_OphTrOperationnote_Vitrectomy'");
		$this->update('element_type', array('required' => NULL), "class_name = 'Element_OphTrOperationnote_Personnel'");
	}
}