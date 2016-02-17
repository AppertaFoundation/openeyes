<?php

class m140514_133124_trabeculectomy_changes extends OEMigration
{
	public function up()
	{
		$this->dropColumn('et_ophtroperationnote_trabeculectomy','27_guage_needle');
		$this->dropColumn('et_ophtroperationnote_trabeculectomy_version','27_guage_needle');
		$this->dropColumn('et_ophtroperationnote_trabeculectomy','ac_maintainer');
		$this->dropColumn('et_ophtroperationnote_trabeculectomy_version','ac_maintainer');

		$this->addColumn('ophtroperationnote_trabeculectomy_difficulty','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_difficulty_version','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_complication','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_complication_version','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_conjunctival_flap_type','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_conjunctival_flap_type_version','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_site','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_site_version','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_size','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_size_version','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_sclerostomy_type','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_sclerostomy_type_version','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_viscoelastic_type','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_viscoelastic_type_version','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_viscoelastic_flow','display_order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationnote_trabeculectomy_viscoelastic_flow_version','display_order','tinyint(1) unsigned not null');

		$this->delete('ophtroperationnote_trabeculectomy_difficulty');
		$this->delete('ophtroperationnote_trabeculectomy_complication');
		$this->delete('ophtroperationnote_trabeculectomy_conjunctival_flap_type');
		$this->delete('ophtroperationnote_trabeculectomy_site');
		$this->delete('ophtroperationnote_trabeculectomy_size');
		$this->delete('ophtroperationnote_trabeculectomy_sclerostomy_type');
		$this->delete('ophtroperationnote_trabeculectomy_viscoelastic_type');
		$this->delete('ophtroperationnote_trabeculectomy_viscoelastic_flow');
	
		$this->addColumn('et_ophtroperationnote_trabeculectomy','event_id','int(10) unsigned not null');
		$this->addColumn('et_ophtroperationnote_trabeculectomy_version','event_id','int(10) unsigned not null');
		$this->createIndex('et_ophtroperationnote_trabeculectomy_event_id_fk','et_ophtroperationnote_trabeculectomy','event_id');
		$this->addForeignKey('et_ophtroperationnote_trabeculectomy_event_id_fk','et_ophtroperationnote_trabeculectomy','event_id','event','id');

		$this->initialiseData(dirname(__FILE__));
	}

	public function down()
	{
		$this->dropForeignKey('et_ophtroperationnote_trabeculectomy_event_id_fk','et_ophtroperationnote_trabeculectomy');
		$this->dropColumn('et_ophtroperationnote_trabeculectomy','event_id');
		$this->dropColumn('et_ophtroperationnote_trabeculectomy_version','event_id');

		$this->dropColumn('ophtroperationnote_trabeculectomy_difficulty','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_difficulty_version','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_complication','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_complication_version','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_conjunctival_flap_type','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_conjunctival_flap_type_version','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_site','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_site_version','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_size','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_size_version','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_sclerostomy_type','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_sclerostomy_type_version','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_viscoelastic_type','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_viscoelastic_type_version','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_viscoelastic_flow','display_order');
		$this->dropColumn('ophtroperationnote_trabeculectomy_viscoelastic_flow_version','display_order');

		$this->addColumn('et_ophtroperationnote_trabeculectomy','27_guage_needle','tinyint(1) unsigned not null');
		$this->addColumn('et_ophtroperationnote_trabeculectomy_version','27_guage_needle','tinyint(1) unsigned not null');
		$this->addColumn('et_ophtroperationnote_trabeculectomy','ac_maintainer','tinyint(1) unsigned not null');
		$this->addColumn('et_ophtroperationnote_trabeculectomy_version','ac_maintainer','tinyint(1) unsigned not null');
	}
}
