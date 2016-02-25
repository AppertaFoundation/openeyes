<?php

class m131002_120142_missing_procedure_elements extends CDbMigration
{
	public function up()
	{
		$event_type = $this->dbConnection->createCommand()->select("*")->from("event_type")->where("class_name = :class_name",array(":class_name"=>"OphTrOperationnote"))->queryRow();

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Argon laser trabeculoplasty','class_name'=>'ElementArgonLaserTrabeculoplasty','display_order'=>20,'default'=>0));

		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementArgonLaserTrabeculoplasty"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Argon laser trabeculoplasty"))->queryRow();
		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Argon laser trabeculoplasty' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_al_trabeculoplasty', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_altraby_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_altraby_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_altraby_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_altraby_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_altraby_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_altraby_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Focal laser photocoagulation','class_name'=>'ElementFocalLaserPhotocoagulation','display_order'=>20,'default'=>0));

		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementFocalLaserPhotocoagulation"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Focal laser photocoagulation"))->queryRow();
		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Focal laser photocoagulation' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_fl_photocoagulation', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_flphoto_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_flphoto_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_flphoto_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_flphoto_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_flphoto_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_flphoto_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Laser demarcation','class_name'=>'ElementLaserDemarcation','display_order'=>20,'default'=>0));

		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserDemarcation"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Laser demarcation"))->queryRow();
		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Laser demarcation' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_laser_demarcation', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_laserdemar_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_laserdemar_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_laserdemar_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_laserdemar_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_laserdemar_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_laserdemar_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Laser gonioplasty','class_name'=>'ElementLaserGonioplasty','display_order'=>20,'default'=>0));

		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserGonioplasty"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Laser gonioplasty"))->queryRow();
		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Laser gonioplasty' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_laser_gonio', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_lasergoni_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_lasergoni_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_lasergoni_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_lasergoni_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_lasergoni_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_lasergoni_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Laser hyaloidotomy','class_name'=>'ElementLaserHyaloidotomy','display_order'=>20,'default'=>0));

		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserHyaloidotomy"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Laser hyaloidotomy"))->queryRow();
		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Laser hyaloidotomy' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_laser_hyal', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',				 
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_laserhyal_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_laserhyal_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_laserhyal_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_laserhyal_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_laserhyal_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_laserhyal_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Laser iridoplasty','class_name'=>'ElementLaserIridoplasty','display_order'=>20,'default'=>0));

		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserIridoplasty"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Laser iridoplasty"))->queryRow();
		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Laser iridoplasty' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_laser_irid', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',				 'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_laseririd_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_laseririd_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_laseririd_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_laseririd_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_laseririd_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_laseririd_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Laser to chorioretinal lesion','class_name'=>'ElementLaserChorioretinal','display_order'=>20,'default'=>0));

		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserChorioretinal"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Laser to chorioretinal lesion"))->queryRow();
		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Laser to chorioretinal lesion' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_laser_chor', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',				 'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',				 'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_laserchor_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_laserchor_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_laserchor_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_laserchor_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_laserchor_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_laserchor_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Laser vitreolysis','class_name'=>'ElementLaserVitreolysis','display_order'=>20,'default'=>0));
	
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserVitreolysis"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Laser vitreolysis"))->queryRow();
		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Laser vitreolysis' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_laser_vitr', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',				 'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',				 'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_laservitr_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_laservitr_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_laservitr_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_laservitr_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_laservitr_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_laservitr_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Macular grid','class_name'=>'ElementMacularGrid','display_order'=>20,'default'=>0));
 
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementMacularGrid"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Macular grid"))->queryRow();
		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Macular grid' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_macular_grid', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',				 'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',				 'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_macugrid_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_macugrid_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_macugrid_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_macugrid_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_macugrid_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_macugrid_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->insert('element_type',array('event_type_id'=>$event_type['id'],'name'=>'Suture lysis','class_name'=>'ElementSutureLysis','display_order'=>20,'default'=>0));

		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementSutureLysis"))->queryRow();

		$proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => "Suture lysis"))->queryRow();
		if ($proc) {
			$this->insert('ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		} else {
			echo "**WARNING** 'Suture lysis' not present in proc table, not linking to element type\n";
		}

		$this->createTable('et_ophtroperationnote_suture_lys', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',				 'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',				 'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_suturely_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_suturely_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_suturely_ev_fk` (`event_id`)',
				'CONSTRAINT `et_ophtroperationnote_suturely_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_suturely_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_suturely_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
	}

	public function down()
	{
		$event_type = $this->dbConnection->createCommand()->select("*")->from("event_type")->where("class_name = :class_name",array(":class_name"=>"OphTrOperationnote"))->queryRow();

		$this->dropTable('et_ophtroperationnote_suture_lys');
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementSutureLysis"))->queryRow();
		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");

		$this->dropTable('et_ophtroperationnote_macular_grid');
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementMacularGrid"))->queryRow();
		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");

		$this->dropTable('et_ophtroperationnote_laser_vitr');
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserVitreolysis"))->queryRow();
		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");

		$this->dropTable('et_ophtroperationnote_laser_chor');
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserChorioretinal"))->queryRow();
		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");

		$this->dropTable('et_ophtroperationnote_laser_irid');
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserIridoplasty"))->queryRow();
		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");

		$this->dropTable('et_ophtroperationnote_laser_hyal');
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserHyaloidotomy"))->queryRow();
		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");

		$this->dropTable('et_ophtroperationnote_laser_gonio');
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserGonioplasty"))->queryRow();
		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");

		$this->dropTable('et_ophtroperationnote_laser_demarcation');
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementLaserDemarcation"))->queryRow();
		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");

		$this->dropTable('et_ophtroperationnote_fl_photocoagulation');
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementFocalLaserPhotocoagulation"))->queryRow();
		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");

		$this->dropTable('et_ophtroperationnote_al_trabeculoplasty');
		$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(":event_type_id"=>$event_type['id'],":class_name"=>"ElementArgonLaserTrabeculoplasty"))->queryRow();
		$this->delete('ophtroperationnote_procedure_element',"element_type_id = {$element_type['id']}");
		$this->delete('element_type',"id = {$element_type['id']}");
	}
}
