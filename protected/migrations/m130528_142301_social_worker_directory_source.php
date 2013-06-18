<?php

class m130528_142301_social_worker_directory_source extends CDbMigration
{
	public function up()
	{
		$this->insert('import_source',array('name'=>'Social worker directory'));
	}

	public function down()
	{
		$this->delete('import_source',"name='Social worker directory'");
	}
}
