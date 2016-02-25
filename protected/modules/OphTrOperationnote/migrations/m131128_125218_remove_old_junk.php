<?php

class m131128_125218_remove_old_junk extends CDbMigration
{
	public function up()
	{
		$opnote = $this->dbConnection->createCommand()->select("*")->from("event_type")->where("class_name = :class_name",array(":class_name" => "OphTrOperationnote"))->queryRow();

		foreach (array(
			'et_ophtroperationnote_al_trabeculoplasty' => 'ElementArgonLaserTrabeculoplasty',
			'et_ophtroperationnote_cycloablation' => 'ElementCycloablation',
			'et_ophtroperationnote_fl_photocoagulation' => 'ElementFocalLaserPhotocoagulation',
			'et_ophtroperationnote_laser_chor' => 'ElementLaserChorioretinal',
			'et_ophtroperationnote_laser_demarcation' => 'ElementLaserDemarcation',
			'et_ophtroperationnote_laser_gonio' => 'ElementLaserGonioplasty',
			'et_ophtroperationnote_laser_hyal' => 'ElementLaserHyaloidotomy',
			'et_ophtroperationnote_laser_irid' => 'ElementLaserIridoplasty',
			'et_ophtroperationnote_laser_vitr' => 'ElementLaserVitreolysis',
			'et_ophtroperationnote_macular_grid' => 'ElementMacularGrid',
			'et_ophtroperationnote_suture_lys' => 'ElementSutureLysis') as $table => $element) {

			$element_type = $this->dbConnection->createCommand()->select('id')->from("element_type")->where("event_type_id=:event_type_id and class_name=:class_name",array(':event_type_id'=>$opnote['id'],':class_name'=>$element))->queryRow();
			$pe = $this->dbConnection->createCommand()->select("*")->from("ophtroperationnote_procedure_element")->where("element_type_id=:element_type_id",array(':element_type_id'=>$element_type['id']))->queryRow();

			foreach ($this->dbConnection->createCommand()->select("*")->from($table)->order('id asc')->queryAll() as $row) {
				$this->insert('et_ophtroperationnote_genericprocedure',array(
						'event_id' => $row['event_id'],
						'proc_id' => $pe['procedure_id'],
						'comments' => $row['comments'],
						'created_user_id' => $row['created_user_id'],
						'created_date' => $row['created_date'],
						'last_modified_user_id' => $row['last_modified_user_id'],
						'last_modified_date' => $row['last_modified_date'],
					));
			}

			if ($pe['id']) {
				$this->delete('ophtroperationnote_procedure_element',"id={$pe['id']}");
			}
			$this->delete('element_type',"id={$element_type['id']}");
			$this->dropTable($table);
		}
	}

