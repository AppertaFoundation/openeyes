<?php

class m120326_122503_allow_null_in_assistant_id extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('et_ophtroperationnote_procedurelist_assistant_id_fk','et_ophtroperationnote_procedurelist');
		$this->dropIndex('et_ophtroperationnote_procedurelist_assistant_id_fk','et_ophtroperationnote_procedurelist');
	}

	public function down()
	{
		$this->createIndex('et_ophtroperationnote_procedurelist_assistant_id_fk','et_ophtroperationnote_procedurelist','assistant_id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_assistant_id_fk','et_ophtroperationnote_procedurelist','assistant_id','user','id');
	}
}
