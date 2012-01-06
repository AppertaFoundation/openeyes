<?php

class m120106_114403_new_common_ophthalmic_disorders extends CDbMigration
{
	public function up()
	{
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 186678004, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 231855007, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 69278003, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 410692006, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 24010005, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 128350005, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 41446000, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 47704002, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 68659002, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 193570009, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 231861005, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 253230008, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 95479005, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 418134006, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 9826008, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 231857004, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 87513003, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 299699004, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 302896008, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 255004001, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 34250006, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 255005000, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 231938007, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 87614000, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 415172002, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 77489003, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 314765000, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 95717004, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 83901003, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 314561006, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 73442001, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 231903005, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 415735001, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 60332004, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 363463000, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 317349009, 'specialty_id' => 13));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 16596007, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 399054005, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 62176008, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 46343005, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 194131002, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 67320001, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 232101009, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 27590007, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 63988001, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 387742006, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 38101003, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 57190000, 'specialty_id' => 11));
		$this->insert('common_ophthalmic_disorder', array('disorder_id' => 82649003, 'specialty_id' => 11));
	}

	public function down()
	{
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 186678004, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 231855007, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 69278003, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 410692006, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 24010005, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 128350005, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 41446000, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 47704002, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 68659002, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 193570009, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 231861005, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 253230008, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 95479005, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 418134006, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 9826008, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 231857004, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 87513003, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 299699004, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 302896008, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 255004001, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 34250006, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 255005000, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 231938007, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 87614000, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 415172002, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 77489003, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 314765000, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 95717004, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 83901003, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 314561006, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 73442001, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 231903005, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 415735001, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 60332004, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 363463000, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 317349009, 'specialty_id' => 13));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 16596007, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 399054005, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 62176008, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 46343005, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 194131002, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 67320001, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 232101009, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 27590007, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 63988001, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 387742006, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 38101003, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 57190000, 'specialty_id' => 11));
		$this->delete('common_ophthalmic_disorder', 'disorder_id = :disorder_id and specialty_id = :specialty_id', array('disorder_id' => 82649003, 'specialty_id' => 11));
	}
}