	public function down()
	{
		$this->execute("
CREATE TABLE `et_ophtroperationnote_al_trabeculoplasty` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_altraby_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_altraby_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_altraby_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_altraby_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_altraby_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_altraby_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("
CREATE TABLE `et_ophtroperationnote_cycloablation` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_cyclob_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_cyclob_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_cyclob_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_cyclob_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_cyclob_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_cyclob_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("
CREATE TABLE `et_ophtroperationnote_fl_photocoagulation` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_flphoto_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_flphoto_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_flphoto_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_flphoto_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_flphoto_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_flphoto_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("
CREATE TABLE `et_ophtroperationnote_laser_chor` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_laserchor_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_laserchor_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_laserchor_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_laserchor_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_laserchor_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_laserchor_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("
CREATE TABLE `et_ophtroperationnote_laser_demarcation` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_laserdemar_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_laserdemar_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_laserdemar_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_laserdemar_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_laserdemar_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_laserdemar_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("
CREATE TABLE `et_ophtroperationnote_laser_gonio` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_lasergoni_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_lasergoni_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_lasergoni_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_lasergoni_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_lasergoni_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_lasergoni_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("
CREATE TABLE `et_ophtroperationnote_laser_hyal` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_laserhyal_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_laserhyal_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_laserhyal_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_laserhyal_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_laserhyal_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_laserhyal_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("
CREATE TABLE `et_ophtroperationnote_laser_irid` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_laseririd_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_laseririd_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_laseririd_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_laseririd_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_laseririd_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_laseririd_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("
CREATE TABLE `et_ophtroperationnote_laser_vitr` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_laservitr_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_laservitr_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_laservitr_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_laservitr_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_laservitr_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_laservitr_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("
CREATE TABLE `et_ophtroperationnote_macular_grid` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_macugrid_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_macugrid_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_macugrid_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_macugrid_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_macugrid_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_macugrid_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("
CREATE TABLE `et_ophtroperationnote_suture_lys` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` varchar(4096) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `et_ophtroperationnote_suturely_lmui_fk` (`last_modified_user_id`),
	KEY `et_ophtroperationnote_suturely_cui_fk` (`created_user_id`),
	KEY `et_ophtroperationnote_suturely_ev_fk` (`event_id`),
	CONSTRAINT `et_ophtroperationnote_suturely_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_suturely_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophtroperationnote_suturely_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$event_type = $this->dbConnection->createCommand()->select("*")->from("event_type")->where("class_name = :class_name",array(":class_name" => "OphTrOperationnote"))->queryRow();

		foreach (array(
			'et_ophtroperationnote_al_trabeculoplasty' => array(
				'class' => 'ElementArgonLaserTrabeculoplasty',
				'proc' => 'Argon laser trabeculoplasty',
			),
			'et_ophtroperationnote_cycloablation' => array(
				'class' => 'ElementCycloablation',
				'proc' => 'Cycloablation',
			),
			'et_ophtroperationnote_fl_photocoagulation' => array(
				'class' => 'ElementFocalLaserPhotocoagulation',
				'proc' => 'Focal laser photocoagulation',
			),
			'et_ophtroperationnote_laser_chor' => array(
				'class' => 'ElementLaserChorioretinal',
				'proc' => 'Laser to chorioretinal lesion',
			),
			'et_ophtroperationnote_laser_demarcation' => array(
				'class' => 'ElementLaserDemarcation',
				'proc' => 'Laser demarcation',
			),
			'et_ophtroperationnote_laser_gonio' => array(
				'class' => 'ElementLaserGonioplasty',
				'proc' => 'Laser gonioplasty',
			),
			'et_ophtroperationnote_laser_hyal' => array(
				'class' => 'ElementLaserHyaloidotomy',
				'proc' => 'Laser hyaloidotomy',
			),
			'et_ophtroperationnote_laser_irid' => array(
				'class' => 'ElementLaserIridoplasty',
				'proc' => 'Laser iridoplasty',
			),
			'et_ophtroperationnote_laser_vitr' => array(
				'class' => 'ElementLaserVitreolysis',
				'proc' => 'Laser vitreolysis',
			),
			'et_ophtroperationnote_macular_grid' => array(
				'class' => 'ElementMacularGrid',
				'proc' => 'Macular grid',
			),
			'et_ophtroperationnote_suture_lys' => array(
				'class' => 'ElementSutureLysis',
				'proc' => 'Suture lysis',
			)) as $table => $data) {

			if ($proc = $this->dbConnection->createCommand()->select("*")->from("proc")->where("term = :term",array(":term" => $data['proc']))->queryRow()) {
				$this->insert('element_type',array(
					'name' => $data['proc'],
					'class_name' => $data['class'],
					'event_type_id' => $event_type['id'],
					'display_order' => 20,
					'default' => 0,
				));

				$element_type = $this->dbConnection->createCommand()->select("*")->from("element_type")->where("event_type_id = :event_type_id and class_name = :class_name",array(':event_type_id' => $event_type['id'],':class_name' => $data['class']))->queryRow();

				$this->insert('ophtroperationnote_procedure_element',array(
					'procedure_id' => $proc['id'],
					'element_type_id' => $element_type['id'],
				));
					
				foreach ($this->dbConnection->createCommand()->select("*")->from("et_ophtroperationnote_genericprocedure")->where("proc_id = :proc_id",array(":proc_id" => $proc['id']))->order("id asc")->queryAll() as $row) {
					$id = $row['id'];

					unset($row['id']);
					unset($row['proc_id']);

					$this->insert($table,$row);

					$this->delete("et_ophtroperationnote_genericprocedure","id = $id");
				}
			}
		}
	}
}
