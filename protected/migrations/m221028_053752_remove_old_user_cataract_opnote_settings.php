<?php

class m221028_053752_remove_old_user_cataract_opnote_settings extends OEMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$element_type = ElementType::model()->find('class_name = :class_name', array(':class_name' => 'Element_OphTrOperationnote_Cataract'));

		SettingUser::model()->deleteAll('element_type_id = :element_type_id', array(':element_type_id' => $element_type->id));
	}

	public function safeDown()
	{
		echo "m221028_053752_remove_old_user_cataract_opnote_settings does not support migration down.\n";
		return false;
	}
}
