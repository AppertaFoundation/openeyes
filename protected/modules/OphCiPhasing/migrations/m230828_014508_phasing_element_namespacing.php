<?php

class m230828_014508_phasing_element_namespacing extends OEMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$this->updateElementTypeClassName('Element_OphCiPhasing_IntraocularPressure', 'Element_OphCiPhasing_IntraocularPressure', 'OphCiPhasing', true);
	}

	public function safeDown()
	{
		echo "m230828_014508_phasing_element_namespacing does not support migration down.\n";
		return false;
	}
}
