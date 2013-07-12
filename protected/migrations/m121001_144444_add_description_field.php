<?php

class m121001_144444_add_description_field extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('drug','code');
		$this->addColumn('drug','tallman','varchar(100)');
		$this->alterColumn('drug','name','varchar(100)');
		foreach (Drug::model()->findAll() as $drug) {
			$drug->tallman = $drug->name;
			$drug->name = ucfirst(strtolower($drug->name));
			$drug->save();
		}
	}

	public function down()
	{
		foreach (Drug::model()->findAll() as $drug) {
			$drug->name = $drug->tallman;
			$drug->save();
		}
		$this->dropColumn('drug','tallman');
		$this->alterColumn('drug','name','varchar(64)');
		$this->addColumn('drug','code','varchar(40)');
	}

}
