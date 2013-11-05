<?php

class m121001_144444_add_description_field extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('drug','code');
		$this->addColumn('drug','tallman','varchar(100)');
		$this->alterColumn('drug','name','varchar(100)');

		$db = $this->getDbConnection();
		$drugs =  $db->createCommand('select * from drug')->queryAll();
		foreach ($drugs as $drug) {
			$this->update('drug', array('tallman'=>$drug['name'], 'name'=>ucfirst( strtolower($drug['name']) ) ),
				' id=:id', array(':id'=> $drug['id'])
			);
			//$drug->tallman = $drug->name;
			//$drug->name = ucfirst(strtolower($drug->name));
			//$drug->save();
		}
	}

	public function down()
	{
		$db = $this->getDbConnection();
		$drugs =  $db->createCommand('select * from drug')->queryAll();
		foreach ($drugs as $drug) {
			$this->update('drug', array( 'name'=>$drug['tallman']),
				' id=:id', array(':id', $drug['id'])
			);

		}
		$this->dropColumn('drug','tallman');
		$this->alterColumn('drug','name','varchar(64)');
		$this->addColumn('drug','code','varchar(40)');
	}

}
