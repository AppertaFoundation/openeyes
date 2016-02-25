<?php

class m140812_111740_fix_db_schema extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('et_ophtroperationnote_trabeculectomy_size_id_fk','et_ophtroperationnote_trabeculectomy');
		$this->dropForeignKey('et_ophtroperationnote_trabeculectomy_site_id_fk','et_ophtroperationnote_trabeculectomy');
		$this->dropIndex('et_ophtroperationnote_trabeculectomy_size_id_fk','et_ophtroperationnote_trabeculectomy');

		$this->addForeignKey('et_ophtroperationnote_trabeculectomy_size_id_fk','et_ophtroperationnote_trabeculectomy','size_id','ophtroperationnote_trabeculectomy_size','id');
		$this->addForeignKey('et_ophtroperationnote_trabeculectomy_site_id_fk','et_ophtroperationnote_trabeculectomy','site_id','ophtroperationnote_trabeculectomy_site','id');
	}

	public function down()
	{
		$this->dropForeignKey('et_ophtroperationnote_trabeculectomy_size_id_fk','et_ophtroperationnote_trabeculectomy');
		$this->dropIndex('et_ophtroperationnote_trabeculectomy_size_id_fk','et_ophtroperationnote_trabeculectomy');

		$this->addForeignKey('et_ophtroperationnote_trabeculectomy_size_id_fk','et_ophtroperationnote_trabeculectomy','site_id','ophtroperationnote_trabeculectomy_size','id');
	}
}
