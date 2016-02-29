<?php

class m131002_114004_elements_for_new_procedures extends CDbMigration
{
	public function up()
	{
		$event_type = $this->dbConnection->createCommand()->select("*")->from("event_type")->where("class_name = :class_name",array(":class_name"=>"OphTrOperationnote"))->queryRow();

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Cycloablation','class_name'=>'ElementCycloablation','display_order'=>20,'default'=>0));

		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementCycloablation"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Cycloablation"))->queryRow();

		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Cycloablation' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_cycloablation', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_cyclob_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_cyclob_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_cyclob_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_cyclob_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_cyclob_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_cyclob_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
	}

	public function down()
	{
		$this->dropTable('et_ophtroperationnote_cycloablation');

		$event_type = $this->dbConnection->createCommand()->select("*")->from("event_type")->where("class_name = :class_name",array(":class_name"=>"OphTrOperationnote"))->queryRow();

		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementCycloablation"))->queryRow();

		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");
	}
}
