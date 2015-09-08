<?php

class m150902_091538_nod_export extends CDbMigration
{
	public function up()
	{
		$storedProcedure = <<<EOL
           -- Configuration settings for this script --
			SET SESSION group_concat_max_len = 100000;
			SET max_sp_recursion_depth = 1024;

			SET @time_now = unix_timestamp(now());

			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_surgeons;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_surgeons(IN dir VARCHAR(255))
			BEGIN
			  SET @file = concat( dir, '/surgeons_', @time_now, '.csv');
			  SET @cmd = concat("(SELECT 'id','registration code','title', 'first name')
								UNION (SELECT id, IFNULL(registration_code, 'NULL'), IFNULL(title, 'NULL'), IFNULL(first_name, 'NULL') FROM user INTO OUTFILE '",@file,
								"' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
								"  LINES TERMINATED BY '\r\n')");

			  PREPARE statement FROM @cmd;
			  EXECUTE statement;

			END $$
			DELIMITER ;


			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_patients;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_patients(IN dir VARCHAR(255))
			  BEGIN
				CREATE TEMPORARY TABLE temp_patients SELECT id, gender, ethnic_group_id, dob, date_of_death FROM patient;
				UPDATE temp_patients SET gender = (SELECT CASE WHEN gender='F' THEN 2 WHEN gender='M' THEN 1 END);

				#TODO: Add IMDScore and IsPrivate fields & confirm output type for ethnicity

				SET @file = concat( dir,'/ patients_', @time_now, '.csv');
				SET @cmd = concat("(SELECT 'id','gender','ethnic_group_id', 'dob', 'date of death')
								  UNION (SELECT id, IFNULL(gender, 'NULL'), IFNULL(ethnic_group_id, 'NULL'), IFNULL(dob, 'NULL'), IFNULL(date_of_death, 'NULL') FROM temp_patients INTO OUTFILE '",@file,
								  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
								  "  LINES TERMINATED BY '\r\n')");

				PREPARE statement FROM @cmd;
				EXECUTE statement;
				DROP TEMPORARY TABLE temp_patients;

			  END $$
			DELIMITER ;




			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_patient_cvi_status;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_patient_cvi_status(IN dir VARCHAR(255))
			  BEGIN
				CREATE TEMPORARY TABLE temp_patient_cvi_status SELECT id as PatientId, cvi_status_date as Date, cvi_status_id FROM patient_oph_info;
				ALTER TABLE temp_patient_cvi_status ADD IsDateApprox TINYINT DEFAULT 0 NOT NULL, ADD IsCVIBlind TINYINT DEFAULT 0 NOT NULL, ADD IsCVIPartial TINYINT DEFAULT 0 NOT NULL;
				UPDATE temp_patient_cvi_status SET IsCVIBlind = (SELECT CASE WHEN cvi_status_id=4 THEN 1 END),
												   IsCVIPartial = (SELECT CASE WHEN cvi_status_id=3 THEN 1 END),
												   IsDateApprox = (SELECT CASE WHEN DAYNAME(Date) IS NULL THEN 1 END);


				SET @file = concat(dir, '/patient_cvi_status_', @time_now, '.csv');
				SET @cmd = concat("(SELECT 'PatientId', 'Date', 'IsDateApprox', 'IsCVIBlind', 'IsCVIPartial')
								  UNION (SELECT PatientId, Date, IsCVIBlind, IsCVIPartial, IsDateApprox FROM temp_patient_cvi_status INTO OUTFILE '", @file,
								  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
								  "  LINES TERMINATED BY '\r\n')");

				PREPARE statement FROM @cmd;
				EXECUTE statement;
				DROP TABLE temp_patient_cvi_status;


			  END $$

			DELIMITER ;


			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_nod_episodes;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_nod_episodes(IN dir VARCHAR(255))
			  BEGIN

				SET @file = concat(dir,'/episodes_', @time_now, '.csv');

				SET @cmd = concat("(SELECT 'PatientId', 'EpisodeId', 'Date')
						   UNION (SELECT patient_id, id, start_date FROM episode INTO OUTFILE '",@file,
						   "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
						   "  LINES TERMINATED BY '\r\n')");

				PREPARE statement FROM @cmd;
				EXECUTE statement;

			  END $$
			DELIMITER ;


			-- Episode Diagnosis --
			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_episodes_diagnosis;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_episodes_diagnosis(IN dir VARCHAR(255))
			  BEGIN
				CREATE TABLE temp_episodes_diagnosis SELECT id , firm_id, eye_id, last_modified_date ,disorder_id FROM episode;
				ALTER TABLE  temp_episodes_diagnosis ADD SurgeonId INTEGER(10), ADD ConditionId INTEGER(10), ADD Eye Varchar(10);


				SET @count = (SELECT count(*) FROM temp_episodes_diagnosis);
				SET @ids =  (SELECT substring_index(group_concat(id SEPARATOR ','), ',', @count) FROM temp_episodes_diagnosis);

				WHILE (LOCATE(',', @ids) > 0) DO
				  SET @ids = SUBSTRING(@ids, LOCATE(',', @ids) + 1);
				  SET @id =  (SELECT TRIM(SUBSTRING_INDEX(@ids, ',', 1)));
				  SET @id = TRIM(@id);

				  SET @surgeon_id = (select last_modified_user_id from episode_version where id=@id order by last_modified_date asc limit 1);
				  SET @condition_id= (SELECT service_subspecialty_assignment_id FROM firm WHERE id = (SELECT firm_id from temp_episodes_diagnosis WHERE id = @id));

				  IF ( @surgeon_id IS NULL) THEN
					SET @surgeon_id = (SELECT last_modified_user_id FROM episode WHERE id=@id);
				  END IF;

				  UPDATE temp_episodes_diagnosis SET SurgeonId = @surgeon_id, ConditionId = @condition_id, Eye= (SELECT CASE WHEN eye_id = 1 THEN 'L' WHEN eye_id = 2 THEN 'R' WHEN eye_id = 3 THEN 'Both' WHEN eye_id IS NULL THEN 'N' END ) WHERE id = @id;

				END WHILE;

				#TODO: Map conditionId and DiagnosisTermId

				SET @file = concat(dir,'/episode_diagnosis_', @time_now, '.csv');
				SET @cmd = concat("(SELECT 'EpisodeId', 'Eye', 'Date', 'SurgeonId', 'ConditionId', 'DiagnosisTermId')
								   UNION (SELECT id, Eye, last_modified_date, SurgeonId, ConditionId, disorder_id FROM temp_episodes_diagnosis INTO OUTFILE '", @file ,
								   "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
								   "  LINES TERMINATED BY '\r\n')");

				PREPARE statement FROM @cmd;
				EXECUTE statement;

				DROP TABLE temp_episodes_diagnosis;

			  END $$

			DELIMITER ;




			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_episode_diabetic_diagnosis;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_diabetic_diagnosis(IN dir VARCHAR(255))
			  BEGIN

				SET @count = (select count(*) from disorder where fully_specified_name like '%diabet%');
				SET @diabetes_ids = (SELECT substring_index(group_concat(id SEPARATOR ','), ',', @count) FROM disorder where term like '%diabet%');
				CREATE TABLE temp_episode_diabetic_diagnosis SELECT e.id, s.patient_id, s.disorder_id, s.date, p.dob FROM secondary_diagnosis s
															 LEFT JOIN disorder d ON d.id = s.disorder_id
															 LEFT JOIN episode e ON e.patient_id = s.patient_id
															 LEFT JOIN patient p ON e.patient_id = p.id;

				ALTER TABLE temp_episode_diabetic_diagnosis ADD IsDiabetic TINYINT DEFAULT 0 NOT NULL,
															ADD DiabetesTypeId INTEGER(10),
															ADD DiabetesRegimeId INTEGER(10) DEFAULT 9 NOT NULL,
															ADD AgeAtDiagnosis INTEGER(3),
															ADD edd_id INTEGER NOT NULL AUTO_INCREMENT FIRST, ADD primary KEY(edd_id);

				SET @type_1 = ('23045005,28032008,46635009,190368000,190369008,190371008,190372001,199229001,237618001,290002008,313435000,314771006,314893005,
							   314894004,401110002,420270002,420486006,420514000,420789003,420825003,420868002,420918009,421165007,421305000,421365002,421468001,
							   421893009,421920002,422228004,422297002,425159004,425442003,426907004,427571000,428896009,11530004');

				SET @type_2 = ('9859006,44054006,81531005,190388001,190389009,190390000,190392008,199230006,237599002,237604008,237614004,237650006,
							   313436004,314772004,314902007,314903002,314904008,359642000,395204000,420279001,420414003,420436000,420715001,420756003,
							   421326000,421631007,421707005,421750000,421779007,421847006,421986006,422014003,422034002,422099009,422166005,423263001,
							   424989000,427027005,427134009,428007007,359638003');

				SET @gestational = ('237626009,237627000,11687002,46894009,71546005,75022004,420491007,420738003,420989005,421223006,421389009,421443003,
									422155003,76751001,199223000,199225007,199227004');

				SET @midd = ('237619009,359939009');

				SET @modd = ('14052004,28453007');

				SET @other = ('2751001,4307007,4783006,5368009,5969009,8801005,33559001,42954008,49817004,51002006,57886004,59079001,70694009,
							  73211009,75524006,75682002,111552007,111554008,127012008,190329007,190330002,190331003,190406000,190407009,190410002,
							  190411003,190412005,190416008,190447002,199226008,199228009,199231005,237600004,237601000,237603002,237611007,237612000,
							  237616002,237617006,237620003,238981002,275918005,276560009,408540003,413183008,420422005,420683009,421256007,421895002,
							  422088007,422183001,422275004,426705001,426875007,427089005,441628001,91352004,399144008');

				#TODO: Update DiabetesRegimeId

				SET @update_cmd = concat('UPDATE temp_episode_diabetic_diagnosis SET IsDiabetic = 1, AgeAtDiagnosis = DATEDIFF(date, dob)/365,
										  DiabetesTypeId = (SELECT CASE WHEN disorder_id IN (',@type_1,') THEN 1
																		WHEN disorder_id IN (',@type_2,') THEN 2
																		WHEN disorder_id IN (',@gestational,') THEN 3
																		WHEN disorder_id IN (',@midd,') THEN 4
																		WHEN disorder_id IN (',@modd,') THEN 5
																		WHEN disorder_id IN (',@other,') THEN 9
																		END )
										  WHERE disorder_id IN (',@diabetes_ids,')');

				PREPARE update_statement FROM @update_cmd;
				EXECUTE update_statement;



				SET @file = concat('/tmp/episode_diabetic_diagnosis_', @time_now, '.csv');
				SET @cmd = concat("(SELECT 'EpisodeId', 'IsDiabetic', 'DiabetesTypeId', 'DiabetesRegimeId', 'AgeAtDiagnosis')
						  UNION (SELECT id, IsDiabetic, DiabetesTypeId, DiabetesRegimeId, AgeAtDiagnosis FROM temp_episode_diabetic_diagnosis INTO OUTFILE '", @file ,
								  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
								  "  LINES TERMINATED BY '\r\n')");

				PREPARE statement FROM @cmd;
				EXECUTE statement;

				DROP TABLE temp_episode_diabetic_diagnosis;

			  END $$

			DELIMITER ;


			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_episode_drug;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_drug(IN dir VARCHAR(255))
			  BEGIN

				CREATE VIEW nod_episode_drug AS SELECT e.id as EpisodeId , dr.id as DrugRouteId,
												  (SELECT CASE WHEN option_id = 1 THEN 'L' WHEN option_id = 2 THEN 'R' WHEN option_id = 3 THEN 'B'  ELSE 'N' END) AS Eye,
												  (SELECT CASE WHEN m.drug_id IS NOT NULL THEN (SELECT name FROM drug WHERE id = m.drug_id) WHEN m.drug_id IS NULL THEN ''
														  WHEN m.medication_drug_id IS NOT NULL THEN (SELECT name FROM medication_drug WHERE id = m.drug_id) WHEN m.medication_drug_id IS NULL THEN '' END) as DrugId,
												  (SELECT CASE WHEN DAYNAME(m.start_date) IS NULL THEN 1 ELSE 0 END) AS IsStartDateApprox,
												  (SELECT CASE WHEN opi.prescription_id IS NOT NULL THEN 1 ELSE 0 END ) AS IsAddedByPrescription,
												  (SELECT CASE WHEN m.start_date IS NULL THEN '' ELSE m.start_date END) AS StartDate,
												  (SELECT CASE WHEN m.end_date IS NULL THEN '' ELSE m.end_date END) AS StopDate,
												  (SELECT CASE WHEN opi.continue_by_gp IS NULL THEN 0 ELSE opi.continue_by_gp END) AS IsContinueIndefinitely

				FROM episode e
				INNER JOIN medication m ON e.patient_id = m.patient_id
				LEFT JOIN drug_route dr ON dr.id = m.route_id
				LEFT JOIN event ev ON ev.episode_id = e.id
				LEFT JOIN event_type evt ON evt.id = ev.event_type_id
				LEFT JOIN et_ophdrprescription_details etp ON etp.event_id = ev.id
				LEFT JOIN ophdrprescription_item opi ON etp.id = opi.prescription_id
				GROUP BY episode_id;

				SET @file = concat('/tmp/episode_drug_', @time_now, '.csv');
				SET @cmd = concat("(SELECT 'EpisodeId', 'Eye', 'DrugId', 'DrugRouteId', 'StartDate', 'StopDate', 'IsAddedByPrescription', 'IsContinueIndefinitely', 'IsStartDateApprox')
								  UNION (SELECT EpisodeId, Eye, DrugId, DrugRouteId, StartDate, StopDate, IsAddedByPrescription, IsContinueIndefinitely, IsStartDateApprox FROM nod_episode_drug INTO OUTFILE '", @file,
								  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
								  "  LINES TERMINATED BY '\r\n')");

				PREPARE statement FROM @cmd;
				EXECUTE statement;

				DROP VIEW nod_episode_drug;

			  END $$
			DELIMITER ;



			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_episode_biometry;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_biometry(IN dir VARCHAR(255))
			  BEGIN

				CREATE VIEW nod_episode_biometry AS SELECT e.id AS EpisodeId
													FROM episode e
													LEFT JOIN event ev ON ev.episode_id = e.id
													LEFT JOIN event_type et ON et.id = ev.event_type_id
													WHERE et.id = 17;
				#TODO update biometry data in database

			  END $$
			DELIMITER ;


			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_episode_iop;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_iop(IN dir VARCHAR(255))
			  BEGIN

				CREATE VIEW nod_episode_iop AS SELECT e.id AS EpisodeId, oipv.reading_id,
											   (SELECT CASE WHEN oipv.eye_id = 1 THEN 'L' WHEN oipv.eye_id = 2 THEN 'R' END) As Eye
											   FROM episode e
											   JOIN event ev ON ev.episode_id = e.id
											   JOIN event_type et ON et.id = ev.event_type_id
											   JOIN et_ophciexamination_intraocularpressure etoi ON etoi.event_id = ev.id
											   JOIN ophciexamination_intraocularpressure_value oipv ON oipv.element_id = etoi.id
											   WHERE et.name = 'Examination'
											   GROUP BY e.id;

				#TODO complete query after talk with Toby

			  END $$
			DELIMITER ;



			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_EpisodePreOpAssessment;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_EpisodePreOpAssessment(IN dir VARCHAR(255))
			  BEGIN

				CREATE VIEW nod_episode_preop_assessment AS SELECT e.id AS EpisodeId,
															(SELECT CASE WHEN pl.eye_id = 1 THEN 'L' WHEN pl.eye_id = 2 THEN 'R' WHEN pl.eye_id = 3 THEN 'B' END) AS Eye,
															(SELECT CASE WHEN pr.risk_id IS NULL THEN 0 WHEN pr.risk_id = 1 THEN 1 ELSE 0 END) AS IsAbleToLieFlat,
															(SELECT CASE WHEN pr.risk_id IS NULL THEN 0 WHEN pr.risk_id = 4 THEN 1 ELSE 0 END) AS IsInabilityToCooperate
															FROM episode e
															LEFT JOIN event ev ON ev.episode_id = e.id
															JOIN et_ophtroperationnote_procedurelist pl ON pl.event_id = ev.id
															LEFT JOIN patient_risk_assignment pr ON pr.patient_id = e.patient_id
															GROUP BY e.id;

				SET @file = concat('/tmp/episode_preop_assessment_', @time_now, '.csv');
				SET @cmd = concat("(SELECT 'EpisodeId', 'Eye', 'IsAbleToLieFlat', 'IsInabilityToCooperate')
							UNION (SELECT EpisodeId, Eye, IsAbleToLieFlat, IsInabilityToCooperate FROM nod_episode_preop_assessment INTO OUTFILE '", @file,
							"' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
							"  LINES TERMINATED BY '\r\n')");

				PREPARE statement FROM @cmd;
				EXECUTE statement;

				DROP VIEW nod_episode_preop_assessment;

			  END $$
			DELIMITER ;



			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_episode_refraction;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_refraction(IN dir VARCHAR(255))
			  BEGIN

				CREATE VIEW nod_episode_refraction AS (SELECT e.episode_id AS EpisodeId, r.left_sphere AS Sphere, r.left_cylinder AS Cylinder, r.left_axis AS Axis, '' as RefractionTypeId, '' AS ReadingAdd,
													  (SELECT CASE WHEN r.eye_id = 1 THEN 'L' END) AS Eye
													  FROM event e
													  INNER JOIN et_ophciexamination_refraction r ON r.event_id = e.id
													  WHERE r.eye_id = 1)
													  UNION
													  (SELECT e.episode_id As EpisodeId, r.right_sphere AS Sphere, r.right_cylinder AS Cylinder, r.right_axis AS Axis, '' as RefractionTypeId, '' AS ReadingAdd,
													  (SELECT CASE WHEN r.eye_id = 2 THEN 'R' END) AS Eye
													  FROM event e
													  INNER JOIN et_ophciexamination_refraction r ON r.event_id = e.id
													  WHERE r.eye_id = 2);


				SET @file = concat('/tmp/episode_refraction_', @time_now, '.csv');
				SET @cmd = concat("(SELECT 'EpisodeId', 'Eye', 'RefractionTypeId', 'Sphere', 'Cylinder', 'Axis', 'ReadingAdd')
								  UNION (SELECT EpisodeId, Eye, RefractionTypeId, Sphere, Cylinder, Axis, ReadingAdd FROM  nod_episode_refraction INTO OUTFILE '", @file,
								  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
								  "  LINES TERMINATED BY '\r\n')");

				PREPARE statement FROM @cmd;
				EXECUTE statement;

				DROP VIEW nod_episode_refraction;

			  END $$
			DELIMITER ;


			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_episode_visual_acuity;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_visual_acuity(IN dir VARCHAR(255))
			  BEGIN

				CREATE VIEW nod_episode_visual_acuity AS SELECT e.episode_id AS EpisodeId, v.unit_id AS NotationRecordedId,
														   (SELECT CASE WHEN v.eye_id = 1 THEN 'L' WHEN v.eye_id = 2 THEN 'R' END) AS Eye,
														   (SELECT max(value) FROM ophciexamination_visualacuity_reading r JOIN et_ophciexamination_visualacuity va ON va.id = r.element_id WHERE r.element_id = v.id AND va.unit_id = (SELECT id FROM ophciexamination_visual_acuity_unit WHERE name = 'logMAR single-letter')) AS BestMeasure,
														   #(SELECT value from ophciexamination_visualacuity_reading r JOIN et_ophciexamination_visualacuity va ON va.id = r.element_id WHERE r.element_id = v.id AND method_id = 1) AS Unaided,
														   NULL AS Unaided, NULL As Pinhole, NULL AS BestCorrected
														 FROM event e
														 INNER JOIN et_ophciexamination_visualacuity v ON v.event_id = e.id
														 WHERE v.eye_id != 3;
				#TODO: Unaided, Pinhole, BestCorrected

				SET @file = concat('/tmp/episode_visual_acuity_', @time_now, '.csv');
				SET @cmd = concat("(SELECT 'EpisodeId', 'Eye', 'NotationRecordedId', 'BestMeasure', 'Unaided', 'Pinhole', 'BestCorrected')
						   UNION (SELECT EpisodeId, Eye, NotationRecordedId, BestMeasure, Unaided, Pinhole, BestCorrected FROM nod_episode_visual_acuity INTO OUTFILE '", @file,
						   "'  FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
						   "  LINES TERMINATED BY '\r\n')");

				PREPARE statement FROM @cmd;
				EXECUTE statement;

				DROP VIEW nod_episode_visual_acuity;

			  END $$
			DELIMITER ;




			DELIMITER $$
			DROP PROCEDURE IF EXISTS get_episode_operation;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_operation(IN dir VARCHAR(255))
			  BEGIN

				CREATE VIEW nod_episode_operation AS SELECT e.id AS OperationId, e.episode_id AS EpisodeId, /*pr.snomed_term AS Description,*/ NULL AS IsHypertensive, e.event_date As ListedDate, s.surgeon_id AS SurgeonId
													 FROM event e
													 JOIN event_type evt ON evt.id = e.event_type_id
													 #INNER JOIN et_ophtroperationnote_procedurelist pl ON pl.booking_event_id = e.id
													 #INNER JOIN ophtroperationnote_procedurelist_procedure_assignment pa ON pa.procedurelist_id = pl.id
													 #INNER JOIN proc pr ON pr.id = pa.proc_id
													 LEFT JOIN et_ophtroperationnote_surgeon s ON s.event_id = e.id
													 WHERE evt.name = 'Operation booking';

				SET @file = concat(dir, '/episode_operation_', @time_now, '.csv');
				SET @cmd = ();

			  END $$
			DELIMITER ;


			DELIMITER
			DROP PROCEDURE IF EXISTS get_episode_treatment;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_treatment(IN dir VARCHAR(255))
			  BEGIN

			  	CREATE VIEW nod_episode_treatment AS SELECT pa.id AS TreatmentId, pl.event_id AS OperationId,
			  	                                     (SELECT CASE WHEN pl.eye_id = 1 THEN 'L' WHEN pl.eye_id = 2 THEN 'R' END) As Eye,
			  	                                     pa.proc_id AS TreatmentTypeId
			  	                                     FROM ophtroperationnote_procedurelist_procedure_assignment pa
			  	                                     JOIN et_ophtroperationnote_procedurelist pl ON pa.procedurelist_id = pl.event_id;

			  	SET @file = concat(dir, '/episode_treatment_', @time_now, '.csv');
			  	SET @cms = concat("(SELECT 'TreatmentId', 'OperationId', 'Eye', 'TreatmentTypeId')
			  	           UNION (SELECT TreatmentId, OperationId, Eye, TreatmentTypeId FROM nod_episode_treatment INTO OUTFILE '", @file,
			  	           "'  FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
						   "  LINES TERMINATED BY '\r\n')");

                PREPARE statement FROM @cmd;
				EXECUTE statement;

				DROP VIEW nod_episode_treatment;

			  END $$
			DELIMITER ;


			DELIMITER
			DROP PROCEDURE IF EXISTS get_episode_treatment_cataract;
			CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_treatment_cataract(IN dir VARCHAR(255))
			  BEGIN
				CREATE VIEW nod_episode_treatment_cataract AS SELECT pa1.id,
				 											  (	SELECT CASE WHEN pl.eye_id = 1 THEN 'L' WHEN pl.eye_id = 2 THEN 'R' END
															   	FROM et_ophtroperationnote_procedurelist pl
															   	JOIN ophtroperationnote_procedurelist_procedure_assignment pa ON pa.procedurelist_id = pl.event_id
															   	JOIN event e ON e.id = pl.event_id
																JOIN episode ep ON ep.id = e.episode_id
																GROUP BY ep.patient_id
																ORDER BY pl.last_modified_date desc
																WHERE pa.id = pa1.id
																) AS Eye,
																NULL AS PreparationDrugId
															  FROM ophtroperationnote_procedurelist_procedure_assignment pa1
															  JOIN et_ophtroperationnote_procedurelist pl1 ON pa1.procedurelist_id = pl1.event_id;


			  END
			DELIMITER ;









			DELIMITER $$
			DROP PROCEDURE IF EXISTS run_nod_export_generator;
			CREATE DEFINER=`root`@`localhost` PROCEDURE run_nod_export_generator(IN dir VARCHAR(255))
			  BEGIN

				call get_surgeons(dir);
				call get_patients(dir);
				call get_patient_cvi_status(dir);
				call get_nod_episodes(dir);
				call get_episodes_diagnosis(dir);
				call get_episode_diabetic_diagnosis(dir);
				call get_episode_drug(dir);
				call get_EpisodePreOpAssessment(dir);
				call get_episode_refraction(dir);
				call get_episode_visual_acuity(dir);
				call get_episode_treatment(dir);

			  END $$
			DELIMITER ;





EOL;

	}

	public function down()
	{
		$this->execute('DROP PROCEDURE IF EXISTS get_surgeons;');
		$this->execute('DROP PROCEDURE IF EXISTS get_patients;');
		$this->execute('DROP PROCEDURE IF EXISTS get_patient_cvi_status;');
		$this->execute('DROP PROCEDURE IF EXISTS get_nod_episodes;');
		$this->execute('DROP PROCEDURE IF EXISTS get_episodes_diagnosis;');
		$this->execute('DROP PROCEDURE IF EXISTS get_episode_diabetic_diagnosis;');
		$this->execute('DROP PROCEDURE IF EXISTS get_episode_drug;');
		$this->execute('DROP PROCEDURE IF EXISTS get_EpisodePreOpAssessment;');
		$this->execute('DROP PROCEDURE IF EXISTS get_episode_refraction;');
		$this->execute('DROP PROCEDURE IF EXISTS get_episode_visual_acuity;');
		$this->execute('DROP PROCEDURE IF EXISTS get_episode_treatment;');
		$this->execute('DROP PROCEDURE IF EXISTS run_nod_export_generator;');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}