<?php

class m130503_135207_new_specialty_table extends CDbMigration
{
	public function up()
	{
		$this->delete('specialty',"code != 'OPH' and code != 'SUP'");
		$this->update('specialty',array('code'=>'130'),"code='OPH'");
		$this->update('specialty',array('code'=>'990'),"code='SUP'");
		$this->alterColumn('specialty','code','int(10) unsigned NOT NULL');

		$this->createTable('specialty_type',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `specialty_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `specialty_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `specialty_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `specialty_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('specialty_type',array('id'=>1,'name'=>'Surgical','display_order'=>1));
		$this->insert('specialty_type',array('id'=>2,'name'=>'Medical','display_order'=>1));
		$this->insert('specialty_type',array('id'=>3,'name'=>'Psychiatry','display_order'=>1));
		$this->insert('specialty_type',array('id'=>4,'name'=>'Radiology','display_order'=>1));
		$this->insert('specialty_type',array('id'=>5,'name'=>'Pathology','display_order'=>1));
		$this->insert('specialty_type',array('id'=>6,'name'=>'Other','display_order'=>1));

		$this->dropColumn('specialty','medical');
		$this->addColumn('specialty','specialty_type_id','int(10) unsigned NOT NULL');
		$this->addColumn('specialty','default_title','varchar(64) COLLATE utf8_bin NOT NULL');
		$this->addColumn('specialty','default_is_surgeon','tinyint(1) unsigned NOT NULL DEFAULT 0');

		$this->update('specialty',array('specialty_type_id'=>1,'default_title'=>'Consultant Ophthalmologist','default_is_surgeon'=>1),'code=130');
		$this->update('specialty',array('specialty_type_id'=>6),'code=990');

		$this->createIndex('specialty_specialty_type_id_fk','specialty','specialty_type_id');
		$this->addForeignKey('specialty_specialty_type_id_fk','specialty','specialty_type_id','specialty_type','id');

		$this->insert('specialty',array('name'=>'General Surgery','code'=>100,'specialty_type_id'=>1,'default_title'=>'Consultant Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Urology','code'=>101,'specialty_type_id'=>1,'default_title'=>'ConsultantUrologist','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Trauma & Orthopaedics','code'=>110,'specialty_type_id'=>1,'default_title'=>'Consultant Orthopaedic Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'ENT','code'=>120,'specialty_type_id'=>1,'default_title'=>'Consultant ENT Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Oral Surgery','code'=>140,'specialty_type_id'=>1,'default_title'=>'Consultant Oral Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Restorative Dentistry','code'=>141,'specialty_type_id'=>1,'default_title'=>'Consultant Dental Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Paediatric Dentistry','code'=>142,'specialty_type_id'=>1,'default_title'=>'Consultant Dental Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Orthodontics','code'=>143,'specialty_type_id'=>1,'default_title'=>'Consultant Orthodontist','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Oral & Maxillo Facial Surgery','code'=>145,'specialty_type_id'=>1,'default_title'=>'Consultant Maxillo Facial Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Endodontics','code'=>146,'specialty_type_id'=>1,'default_title'=>'Consultant Endontic Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Periodontics','code'=>147,'specialty_type_id'=>1,'default_title'=>'Consultant Peridontic Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Prosthodontics','code'=>148,'specialty_type_id'=>1,'default_title'=>'Consultant Prosthodontist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Surgical Dentistry','code'=>149,'specialty_type_id'=>1,'default_title'=>'Consultant Dental Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Neurosurgery','code'=>150,'specialty_type_id'=>1,'default_title'=>'Consultant Neurosurgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Plastic Surgery','code'=>160,'specialty_type_id'=>1,'default_title'=>'Consultant Plastic Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Cardiothoracic Surgery','code'=>170,'specialty_type_id'=>1,'default_title'=>'Consultant Cardiothoracic Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Paediatric Surgery','code'=>171,'specialty_type_id'=>1,'default_title'=>'Consultant Paediatric Surgeon','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Accident & Emergency','code'=>180,'specialty_type_id'=>1,'default_title'=>'Accident & Emergency Consultant','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Anaesthetics','code'=>190,'specialty_type_id'=>1,'default_title'=>'Consultant Anaesthetist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Critical Care Medicine','code'=>192,'specialty_type_id'=>2,'default_title'=>'Consultant in Critical Care','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'General Medicine','code'=>300,'specialty_type_id'=>2,'default_title'=>'Consultant Physician','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Gastroenterology','code'=>301,'specialty_type_id'=>2,'default_title'=>'Consultant Gastroenterologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Endocrinology','code'=>302,'specialty_type_id'=>2,'default_title'=>'Consultant Endocrinologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Clinical Haematology','code'=>303,'specialty_type_id'=>2,'default_title'=>'Consultant Haematologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Clinical Physiology','code'=>304,'specialty_type_id'=>2,'default_title'=>'Consultant Physiologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Clinical Pharmacology','code'=>305,'specialty_type_id'=>2,'default_title'=>'Consultant Pharmacologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Audiological Medicine','code'=>310,'specialty_type_id'=>2,'default_title'=>'Consultant Audiologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Clinical Genetics','code'=>311,'specialty_type_id'=>2,'default_title'=>'Consultant Clinical Geneticist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Clinical Cytogenics and Molecular Genetics','code'=>312,'specialty_type_id'=>2,'default_title'=>'Consultant Clinical Geneticist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Clinical Immunology and Allergy','code'=>313,'specialty_type_id'=>2,'default_title'=>'Consultant Immunologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Rehabilitation','code'=>314,'specialty_type_id'=>2,'default_title'=>'Consultant in Rehabilitation','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Palliative Medicine','code'=>315,'specialty_type_id'=>2,'default_title'=>'Consultant in Palliative Care','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Cardiology','code'=>320,'specialty_type_id'=>2,'default_title'=>'Consultant Cardiologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Paediatric Cardiology','code'=>321,'specialty_type_id'=>2,'default_title'=>'Consultant Paediatric Cardiologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Dermatology','code'=>330,'specialty_type_id'=>2,'default_title'=>'Consultant Dermatologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Respiratory Medicine','code'=>340,'specialty_type_id'=>2,'default_title'=>'Consultant Physician','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Infectious Diseases','code'=>350,'specialty_type_id'=>2,'default_title'=>'Consultant in Infectious Diseases','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Tropical Medicine','code'=>352,'specialty_type_id'=>2,'default_title'=>'Consultant in Tropical Medicine','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Genitourinary Medicine','code'=>360,'specialty_type_id'=>2,'default_title'=>'Consultant in Genitourinary Medicine','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Nephrology','code'=>361,'specialty_type_id'=>2,'default_title'=>'Consultant Nephrologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Medical Oncology','code'=>370,'specialty_type_id'=>2,'default_title'=>'Consultant Oncologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Nuclear Medicine','code'=>371,'specialty_type_id'=>2,'default_title'=>'Consultant in Nuclear Medicine','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Neurology','code'=>400,'specialty_type_id'=>2,'default_title'=>'Consultant Neurologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Clinical Neuro-physiology','code'=>401,'specialty_type_id'=>2,'default_title'=>'Consultant Neuro-Physiologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Rheumatology','code'=>410,'specialty_type_id'=>2,'default_title'=>'Consultant Rheumatologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Paediatrics','code'=>420,'specialty_type_id'=>2,'default_title'=>'Consultant Paediatrician','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Paediatric Neurology','code'=>421,'specialty_type_id'=>2,'default_title'=>'Consultant Paediatric Neurologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Geriatric Medicine','code'=>430,'specialty_type_id'=>2,'default_title'=>'Consultant Geriatrician','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Dental Medicine Specialties','code'=>450,'specialty_type_id'=>2,'default_title'=>'Consultant Physician','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Medical Ophthalmology','code'=>460,'specialty_type_id'=>2,'default_title'=>'Consultant Ophthalmologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Obstetrics and Gynaecology','code'=>500,'specialty_type_id'=>2,'default_title'=>'Consultant Gynaecologist','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Obstetrics','code'=>501,'specialty_type_id'=>2,'default_title'=>'Consultant Obstetrician','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Gynaecology','code'=>502,'specialty_type_id'=>2,'default_title'=>'Consultant Gynaecologist','default_is_surgeon'=>1));
		$this->insert('specialty',array('name'=>'Midwife Episode','code'=>560,'specialty_type_id'=>2,'default_title'=>'Consultant','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Learning Disability','code'=>700,'specialty_type_id'=>3,'default_title'=>'Consultant in Learning Disability','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Adult Mental Illness','code'=>710,'specialty_type_id'=>3,'default_title'=>'Consultant Psychiatrist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Child and Adolescent Psychiatry','code'=>711,'specialty_type_id'=>3,'default_title'=>'Consultant Psychiatrist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Forensic Psychiatry','code'=>712,'specialty_type_id'=>3,'default_title'=>'Consultant Psychiatrist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Psychotherapy','code'=>713,'specialty_type_id'=>3,'default_title'=>'Consultant Psychotherapist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Old Age Psychiatry','code'=>715,'specialty_type_id'=>3,'default_title'=>'Consultant Psychiatrist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Clinical Oncology','code'=>800,'specialty_type_id'=>4,'default_title'=>'Consultant Oncologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Radiology','code'=>810,'specialty_type_id'=>4,'default_title'=>'Consultant Radiologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'General Pathology','code'=>820,'specialty_type_id'=>5,'default_title'=>'Consultant Pathologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Blood Transfusion','code'=>821,'specialty_type_id'=>5,'default_title'=>'Consultant Haematologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Chemical Pathology','code'=>822,'specialty_type_id'=>5,'default_title'=>'Consultant Pathologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Haematology','code'=>823,'specialty_type_id'=>5,'default_title'=>'Consultant Haematologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Histopathology','code'=>824,'specialty_type_id'=>5,'default_title'=>'Consultant Pathologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Immunopathology','code'=>830,'specialty_type_id'=>5,'default_title'=>'Consultant Pathologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Medical Microbiology and Virology','code'=>831,'specialty_type_id'=>5,'default_title'=>'Consultant Microbiologist','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Community Medicine','code'=>900,'specialty_type_id'=>6,'default_title'=>'Consultant in Community Medicine','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Occupational Medicine','code'=>901,'specialty_type_id'=>6,'default_title'=>'Consultant in Occupational Medicine','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Community Health Services Dental','code'=>902,'specialty_type_id'=>6,'default_title'=>'Consultant in Community Health','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Public Health Medicine','code'=>903,'specialty_type_id'=>6,'default_title'=>'Public Health Consultant','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Public Health Dental','code'=>904,'specialty_type_id'=>6,'default_title'=>'Public Health Dental Consultant','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Nursing Episode','code'=>950,'specialty_type_id'=>6,'default_title'=>'Consultant','default_is_surgeon'=>0));
		$this->insert('specialty',array('name'=>'Allied Health Professional Episode','code'=>960,'specialty_type_id'=>6,'default_title'=>'Consultant','default_is_surgeon'=>0));
	}

	public function down()
	{
		$this->dropForeignKey('specialty_specialty_type_id_fk','specialty');
		$this->dropIndex('specialty_specialty_type_id_fk','specialty');
		$this->dropColumn('specialty','specialty_type_id');
		$this->addColumn('specialty','medical','tinyint(1) NOT NULL DEFAULT 1');
		$this->dropColumn('specialty','default_title');
		$this->dropColumn('specialty','default_is_surgeon');

		$this->dropTable('specialty_type');

		$this->delete('specialty','code != 130 and code != 990');

		$this->alterColumn('specialty','code',"varchar(3) COLLATE utf8_bin NOT NULL DEFAULT ''");

		$this->update('specialty',array('code'=>'OPH'),"code=130");
		$this->update('specialty',array('code'=>'SUP'),"code=990");

		$this->insert('specialty',array('id'=>1,'name'=>'Abdominal Radiology','code'=>'AR','medical'=>1));
		$this->insert('specialty',array('id'=>2,'name'=>'Abdominal Surgery','code'=>'AS','medical'=>1));
		$this->insert('specialty',array('id'=>3,'name'=>'Addiction Medicine','code'=>'ADM','medical'=>1));
		$this->insert('specialty',array('id'=>4,'name'=>'Addiction Psychiatry','code'=>'ADP','medical'=>1));
		$this->insert('specialty',array('id'=>5,'name'=>'Adolescent Medicine (Family Medicine)','code'=>'AMF','medical'=>1));
		$this->insert('specialty',array('id'=>6,'name'=>'Adolescent Medicine (Internal Medicine)','code'=>'AMI','medical'=>1));
		$this->insert('specialty',array('id'=>7,'name'=>'Adolescent Medicine (Pediatrics)','code'=>'ADL','medical'=>1));
		$this->insert('specialty',array('id'=>8,'name'=>'Adult Reconstructive Orthopedics','code'=>'OAR','medical'=>1));
		$this->insert('specialty',array('id'=>9,'name'=>'Aerospace Medicine','code'=>'AM','medical'=>1));
		$this->insert('specialty',array('id'=>10,'name'=>'Allergy','code'=>'A','medical'=>1));
		$this->insert('specialty',array('id'=>11,'name'=>'Allergy & Immunology','code'=>'AI','medical'=>1));
		$this->insert('specialty',array('id'=>12,'name'=>'Clinical Laboratory Immunology (Allergy & Immunology)','code'=>'ALI','medical'=>1));
		$this->insert('specialty',array('id'=>13,'name'=>'Anatomic/Clinical Pathology','code'=>'PTH','medical'=>1));
		$this->insert('specialty',array('id'=>14,'name'=>'Anatomic Pathology','code'=>'ATP','medical'=>1));
		$this->insert('specialty',array('id'=>15,'name'=>'Anesthesiology','code'=>'AN','medical'=>1));
		$this->insert('specialty',array('id'=>16,'name'=>'Blood Banking/Transfusion Medicine','code'=>'BBK','medical'=>1));
		$this->insert('specialty',array('id'=>17,'name'=>'Clinical Cardiac Electrophysiology','code'=>'ICE','medical'=>1));
		$this->insert('specialty',array('id'=>18,'name'=>'Cardiothoracic Radiology','code'=>'CTR','medical'=>1));
		$this->insert('specialty',array('id'=>19,'name'=>'Cardiothoracic Surgery','code'=>'CTS','medical'=>1));
		$this->insert('specialty',array('id'=>20,'name'=>'Cardiovascular Disease','code'=>'CD','medical'=>1));
		$this->insert('specialty',array('id'=>21,'name'=>'Chemical Pathology','code'=>'PCH','medical'=>1));
		$this->insert('specialty',array('id'=>22,'name'=>'Child and Adolescent Psychiatry','code'=>'CHP','medical'=>1));
		$this->insert('specialty',array('id'=>23,'name'=>'Child Neurology','code'=>'CHN','medical'=>1));
		$this->insert('specialty',array('id'=>24,'name'=>'Clinical Biochemical Genetics','code'=>'CBG','medical'=>1));
		$this->insert('specialty',array('id'=>25,'name'=>'Clinical Cytogenetics','code'=>'CCG','medical'=>1));
		$this->insert('specialty',array('id'=>26,'name'=>'Clinical Genetics','code'=>'CG','medical'=>1));
		$this->insert('specialty',array('id'=>27,'name'=>'Clinical and Laboratory Dermatological Immunology','code'=>'DDL','medical'=>1));
		$this->insert('specialty',array('id'=>28,'name'=>'Clinical and Laboratory Immunology (Internal Medicine)','code'=>'ILI','medical'=>1));
		$this->insert('specialty',array('id'=>29,'name'=>'Clinical and Laboratory Immunology (Pediatrics)','code'=>'PLI','medical'=>1));
		$this->insert('specialty',array('id'=>30,'name'=>'Clinical Molecular Genetics','code'=>'CMG','medical'=>1));
		$this->insert('specialty',array('id'=>31,'name'=>'Clinical Neurophysiology','code'=>'CN','medical'=>1));
		$this->insert('specialty',array('id'=>32,'name'=>'Clinical Pathology','code'=>'CLP','medical'=>1));
		$this->insert('specialty',array('id'=>33,'name'=>'Clinical Pharmacology','code'=>'PA','medical'=>1));
		$this->insert('specialty',array('id'=>34,'name'=>'Colon & Rectal Surgery','code'=>'CRS','medical'=>1));
		$this->insert('specialty',array('id'=>35,'name'=>'Cosmetic Surgery','code'=>'CS','medical'=>1));
		$this->insert('specialty',array('id'=>36,'name'=>'Craniofacial Surgery','code'=>'CFS','medical'=>1));
		$this->insert('specialty',array('id'=>37,'name'=>'Critical Care Medicine (Anesthesiology)','code'=>'CCA','medical'=>1));
		$this->insert('specialty',array('id'=>38,'name'=>'Critical Care Medicine (Internal Medicine)','code'=>'CCM','medical'=>1));
		$this->insert('specialty',array('id'=>39,'name'=>'Critical Care Medicine (Neurological Surgery)','code'=>'NCC','medical'=>1));
		$this->insert('specialty',array('id'=>40,'name'=>'Critical Care Medicine (Obstetrics & Gynecology)','code'=>'OCC','medical'=>1));
		$this->insert('specialty',array('id'=>41,'name'=>'Cytopathology','code'=>'PCP','medical'=>1));
		$this->insert('specialty',array('id'=>42,'name'=>'Dermatology','code'=>'D','medical'=>1));
		$this->insert('specialty',array('id'=>43,'name'=>'Dermatopathology','code'=>'DMP','medical'=>1));
		$this->insert('specialty',array('id'=>44,'name'=>'Dermatologic Surgery','code'=>'DS','medical'=>1));
		$this->insert('specialty',array('id'=>45,'name'=>'Developmental-Behavioral  Pediatrics','code'=>'DBP','medical'=>1));
		$this->insert('specialty',array('id'=>46,'name'=>'Diabetes','code'=>'DIA','medical'=>1));
		$this->insert('specialty',array('id'=>47,'name'=>'Diagnostic Radiology','code'=>'DR','medical'=>1));
		$this->insert('specialty',array('id'=>48,'name'=>'Emergency Medicine','code'=>'EM','medical'=>1));
		$this->insert('specialty',array('id'=>49,'name'=>'Endocrinology, Diabetes and Metabolism','code'=>'END','medical'=>1));
		$this->insert('specialty',array('id'=>50,'name'=>'Endovascular Surgical Neuroradiology','code'=>'ESN','medical'=>1));
		$this->insert('specialty',array('id'=>51,'name'=>'Epidemiology','code'=>'EP','medical'=>1));
		$this->insert('specialty',array('id'=>52,'name'=>'Facial Plastic Surgery','code'=>'FPS','medical'=>1));
		$this->insert('specialty',array('id'=>53,'name'=>'Family Practice','code'=>'FP','medical'=>1));
		$this->insert('specialty',array('id'=>54,'name'=>'Forensic Pathology','code'=>'FOP','medical'=>1));
		$this->insert('specialty',array('id'=>55,'name'=>'Forensic Psychiatry','code'=>'PFP','medical'=>1));
		$this->insert('specialty',array('id'=>56,'name'=>'Gastroenterology','code'=>'GE','medical'=>1));
		$this->insert('specialty',array('id'=>57,'name'=>'General Practice','code'=>'GP','medical'=>1));
		$this->insert('specialty',array('id'=>58,'name'=>'General Preventive Medicine','code'=>'GPM','medical'=>1));
		$this->insert('specialty',array('id'=>59,'name'=>'General Surgery','code'=>'GS','medical'=>1));
		$this->insert('specialty',array('id'=>60,'name'=>'Geriatric Medicine (Family Practice)','code'=>'FPG','medical'=>1));
		$this->insert('specialty',array('id'=>61,'name'=>'Geriatric Medicine (Internal Medicine)','code'=>'IMG','medical'=>1));
		$this->insert('specialty',array('id'=>62,'name'=>'Geriatric Psychiatry','code'=>'PYG','medical'=>1));
		$this->insert('specialty',array('id'=>63,'name'=>'Gynecology','code'=>'GYN','medical'=>1));
		$this->insert('specialty',array('id'=>64,'name'=>'Gynecological Oncology','code'=>'GO','medical'=>1));
		$this->insert('specialty',array('id'=>65,'name'=>'Hand Surgery','code'=>'HS','medical'=>1));
		$this->insert('specialty',array('id'=>66,'name'=>'Head & Neck Surgery','code'=>'HNS','medical'=>1));
		$this->insert('specialty',array('id'=>67,'name'=>'Hematology (Internal Medicine)','code'=>'HEM','medical'=>1));
		$this->insert('specialty',array('id'=>68,'name'=>'Hematology (Pathology)','code'=>'HMP','medical'=>1));
		$this->insert('specialty',array('id'=>69,'name'=>'Hematology/Oncology','code'=>'HO','medical'=>1));
		$this->insert('specialty',array('id'=>70,'name'=>'Hepatology','code'=>'HEP','medical'=>1));
		$this->insert('specialty',array('id'=>71,'name'=>'Hospitalist','code'=>'HOS','medical'=>1));
		$this->insert('specialty',array('id'=>72,'name'=>'Immunology','code'=>'IG','medical'=>1));
		$this->insert('specialty',array('id'=>73,'name'=>'Immunopathology','code'=>'PIP','medical'=>1));
		$this->insert('specialty',array('id'=>74,'name'=>'Infectious Disease','code'=>'ID','medical'=>1));
		$this->insert('specialty',array('id'=>75,'name'=>'Internal Medicine','code'=>'IM','medical'=>1));
		$this->insert('specialty',array('id'=>76,'name'=>'Internal Medicine/Pediatrics','code'=>'MPD','medical'=>1));
		$this->insert('specialty',array('id'=>77,'name'=>'Interventional Cardiology','code'=>'IC','medical'=>1));
		$this->insert('specialty',array('id'=>78,'name'=>'Legal Medicine','code'=>'LM','medical'=>1));
		$this->insert('specialty',array('id'=>79,'name'=>'Maternal & Fetal Medicine','code'=>'MFM','medical'=>1));
		$this->insert('specialty',array('id'=>80,'name'=>'Maxillofacial Radiology','code'=>'MXR','medical'=>1));
		$this->insert('specialty',array('id'=>81,'name'=>'Medical Genetics','code'=>'MG','medical'=>1));
		$this->insert('specialty',array('id'=>82,'name'=>'Medical Management','code'=>'MDM','medical'=>1));
		$this->insert('specialty',array('id'=>83,'name'=>'Medical Microbiology','code'=>'MM','medical'=>1));
		$this->insert('specialty',array('id'=>84,'name'=>'Medical Oncology','code'=>'ON','medical'=>1));
		$this->insert('specialty',array('id'=>85,'name'=>'Medical Toxicology (Emergency Medicine)','code'=>'ETX','medical'=>1));
		$this->insert('specialty',array('id'=>86,'name'=>'Medical Toxicology (Pediatrics)','code'=>'PDT','medical'=>1));
		$this->insert('specialty',array('id'=>87,'name'=>'Medical Toxicology (Preventive Medicine)','code'=>'PTX','medical'=>1));
		$this->insert('specialty',array('id'=>88,'name'=>'Molecular Genetic Pathology (Medical Genetics)','code'=>'MGG','medical'=>1));
		$this->insert('specialty',array('id'=>89,'name'=>'Molecular Genetic Pathology (Pathology)','code'=>'MGP','medical'=>1));
		$this->insert('specialty',array('id'=>90,'name'=>'Musculoskeletal Oncology','code'=>'OMO','medical'=>1));
		$this->insert('specialty',array('id'=>91,'name'=>'Musculoskeletal Radiology','code'=>'MSR','medical'=>1));
		$this->insert('specialty',array('id'=>92,'name'=>'Neonatal-Perinatal Medicine','code'=>'NPM','medical'=>1));
		$this->insert('specialty',array('id'=>93,'name'=>'Nephrology','code'=>'NEP','medical'=>1));
		$this->insert('specialty',array('id'=>94,'name'=>'Neurodevelopmental Disabilities (Pediatrics)','code'=>'NDP','medical'=>1));
		$this->insert('specialty',array('id'=>95,'name'=>'Neurodevelopmental Disabilities (Psychiatry & Neurology)','code'=>'NDN','medical'=>1));
		$this->insert('specialty',array('id'=>96,'name'=>'Neurology','code'=>'N','medical'=>1));
		$this->insert('specialty',array('id'=>97,'name'=>'Neurology/Diagnostic Radiology/Neuroradiology','code'=>'NRN','medical'=>1));
		$this->insert('specialty',array('id'=>98,'name'=>'Neurological Surgery','code'=>'NS','medical'=>1));
		$this->insert('specialty',array('id'=>99,'name'=>'Neuropathology','code'=>'NP','medical'=>1));
		$this->insert('specialty',array('id'=>100,'name'=>'Neuropsychiatry','code'=>'NUP','medical'=>1));
		$this->insert('specialty',array('id'=>101,'name'=>'Neuroradiology','code'=>'RNR','medical'=>1));
		$this->insert('specialty',array('id'=>102,'name'=>'Nuclear Cardiology','code'=>'NC','medical'=>1));
		$this->insert('specialty',array('id'=>103,'name'=>'Nuclear Medicine','code'=>'NM','medical'=>1));
		$this->insert('specialty',array('id'=>104,'name'=>'Nuclear Radiology','code'=>'NR','medical'=>1));
		$this->insert('specialty',array('id'=>105,'name'=>'Nutrition','code'=>'NTR','medical'=>1));
		$this->insert('specialty',array('id'=>106,'name'=>'Obstetrics','code'=>'OBS','medical'=>1));
		$this->insert('specialty',array('id'=>107,'name'=>'Obstetrics & Gynecology','code'=>'OBG','medical'=>1));
		$this->insert('specialty',array('id'=>108,'name'=>'Occupational Medicine','code'=>'OM','medical'=>1));
		$this->insert('specialty',array('id'=>110,'name'=>'Oral & Maxillofacial Surgery','code'=>'OMF','medical'=>1));
		$this->insert('specialty',array('id'=>111,'name'=>'Orthopedic Surgery','code'=>'ORS','medical'=>1));
		$this->insert('specialty',array('id'=>112,'name'=>'Orthopedic Surgery of the Spine','code'=>'OSS','medical'=>1));
		$this->insert('specialty',array('id'=>113,'name'=>'Orthopedic Trauma','code'=>'OTR','medical'=>1));
		$this->insert('specialty',array('id'=>114,'name'=>'Orthopedics, Foot and Ankle','code'=>'OFA','medical'=>1));
		$this->insert('specialty',array('id'=>115,'name'=>'Osteopathic Manipulative Medicine','code'=>'OMM','medical'=>1));
		$this->insert('specialty',array('id'=>116,'name'=>'Other','code'=>'OS','medical'=>1));
		$this->insert('specialty',array('id'=>117,'name'=>'Otolaryngology','code'=>'OTO','medical'=>1));
		$this->insert('specialty',array('id'=>118,'name'=>'Otology/Neurotology','code'=>'OT','medical'=>1));
		$this->insert('specialty',array('id'=>119,'name'=>'Pain Management','code'=>'APM','medical'=>1));
		$this->insert('specialty',array('id'=>120,'name'=>'Pain Medicine','code'=>'PMD','medical'=>1));
		$this->insert('specialty',array('id'=>121,'name'=>'Pain Medicine (Psychiatry)','code'=>'PPN','medical'=>1));
		$this->insert('specialty',array('id'=>122,'name'=>'Palliative Medicine','code'=>'PLM','medical'=>1));
		$this->insert('specialty',array('id'=>123,'name'=>'Pediatric Allergy','code'=>'PDA','medical'=>1));
		$this->insert('specialty',array('id'=>124,'name'=>'Pediatric Anesthesiology (Pediatrics)','code'=>'PAN','medical'=>1));
		$this->insert('specialty',array('id'=>125,'name'=>'Pediatric Cardiology','code'=>'PDC','medical'=>1));
		$this->insert('specialty',array('id'=>126,'name'=>'Pediatric Cardiothoric Surgery','code'=>'PCS','medical'=>1));
		$this->insert('specialty',array('id'=>127,'name'=>'Pediatric Critical Care Medicine','code'=>'CCP','medical'=>1));
		$this->insert('specialty',array('id'=>128,'name'=>'Pediatric Dermatology','code'=>'PDD','medical'=>1));
		$this->insert('specialty',array('id'=>129,'name'=>'Pediatric Emergency Medicine (Emergency Medicine)','code'=>'PE','medical'=>1));
		$this->insert('specialty',array('id'=>130,'name'=>'Pediatric Emergency Medicine (Pediatrics)','code'=>'PEM','medical'=>1));
		$this->insert('specialty',array('id'=>131,'name'=>'Pediatric Endocrinology','code'=>'PDE','medical'=>1));
		$this->insert('specialty',array('id'=>132,'name'=>'Pediatric Gastroenterology','code'=>'PG','medical'=>1));
		$this->insert('specialty',array('id'=>133,'name'=>'Pediatric Hematology/Oncology','code'=>'PHO','medical'=>1));
		$this->insert('specialty',array('id'=>134,'name'=>'Pediatric Infectious Disease','code'=>'PDI','medical'=>1));
		$this->insert('specialty',array('id'=>135,'name'=>'Pediatric Nephrology','code'=>'PN','medical'=>1));
		$this->insert('specialty',array('id'=>136,'name'=>'Pediatric Ophthalmology','code'=>'PO','medical'=>1));
		$this->insert('specialty',array('id'=>137,'name'=>'Pediatric Orthopedics','code'=>'OP','medical'=>1));
		$this->insert('specialty',array('id'=>138,'name'=>'Pediatric Otolaryngology','code'=>'PDO','medical'=>1));
		$this->insert('specialty',array('id'=>139,'name'=>'Pediatric Pathology','code'=>'PP','medical'=>1));
		$this->insert('specialty',array('id'=>140,'name'=>'Pediatric Pulmonology','code'=>'PDP','medical'=>1));
		$this->insert('specialty',array('id'=>141,'name'=>'Pediatric Radiology','code'=>'PDR','medical'=>1));
		$this->insert('specialty',array('id'=>142,'name'=>'Pediatric Rehabilitation Medicine','code'=>'RPM','medical'=>1));
		$this->insert('specialty',array('id'=>143,'name'=>'Pediatric Rheumatology','code'=>'PPR','medical'=>1));
		$this->insert('specialty',array('id'=>144,'name'=>'Pediatric Surgery (Neurology)','code'=>'NSP','medical'=>1));
		$this->insert('specialty',array('id'=>145,'name'=>'Pediatric Surgery (Surgery)','code'=>'PDS','medical'=>1));
		$this->insert('specialty',array('id'=>146,'name'=>'Pediatric Urology','code'=>'UP','medical'=>1));
		$this->insert('specialty',array('id'=>147,'name'=>'Pediatrics','code'=>'PD','medical'=>1));
		$this->insert('specialty',array('id'=>148,'name'=>'Pharmaceutical Medicine','code'=>'PHM','medical'=>1));
		$this->insert('specialty',array('id'=>149,'name'=>'Phlebology','code'=>'PHL','medical'=>1));
		$this->insert('specialty',array('id'=>150,'name'=>'Physical Medicine & Rehabilitation','code'=>'PM','medical'=>1));
		$this->insert('specialty',array('id'=>151,'name'=>'Plastic Surgery','code'=>'PS','medical'=>1));
		$this->insert('specialty',array('id'=>152,'name'=>'Plastic Surgery within the Head & Neck','code'=>'PSH','medical'=>1));
		$this->insert('specialty',array('id'=>153,'name'=>'Procedural Dermatology','code'=>'PRD','medical'=>1));
		$this->insert('specialty',array('id'=>154,'name'=>'Proctology','code'=>'PRO','medical'=>1));
		$this->insert('specialty',array('id'=>155,'name'=>'Psychiatry','code'=>'P','medical'=>1));
		$this->insert('specialty',array('id'=>156,'name'=>'Psychoanalysis','code'=>'PYA','medical'=>1));
		$this->insert('specialty',array('id'=>157,'name'=>'Psychosomatic Medicine','code'=>'PYM','medical'=>1));
		$this->insert('specialty',array('id'=>158,'name'=>'Public Health and General Preventive Medicine','code'=>'MPH','medical'=>1));
		$this->insert('specialty',array('id'=>159,'name'=>'Pulmonary Critical Care Medicine','code'=>'PCC','medical'=>1));
		$this->insert('specialty',array('id'=>160,'name'=>'Pulmonary Disease','code'=>'PUD','medical'=>1));
		$this->insert('specialty',array('id'=>161,'name'=>'Radiation Oncology','code'=>'RO','medical'=>1));
		$this->insert('specialty',array('id'=>162,'name'=>'Radiological Physics','code'=>'RP','medical'=>1));
		$this->insert('specialty',array('id'=>163,'name'=>'Radiology','code'=>'R','medical'=>1));
		$this->insert('specialty',array('id'=>164,'name'=>'Radioisotopic Pathology','code'=>'RIP','medical'=>1));
		$this->insert('specialty',array('id'=>165,'name'=>'Reproductive Endocrinology & Infertility','code'=>'REN','medical'=>1));
		$this->insert('specialty',array('id'=>166,'name'=>'Rheumatology','code'=>'RHU','medical'=>1));
		$this->insert('specialty',array('id'=>167,'name'=>'Selective Pathology','code'=>'SP','medical'=>1));
		$this->insert('specialty',array('id'=>168,'name'=>'Sleep Medicine','code'=>'SM','medical'=>1));
		$this->insert('specialty',array('id'=>169,'name'=>'Spinal Cord Injury Medicine','code'=>'SCI','medical'=>1));
		$this->insert('specialty',array('id'=>170,'name'=>'Sports Medicine (Emergency Medicine)','code'=>'ESM','medical'=>1));
		$this->insert('specialty',array('id'=>171,'name'=>'Sports Medicine (Family Practice)','code'=>'FSM','medical'=>1));
		$this->insert('specialty',array('id'=>172,'name'=>'Sports Medicine (Internal Medicine)','code'=>'ISM','medical'=>1));
		$this->insert('specialty',array('id'=>173,'name'=>'Sports Medicine (Orthopedic Surgery)','code'=>'OSM','medical'=>1));
		$this->insert('specialty',array('id'=>174,'name'=>'Sports Medicine (Pediatrics)','code'=>'PSM','medical'=>1));
		$this->insert('specialty',array('id'=>175,'name'=>'Sports Medicine (Physical Medicine & Rehabilitation)','code'=>'PMM','medical'=>1));
		$this->insert('specialty',array('id'=>176,'name'=>'Surgical Critical Care (Surgery)','code'=>'CCS','medical'=>1));
		$this->insert('specialty',array('id'=>177,'name'=>'Surgical Oncology','code'=>'SO','medical'=>1));
		$this->insert('specialty',array('id'=>178,'name'=>'Thoracic Surgery','code'=>'TS','medical'=>1));
		$this->insert('specialty',array('id'=>179,'name'=>'Trauma Surgery','code'=>'TRS','medical'=>1));
		$this->insert('specialty',array('id'=>180,'name'=>'Transplant Surgery','code'=>'TTS','medical'=>1));
		$this->insert('specialty',array('id'=>181,'name'=>'Undersea & Hyperbaric Medicine (Emergency Medicine)','code'=>'UME','medical'=>1));
		$this->insert('specialty',array('id'=>182,'name'=>'Undersea & Hyperbaric Medicine (Preventive Medicine)','code'=>'UM','medical'=>1));
		$this->insert('specialty',array('id'=>183,'name'=>'Unspecified','code'=>'US','medical'=>1));
		$this->insert('specialty',array('id'=>184,'name'=>'Urgent Care Medicine','code'=>'UCM','medical'=>1));
		$this->insert('specialty',array('id'=>185,'name'=>'Urology','code'=>'U','medical'=>1));
		$this->insert('specialty',array('id'=>186,'name'=>'Vascular and Interventional Radiology','code'=>'VIR','medical'=>1));
		$this->insert('specialty',array('id'=>187,'name'=>'Vascular Medicine','code'=>'VM','medical'=>1));
		$this->insert('specialty',array('id'=>188,'name'=>'Vascular Neurology','code'=>'VN','medical'=>1));
		$this->insert('specialty',array('id'=>189,'name'=>'Vascular Surgery','code'=>'VS','medical'=>1));
	}
}
