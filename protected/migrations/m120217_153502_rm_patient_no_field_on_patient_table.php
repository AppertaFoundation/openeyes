<?php

class m120217_153502_rm_patient_no_field_on_patient_table extends CDbMigration
{
	public $hashes = array();

	public function up()
	{
		$this->addColumn('patient','hash','varchar(40) COLLATE utf8_bin NOT NULL');

		foreach (Patient::Model()->findAll() as $patient) {
			$command = $this->dbConnection->createCommand("update patient set hash='".$this->hash()."' where id = $patient->id");
			$command->execute();
		}
	}

	public function hash() {
		while (1) {
			$hash = sha1(rand());

			if (!in_array($hash, $this->hashes)) {
				$this->hashes[] = $hash;
				return $hash;
			}
		}
	}

	public function down()
	{
		$this->dropColumn('patient','hash');
	}
}
