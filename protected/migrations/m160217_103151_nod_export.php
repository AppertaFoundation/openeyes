<?php

class m160217_103151_nod_export extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationnote_cataract', 'pupil_size', 'VARCHAR(10)');
        $this->addColumn('et_ophtroperationnote_cataract_version', 'pupil_size', 'VARCHAR(10)');

        $cataracts = $this->getDbConnection()->createCommand()->select('id, eyedraw')->from('et_ophtroperationnote_cataract')->queryAll();

        foreach ($cataracts as $cataract) {
            $eyedraw = json_decode($cataract['eyedraw']);
            $pupilSize = null;
            if (is_array($eyedraw)) {
                foreach ($eyedraw as $eyedrawEl) {
                    if (property_exists($eyedrawEl, 'pupilSize')) {
                        $pupilSize = $eyedrawEl->pupilSize;
                        break;
                    }
                }
            }
            if ($pupilSize) {
                $this->update('et_ophtroperationnote_cataract', array('pupil_size' => $pupilSize), 'id='.$cataract['id']);
            }
        }

        $storedProcedure = <<<EOL
-- Configuration settings for this script --
SET SESSION group_concat_max_len = 100000;
SET max_sp_recursion_depth = 255;

                        -- Surgeon --
                        
DROP PROCEDURE IF EXISTS get_surgeons;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_surgeons(IN dir VARCHAR(255))
BEGIN
CREATE TEMPORARY TABLE tmp_doctor_grade (
	`code` INT(10) UNSIGNED NOT NULL,
	`desc` VARCHAR(100)
);

INSERT INTO tmp_doctor_grade (`code`, `desc`)
VALUES
(0, 'Consultant'),
(1, 'Locum Consultant'),
(2, 'corneal burn'),
(3, 'Associate Specialist'),
(4, 'Fellow'),
(5, 'Registrar'),
(6, 'Staff Grade'),
(7, 'Trust Doctor'),
(8, 'Senior House Officer'),
(9, 'Specialty trainee (year 1)'),
(10, 'Specialty trainee (year 2)'),
(11, 'Specialty trainee (year 3)'),
(12, 'Specialty trainee (year 4)'),
(13, 'Specialty trainee (year 5)'),
(14, 'Specialty trainee (year 6)'),
(15, 'Specialty trainee (year 7)'),
(16, 'Foundation Year 1 Doctor'),
(17, 'Foundation Year 2 Doctor'),
(18, 'GP with a special interest in ophthalmology'),
(19, 'Community ophthalmologist'),
(20, 'Anaesthetist'),
(21, 'Orthoptist'),
(22, 'Optometrist'),
(23, 'Clinical nurse specialist'),
(24, 'Nurse'),
(25, 'Health Care Assistant'),
(26, 'Ophthalmic Technician'),
(27, 'Surgical Care Practitioner'),
(28, 'Clinical Assistant'),
(29, 'RG1'),
(30, 'RG2'),
(31, 'ODP'),
(32, 'Administration staff'),
(33, 'Other');
    
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @file = CONCAT(dir, '/surgeons_', @time_now, '.csv');
SET @cmd = CONCAT("(SELECT 'Surgeonid','GMCnumber','Title', 'FirstName', 'CurrentGradeId')
		UNION (SELECT id, IFNULL(registration_code, 'NULL'), IFNULL(title, 'NULL'), IFNULL(first_name, 'NULL'),
                (
			SELECT `code` 
			FROM tmp_doctor_grade, doctor_grade
			WHERE user.`doctor_grade_id` = doctor_grade.id AND doctor_grade.`grade` = tmp_doctor_grade.desc
			
		 ) AS CurrentGradeId
                FROM user 
                WHERE is_surgeon = 1 AND active = 1
                INTO OUTFILE '",@file,
		"' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
		"  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

DROP TEMPORARY TABLE tmp_doctor_grade;
                        
END;

                        -- Patient --
                        
DROP PROCEDURE IF EXISTS get_patients;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_patients(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
CREATE TEMPORARY TABLE temp_patients SELECT id, gender, ethnic_group_id, dob, date_of_death FROM patient;
UPDATE temp_patients SET gender = (SELECT CASE WHEN gender='F' THEN 2 WHEN gender='M' THEN 1 ELSE 9 END);

#TODO: Add IMDScore and IsPrivate fields & confirm output type for ethnicity

SET @file = CONCAT(dir, '/patients_', @time_now, '.csv');
SET @cmd = CONCAT("(SELECT 'PatientId','GenderId','EthnicityId', 'DateOfBirth', 'DateOfDeath', 'IMDScore', 'IsPrivate')
  UNION (SELECT id, IFNULL(gender, 'NULL'), IFNULL(ethnic_group_id, 'NULL'), IFNULL(dob, 'NULL'), IFNULL(date_of_death, 'NULL'), '', '' FROM temp_patients INTO OUTFILE '",@file,
  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
  "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;
DROP TEMPORARY TABLE temp_patients;

END;

                        -- PatientCVIStatus --

DROP PROCEDURE IF EXISTS get_patient_cvi_status;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_patient_cvi_status(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
CREATE TEMPORARY TABLE temp_patient_cvi_status SELECT id AS PatientId, cvi_status_date AS DATE, cvi_status_id FROM patient_oph_info;
ALTER TABLE temp_patient_cvi_status ADD IsDateApprox TINYINT DEFAULT 0 NOT NULL, ADD IsCVIBlind TINYINT DEFAULT 0 NOT NULL, ADD IsCVIPartial TINYINT DEFAULT 0 NOT NULL;
UPDATE temp_patient_cvi_status SET IsCVIBlind = (SELECT CASE WHEN cvi_status_id=4 THEN 1 END),
						   IsCVIPartial = (SELECT CASE WHEN cvi_status_id=3 THEN 1 END),
						   IsDateApprox = (SELECT CASE WHEN DAYNAME(DATE) IS NULL THEN 1 END);


SET @file = CONCAT(dir, '/patient_cvi_status_', @time_now, '.csv');
SET @cmd = CONCAT("(SELECT 'PatientId', 'Date', 'IsDateApprox', 'IsCVIBlind', 'IsCVIPartial')
		  UNION (SELECT PatientId, Date, IsCVIBlind, IsCVIPartial, IsDateApprox FROM temp_patient_cvi_status INTO OUTFILE '", @file,
		  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
		  "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;
DROP TABLE temp_patient_cvi_status;

END;

                        -- Episode --
                        
DROP PROCEDURE IF EXISTS get_nod_episodes;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_nod_episodes(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @file = CONCAT(dir, '/episodes_', @time_now, '.csv');

SET @cmd = CONCAT("(SELECT 'PatientId', 'EpisodeId', 'Date')
   UNION (SELECT patient_id, id, start_date FROM episode INTO OUTFILE '",@file,
   "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
   "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

END;

                        -- Episode Diagnosis --
                        
DROP PROCEDURE IF EXISTS get_episodes_diagnosis;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episodes_diagnosis(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
CREATE TABLE temp_episodes_diagnosis SELECT id , firm_id, eye_id, last_modified_date ,disorder_id FROM episode;
ALTER TABLE  temp_episodes_diagnosis ADD SurgeonId INTEGER(10), ADD ConditionId INTEGER(10), ADD Eye VARCHAR(10);

SET @count = (SELECT COUNT(*) FROM temp_episodes_diagnosis);
SET @ids =  (SELECT SUBSTRING_INDEX(GROUP_CONCAT(id SEPARATOR ','), ',', @count) FROM temp_episodes_diagnosis);

WHILE (LOCATE(',', @ids) > 0) DO
SET @ids = SUBSTRING(@ids, LOCATE(',', @ids) + 1);
SET @id =  (SELECT TRIM(SUBSTRING_INDEX(@ids, ',', 1)));
SET @id = TRIM(@id);

SET @surgeon_id = (SELECT last_modified_user_id FROM episode_version WHERE id=@id ORDER BY last_modified_date ASC LIMIT 1);
SET @condition_id= (SELECT service_subspecialty_assignment_id FROM firm WHERE id = (SELECT firm_id FROM temp_episodes_diagnosis WHERE id = @id));

IF ( @surgeon_id IS NULL) THEN
SET @surgeon_id = (SELECT last_modified_user_id FROM episode WHERE id=@id);
END IF;

UPDATE temp_episodes_diagnosis SET SurgeonId = @surgeon_id, ConditionId = @condition_id, Eye= (SELECT CASE WHEN eye_id = 1 THEN 'L' WHEN eye_id = 2 THEN 'R' WHEN eye_id = 3 THEN 'B' ELSE 'N' END ) WHERE id = @id;

END WHILE;

#TODO: Map conditionId and DiagnosisTermId

SET @file = CONCAT(dir, '/episode_diagnosis_', @time_now, '.csv');
SET @cmd = CONCAT("(SELECT 'EpisodeId', 'Eye', 'Date', 'SurgeonId', 'ConditionId', 'DiagnosisTermId')
		   UNION (SELECT id, Eye, last_modified_date, SurgeonId, ConditionId, disorder_id FROM temp_episodes_diagnosis INTO OUTFILE '", @file ,
		   "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
		   "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

DROP TABLE temp_episodes_diagnosis;

END;

                        -- EpisodeDiabeticDiagnosis --
                        
DROP PROCEDURE IF EXISTS get_episode_diabetic_diagnosis;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_diabetic_diagnosis(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @count = (SELECT COUNT(*) FROM disorder WHERE fully_specified_name LIKE '%diabet%');
SET @diabetes_ids = (SELECT SUBSTRING_INDEX(GROUP_CONCAT(id SEPARATOR ','), ',', @count) FROM disorder WHERE term LIKE '%diabet%');
CREATE TABLE temp_episode_diabetic_diagnosis SELECT e.id, s.patient_id, s.disorder_id, s.date, p.dob FROM secondary_diagnosis s
									 LEFT JOIN disorder d ON d.id = s.disorder_id
									 LEFT JOIN episode e ON e.patient_id = s.patient_id
									 LEFT JOIN patient p ON e.patient_id = p.id;

ALTER TABLE temp_episode_diabetic_diagnosis ADD IsDiabetic TINYINT DEFAULT 0 NOT NULL,
									ADD DiabetesTypeId INTEGER(10),
									ADD DiabetesRegimeId INTEGER(10) DEFAULT 9 NOT NULL,
									ADD AgeAtDiagnosis INTEGER(3),
									ADD edd_id INTEGER NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY(edd_id);

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

SET @update_cmd = CONCAT('UPDATE temp_episode_diabetic_diagnosis SET IsDiabetic = 1, AgeAtDiagnosis = DATEDIFF(date, dob)/365,
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



SET @file = CONCAT(dir, '/episode_diabetic_diagnosis_', @time_now, '.csv');
SET @cmd = CONCAT("(SELECT 'EpisodeId', 'IsDiabetic', 'DiabetesTypeId', 'DiabetesRegimeId', 'AgeAtDiagnosis')
  UNION (SELECT id, IsDiabetic, DiabetesTypeId, DiabetesRegimeId, AgeAtDiagnosis FROM temp_episode_diabetic_diagnosis INTO OUTFILE '", @file ,
		  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
		  "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

DROP TABLE temp_episode_diabetic_diagnosis;

END;

                        -- EpisodeDrug --
                        
DROP PROCEDURE IF EXISTS get_episode_drug;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_drug(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
CREATE VIEW nod_episode_drug AS SELECT e.id AS EpisodeId , dr.id AS DrugRouteId,
						  (SELECT CASE WHEN option_id = 1 THEN 'L' WHEN option_id = 2 THEN 'R' WHEN option_id = 3 THEN 'B'  ELSE 'N' END) AS Eye,
						  (SELECT CASE WHEN m.drug_id IS NOT NULL THEN (SELECT NAME FROM drug WHERE id = m.drug_id) WHEN m.drug_id IS NULL THEN ''
								  WHEN m.medication_drug_id IS NOT NULL THEN (SELECT NAME FROM medication_drug WHERE id = m.drug_id) WHEN m.medication_drug_id IS NULL THEN '' END) AS DrugId,
						  (SELECT CASE WHEN DAYNAME(m.start_date) IS NULL THEN 1 ELSE 0 END) AS IsStartDateApprox,
						  (SELECT CASE WHEN opi.prescription_id IS NOT NULL THEN 1 ELSE 0 END ) AS IsAddedByPrescription,
						  (SELECT CASE WHEN m.start_date IS NULL THEN '' ELSE m.start_date END) AS StartDate,
						  (SELECT CASE WHEN m.end_date IS NULL THEN '' ELSE m.end_date END) AS StopDate,
						  (SELECT CASE WHEN opi.continue_by_gp IS NULL THEN 0 ELSE opi.continue_by_gp END) AS IsContinueIndefinitely

FROM episode e
INNER JOIN medication m ON e.patient_id = m.patient_id
LEFT JOIN drug_route dr ON dr.id = m.route_id
LEFT JOIN `event` ev ON ev.episode_id = e.id
LEFT JOIN event_type evt ON evt.id = ev.event_type_id
LEFT JOIN et_ophdrprescription_details etp ON etp.event_id = ev.id
LEFT JOIN ophdrprescription_item opi ON etp.id = opi.prescription_id
GROUP BY episode_id;
                        
SET @file = CONCAT(dir, '/episode_drug_', @time_now, '.csv');
SET @cmd = CONCAT("(SELECT 'EpisodeId', 'Eye', 'DrugId', 'DrugRouteId', 'StartDate', 'StopDate', 'IsAddedByPrescription', 'IsContinueIndefinitely', 'IsStartDateApprox')
		  UNION (SELECT EpisodeId, Eye, DrugId, DrugRouteId, StartDate, StopDate, IsAddedByPrescription, IsContinueIndefinitely, IsStartDateApprox FROM nod_episode_drug INTO OUTFILE '", @file,
		  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
		  "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

DROP VIEW nod_episode_drug;

END;

                        -- EpisodeBiometry --
                        
DROP PROCEDURE IF EXISTS get_episode_biometry;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_biometry(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @file = CONCAT(dir, '/episode_biometry_', @time_now, '.csv');

   DROP TABLE IF EXISTS tmp_biometry_formula;

CREATE TABLE tmp_biometry_formula (
	`code` INT(10) UNSIGNED NOT NULL,
	`desc` VARCHAR(100)
);
INSERT INTO tmp_biometry_formula (`code`, `desc`)
VALUES
(1, 'Haigis'),
(2, 'Holladay'),
(3, 'Holladay II'),
(4, 'SRK/T'),
(5, 'SRK II'),
(6, 'Hoffer Q'),
(7, 'Average of SRK/T + Holladay + Hoffer Q'),
(9, 'Not recorded');
                        
CREATE TEMPORARY TABLE tmp_biometry AS 
(
        SELECT
                ev.`episode_id` AS EpisodeId,
                'L' AS Eye,
                axial_length_left AS AxialLength,
                NULL AS BiometryAScanId,
                (SELECT CASE
			WHEN ophinbiometry_imported_events.`device_model` = 'IOLmaster 500'  THEN 1
			WHEN ophinbiometry_imported_events.`device_model` = 'Haag-Streit LensStar' THEN 2
			WHEN ophinbiometry_imported_events.`device_model` = 'Other' THEN 9
		 END) AS BiometryKeratometerId,
                ( SELECT `code` FROM tmp_biometry_formula WHERE tmp_biometry_formula.`desc` = ophinbiometry_calculation_formula.name ) AS BiometryFormulaId,
                k1_left AS K1PreOperative,
                k2_left AS K2PreOperative,
                axis_k1_left AS AxisK1,
                ms.k2_axis_left AS AxisK2,
                ms.acd_left AS ACDepth,
                ms.snr_left AS SNR
        FROM episode ep
        JOIN `event` ev ON ep.id =  ev.`episode_id`
        JOIN event_type et ON ev.`event_type_id` = et.`id`
        JOIN et_ophinbiometry_measurement ms ON ev.id = ms.event_id
        JOIN `event` AS opnote ON ep.id = opnote.`episode_id`
		AND opnote.event_type_id = 4 AND opnote.created_date < ev.created_date
        JOIN ophinbiometry_imported_events ON ev.id = ophinbiometry_imported_events.`event_id`
	JOIN et_ophinbiometry_selection ON ev.id = et_ophinbiometry_selection.`event_id` AND et_ophinbiometry_selection.eye_id = 1 OR et_ophinbiometry_selection.eye_id = 3
	JOIN ophinbiometry_calculation_formula ON et_ophinbiometry_selection.`formula_id_left` = ophinbiometry_calculation_formula.id
        WHERE et.id = 37
        AND ms.deleted = 0
        AND ev.deleted = 0
)
UNION
(
	SELECT
                ev.`episode_id` AS EpisodeId,
                'R' AS Eye,
                axial_length_right AS AxialLength,
                NULL AS BiometryAScanId,
                (SELECT CASE
			WHEN ophinbiometry_imported_events.`device_model` = 'IOLmaster 500'  THEN 1
			WHEN ophinbiometry_imported_events.`device_model` = 'Haag-Streit LensStar' THEN 2
			WHEN ophinbiometry_imported_events.`device_model` = 'Other' THEN 9
		 END) AS BiometryKeratometerId,
                ( SELECT `code` FROM tmp_biometry_formula WHERE tmp_biometry_formula.`desc` = ophinbiometry_calculation_formula.name ) AS BiometryFormulaId,
                k1_right AS K1PreOperative,
                k2_right AS K2PreOperative,
                axis_k1_right AS AxisK1,
                ms.k2_axis_right AS AxisK2,
                ms.acd_right AS ACDepth,
                ms.snr_right AS SNR
        FROM episode ep
        JOIN `event` ev ON ep.id =  ev.`episode_id`
        JOIN event_type et ON ev.`event_type_id` = et.`id`
        JOIN et_ophinbiometry_measurement ms ON ev.id = ms.event_id
        JOIN `event` AS opnote ON ep.id = opnote.`episode_id`
		AND opnote.event_type_id = 4 AND opnote.created_date < ev.created_date
        JOIN ophinbiometry_imported_events ON ev.id = ophinbiometry_imported_events.`event_id`
	JOIN et_ophinbiometry_selection ON ev.id = et_ophinbiometry_selection.`event_id` AND et_ophinbiometry_selection.eye_id = 2 OR et_ophinbiometry_selection.eye_id = 3
	JOIN ophinbiometry_calculation_formula ON et_ophinbiometry_selection.`formula_id_left` = ophinbiometry_calculation_formula.id
        WHERE et.id = 37
        AND ms.deleted = 0
        AND ev.deleted = 0
);
                                               
SET @cmd = CONCAT("(SELECT 'EpisodeId', 'Eye', 'AxialLength', 'BiometryAScanId', 'BiometryKeratometerId', 'BiometryFormulaId', 'K1PreOperative', 'K2PreOperative', 'AxisK1', 'AxisK2', 'ACDepth', 'SNR')
		  UNION 
                      (SELECT * FROM tmp_biometry
                    INTO OUTFILE '", @file,
		  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
		  "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

DROP TEMPORARY TABLE IF EXISTS tmp_biometry;
                        
END;

                        -- EpisodeIOP --
                        
DROP PROCEDURE IF EXISTS get_episode_iop;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_iop(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @file = CONCAT(dir, '/episode_iop_', @time_now, '.csv');
CREATE VIEW nod_episode_iop AS SELECT e.id AS EpisodeId,
                               (SELECT CASE WHEN oipv.eye_id = 1 THEN 'L' WHEN oipv.eye_id = 2 THEN 'R' END) AS Eye,
                                NULL AS TYPE,
                                9 AS GlaucomaMedicationStatusId,
                                oipvr.value AS VALUE
                            FROM episode e
                            JOIN `event` ev ON ev.episode_id = e.id
                            JOIN event_type et ON et.id = ev.event_type_id
                            JOIN et_ophciexamination_intraocularpressure etoi ON etoi.event_id = ev.id
                            JOIN ophciexamination_intraocularpressure_value oipv ON oipv.element_id = etoi.id
                            JOIN ophciexamination_intraocularpressure_reading oipvr ON oipv.`reading_id` = oipvr.id
                            WHERE et.name = 'Examination'
                            GROUP BY e.id;

SET @cmd = CONCAT("(SELECT 'EpisodeId', 'Eye', 'Type', 'GlaucomaMedicationStatusId', 'Value')
		  UNION (SELECT * FROM nod_episode_iop INTO OUTFILE '", @file,
		  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
		  "  LINES TERMINATED BY '\r\n')");                        

PREPARE statement FROM @cmd;
EXECUTE statement;
    
DROP VIEW nod_episode_iop;
END;

                        -- EpisodePreOpAssessment --
                        
DROP PROCEDURE IF EXISTS get_EpisodePreOpAssessment;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_EpisodePreOpAssessment(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
CREATE VIEW nod_episode_preop_assessment AS SELECT e.id AS EpisodeId,
									(SELECT CASE WHEN pl.eye_id = 1 THEN 'L' WHEN pl.eye_id = 2 THEN 'R' WHEN pl.eye_id = 3 THEN 'B' END) AS Eye,
									(SELECT CASE WHEN pr.risk_id IS NULL THEN 0 WHEN pr.risk_id = 1 THEN 1 ELSE 0 END) AS IsAbleToLieFlat,
									(SELECT CASE WHEN pr.risk_id IS NULL THEN 0 WHEN pr.risk_id = 4 THEN 1 ELSE 0 END) AS IsInabilityToCooperate
									FROM episode e
									LEFT JOIN `event` ev ON ev.episode_id = e.id
									JOIN et_ophtroperationnote_procedurelist pl ON pl.event_id = ev.id
									LEFT JOIN patient_risk_assignment pr ON pr.patient_id = e.patient_id
									GROUP BY e.id;

SET @file = CONCAT(dir, '/episode_preop_assessment_', @time_now, '.csv');
SET @cmd = CONCAT("(SELECT 'EpisodeId', 'Eye', 'IsAbleToLieFlat', 'IsInabilityToCooperate')
	UNION (SELECT EpisodeId, Eye, IsAbleToLieFlat, IsInabilityToCooperate FROM nod_episode_preop_assessment INTO OUTFILE '", @file,
	"' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
	"  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

DROP VIEW nod_episode_preop_assessment;

END;

                        -- EpisodeRefraction --
                        
DROP PROCEDURE IF EXISTS get_episode_refraction;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_refraction(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
CREATE VIEW nod_episode_refraction AS (SELECT e.episode_id AS EpisodeId, r.left_sphere AS Sphere, r.left_cylinder AS Cylinder, r.left_axis AS Axis, '' AS RefractionTypeId, '' AS ReadingAdd,
							  (SELECT CASE WHEN r.eye_id = 1 THEN 'L' END) AS Eye
							  FROM `event` e
							  INNER JOIN et_ophciexamination_refraction r ON r.event_id = e.id
							  WHERE r.eye_id = 1)
							  UNION
							  (SELECT e.episode_id AS EpisodeId, r.right_sphere AS Sphere, r.right_cylinder AS Cylinder, r.right_axis AS Axis, '' AS RefractionTypeId, '' AS ReadingAdd,
							  (SELECT CASE WHEN r.eye_id = 2 THEN 'R' END) AS Eye
							  FROM `event` e
							  INNER JOIN et_ophciexamination_refraction r ON r.event_id = e.id
							  WHERE r.eye_id = 2);


SET @file = CONCAT(dir, '/episode_refraction_', @time_now, '.csv');
SET @cmd = CONCAT("(SELECT 'EpisodeId', 'Eye', 'RefractionTypeId', 'Sphere', 'Cylinder', 'Axis', 'ReadingAdd')
		  UNION (SELECT EpisodeId, Eye, RefractionTypeId, Sphere, Cylinder, Axis, ReadingAdd FROM  nod_episode_refraction INTO OUTFILE '", @file,
		  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
		  "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

DROP VIEW nod_episode_refraction;

END;

                        -- EpisodeVisualAcuity --
                        
DROP PROCEDURE IF EXISTS get_episode_visual_acuity;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_visual_acuity(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
CREATE VIEW nod_episode_visual_acuity AS SELECT e.episode_id AS EpisodeId, v.unit_id AS NotationRecordedId,
								   (SELECT CASE WHEN v.eye_id = 1 THEN 'L' WHEN v.eye_id = 2 THEN 'R' END) AS Eye,
								   (SELECT MAX(VALUE) FROM ophciexamination_visualacuity_reading r JOIN et_ophciexamination_visualacuity va ON va.id = r.element_id WHERE r.element_id = v.id AND va.unit_id = (SELECT id FROM ophciexamination_visual_acuity_unit WHERE NAME = 'logMAR single-letter')) AS BestMeasure,
								   #(SELECT value from ophciexamination_visualacuity_reading r JOIN et_ophciexamination_visualacuity va ON va.id = r.element_id WHERE r.element_id = v.id AND method_id = 1) AS Unaided,
								   NULL AS Unaided, NULL AS Pinhole, NULL AS BestCorrected
								 FROM `event` e
								 INNER JOIN et_ophciexamination_visualacuity v ON v.event_id = e.id
								 WHERE v.eye_id != 3;
#TODO: Unaided, Pinhole, BestCorrected

SET @file = CONCAT(dir, '/episode_visual_acuity_', @time_now, '.csv');
SET @cmd = CONCAT("(SELECT 'EpisodeId', 'Eye', 'NotationRecordedId', 'BestMeasure', 'Unaided', 'Pinhole', 'BestCorrected')
   UNION (SELECT EpisodeId, Eye, NotationRecordedId, BestMeasure, Unaided, Pinhole, BestCorrected FROM nod_episode_visual_acuity INTO OUTFILE '", @file,
   "'  FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
   "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

DROP VIEW nod_episode_visual_acuity;

END;
                        
                        -- EpisodeOperation --
                        
DROP PROCEDURE IF EXISTS get_episode_operation;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_operation(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
                        
CREATE TEMPORARY TABLE tmp_doctor_grade (
	`code` INT(10) UNSIGNED NOT NULL,
	`desc` VARCHAR(100)
);

INSERT INTO tmp_doctor_grade (`code`, `desc`)
VALUES
(0, 'Consultant'),
(1, 'Locum Consultant'),
(2, 'corneal burn'),
(3, 'Associate Specialist'),
(4, 'Fellow'),
(5, 'Registrar'),
(6, 'Staff Grade'),
(7, 'Trust Doctor'),
(8, 'Senior House Officer'),
(9, 'Specialty trainee (year 1)'),
(10, 'Specialty trainee (year 2)'),
(11, 'Specialty trainee (year 3)'),
(12, 'Specialty trainee (year 4)'),
(13, 'Specialty trainee (year 5)'),
(14, 'Specialty trainee (year 6)'),
(15, 'Specialty trainee (year 7)'),
(16, 'Foundation Year 1 Doctor'),
(17, 'Foundation Year 2 Doctor'),
(18, 'GP with a special interest in ophthalmology'),
(19, 'Community ophthalmologist'),
(20, 'Anaesthetist'),
(21, 'Orthoptist'),
(22, 'Optometrist'),
(23, 'Clinical nurse specialist'),
(24, 'Nurse'),
(25, 'Health Care Assistant'),
(26, 'Ophthalmic Technician'),
(27, 'Surgical Care Practitioner'),
(28, 'Clinical Assistant'),
(29, 'RG1'),
(30, 'RG2'),
(31, 'ODP'),
(32, 'Administration staff'),
(33, 'Other');
                        
CREATE TABLE nod_episode_operation AS SELECT e.id AS OperationId, e.episode_id AS EpisodeId, e.event_date AS ListedDate, 
    s.surgeon_id AS SurgeonId, 
    (
        SELECT `code`
        FROM tmp_doctor_grade, doctor_grade
        WHERE user.`doctor_grade_id` = doctor_grade.id AND doctor_grade.`grade` = tmp_doctor_grade.desc
    ) AS SurgeonGradeId
            FROM `event` e
            JOIN event_type evt ON evt.id = e.event_type_id
            LEFT JOIN et_ophtroperationnote_surgeon s ON s.event_id = e.id
            INNER JOIN `user` ON s.`surgeon_id` = `user`.`id`
            WHERE evt.name = 'Operation booking';

SET @file = CONCAT(dir, '/episode_operation_', @time_now, '.csv');
                        
SET @cmd = CONCAT("
                (SELECT 'OperationId', 'EpisodeId', 'ListedDate', 'SurgeonId', 'SurgeonGradeId')
                UNION
                (SELECT * FROM nod_episode_operation INTO OUTFILE '", @file,
		  "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
		  "  LINES TERMINATED BY '\r\n')");
                        
PREPARE statement FROM @cmd;
EXECUTE statement;

DROP TABLE nod_episode_operation;
DROP TEMPORARY TABLE tmp_doctor_grade;
                        
END;

                        -- EpisodeOperationComplication --

DROP PROCEDURE IF EXISTS get_episode_operation_complication;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_operation_complication(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @file = CONCAT(dir, '/episode_operation_complication_', @time_now, '.csv');

CREATE TEMPORARY TABLE tmp_complication_type (
	`code` INT(10) UNSIGNED NOT NULL,
	`name` VARCHAR(100)
);

INSERT INTO tmp_complication_type (`code`, `name`)
VALUES
    (0, 'None'),
    (1, 'choroidal / suprachoroidal haemorrhage'),
    (2, 'corneal burn'),
    (3, 'corneal epithelial abrasion'),
    (4, 'corneal oedema'),
    (5, 'endothelial damage / Descemet\'s tear'),
    (6, 'epithelial abrasion'),
    (7, 'hyphaema'),
    (8, 'IOL into the vitreous'),
    (9, 'iris prolapse'),
    (10, 'iris trauma'),
    (11, 'lens exchange required / other IOL problems'),
    (12, 'nuclear / epinuclear fragment into vitreous'),
    (13, 'PC rupture - no vitreous loss'),
    (14, 'PC rupture - vitreous loss'),
    (15, 'phaco burn / wound problems'),
    (16, 'suprachoroidal haemorrhage'),
    (17, 'torn iris / damage from the phaco'),
    (18, 'vitreous loss'),
    (19, 'vitreous to the section at end of surgery'),
    (20, 'zonule dialysis'),
    (21, 'zonule rupture - vitreous loss'),
    (25, 'Not recorded'),
    (999, 'other');
                        
SET @cmd = CONCAT(" (SELECT 'OperationId', 'Eye', 'ComplicationTypeId' )
                    UNION
                    (SELECT
                        event.id AS OperationId, 
                        (SELECT CASE 
                            WHEN et_ophtroperationnote_procedurelist.eye_id = 1 THEN 'L' 
                            WHEN et_ophtroperationnote_procedurelist.eye_id = 2 THEN 'R' 
                            WHEN et_ophtroperationnote_procedurelist.eye_id = 3 THEN 'B' 
                            END
                        ) AS Eye,
                        (SELECT `code` 
                            FROM tmp_complication_type 
                            WHERE tmp_complication_type.`name` = ophtroperationnote_cataract_complications.name
                        ) AS ComplicationTypeId
                    FROM ophtroperationnote_cataract_complication
                    INNER JOIN `et_ophtroperationnote_cataract` ON `ophtroperationnote_cataract_complication`.cataract_id = et_ophtroperationnote_cataract.id
                    INNER JOIN ophtroperationnote_cataract_complications ON ophtroperationnote_cataract_complication.`complication_id` = ophtroperationnote_cataract_complications.`id`
                    INNER JOIN `event` ON  et_ophtroperationnote_cataract.`event_id` = `event`.id
                    INNER JOIN et_ophtroperationnote_procedurelist ON event.id = et_ophtroperationnote_procedurelist.event_id 
                INTO OUTFILE '", @file,
                "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
                "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

DROP TEMPORARY TABLE IF EXISTS tmp_complication_type;

END;
                        
                        -- EpisodeOperationIndication --

DROP PROCEDURE IF EXISTS get_episode_operation_indication;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_operation_indication(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @file = CONCAT(dir, '/episode_operation_indication_', @time_now, '.csv');
SET @cmd = CONCAT(" (SELECT 'OperationId', 'Eye', 'ComplicationTypeId' )
                    UNION
                    ( 
                            SELECT pl.`event_id` AS OperationId, (SELECT CASE WHEN pl.eye_id = 1 THEN 'L' WHEN pl.eye_id = 2 THEN 'R' END) AS Eye,
                            (
                                    SELECT IF(	pl.`booking_event_id`,
                                                    d.`disorder_id`, 
                                                    (
                                                            SELECT disorder_id
                                                            FROM episode
                                                            WHERE e.`episode_id` = episode.id
                                                    )
                                            ) 
                            ) AS IndicationId
                            FROM `event` e
                            JOIN event_type evt ON evt.id = e.event_type_id
                            JOIN et_ophtroperationnote_procedurelist pl ON e.id = pl.event_id
                            JOIN `et_ophtroperationbooking_diagnosis` d ON e.id = d.`event_id`
                            WHERE evt.name = 'Operation booking'
                        INTO OUTFILE '", @file,
                    "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
                    "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;
                        
END;

                        -- EpisodeOperationAnaesthesia --

DROP PROCEDURE IF EXISTS get_episode_operation_anaesthesia;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_operation_anaesthesia(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @file = CONCAT(dir, '/episode_operation_anaesthesia_', @time_now, '.csv');

DROP TEMPORARY TABLE IF EXISTS tmp_anesthesia_type;

CREATE TEMPORARY TABLE tmp_anesthesia_type(
	`id` INT(10) UNSIGNED NOT NULL,
	`name` VARCHAR(50),
	`code` VARCHAR(50),
	`nod_code` VARCHAR(50),
	`nod_desc` VARCHAR(50)
);

INSERT INTO tmp_anesthesia_type(`id`, `name`, `code`, `nod_code`, `nod_desc`)
VALUE
(1, 'Topical', 'Top', 4, 'Topical anaesthesia alone'),
(2, 'LAC',     'LAC', 2, 'Local anaesthesia alone'),
(3, 'LA',      'LA',  2, 'Local anaesthesia alone'),
(4, 'LAS',     'LAS', 2, 'Local anaesthesia alone'),
(5, 'GA',      'GA',  1, 'General anaesthesia alone');
                        
SET @cmd = CONCAT(" (SELECT 'OperationId', 'AnaesthesiaTypeId')
                    UNION
                    (
                        SELECT event_id AS OperationId, 
                        (SELECT `nod_code` FROM tmp_anesthesia_type WHERE at.`name` = `name`) AS AnaesthesiaTypeId
                        FROM et_ophtroperationnote_anaesthetic a 
                        JOIN `anaesthetic_type` `at` ON a.`anaesthetic_type_id` = at.`id`
                        
                        INTO OUTFILE '", @file,
                        "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
                        "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;
    
DROP TEMPORARY TABLE tmp_anesthesia_type;
END;

                        -- EpisodeTreatment --

DROP PROCEDURE IF EXISTS get_episode_treatment;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_treatment(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @file = CONCAT(dir, '/episode_treatment_', @time_now, '.csv');
SET @cmd = CONCAT(" (SELECT 'TreatmentId', 'OperationId', 'Eye', 'TreatmentTypeId' )
                    UNION
                    (SELECT pa.id AS TreatmentId, pl.`event_id` AS OperationId, (SELECT CASE WHEN pl.eye_id = 1 THEN 'L' WHEN pl.eye_id = 2 THEN 'R' END) AS Eye, 
                           proc.`snomed_code` AS TreatmentTyeId
                    FROM ophtroperationnote_procedurelist_procedure_assignment pa
                    JOIN et_ophtroperationnote_procedurelist pl ON pa.`procedurelist_id` = pl.id
                    JOIN proc ON pa.`proc_id` = proc.`id`
                INTO OUTFILE '", @file,
                "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
                "  LINES TERMINATED BY '\r\n')");
                    
PREPARE statement FROM @cmd;
EXECUTE statement;
    
END;

                        -- EpisodeTreatmentCataract --

DROP PROCEDURE IF EXISTS get_episode_treatment_cataract;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_treatment_cataract(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
DROP TEMPORARY TABLE IF EXISTS tmp_iol_positions;
CREATE TEMPORARY TABLE tmp_iol_positions (
	`nodcode` INT(10) UNSIGNED NOT NULL,
	`term` VARCHAR(100)
);

INSERT INTO tmp_iol_positions (`nodcode`, `term`)
VALUES
    (0, 'None'),
    (8, 'In the bag'),
    (9, 'Partly in the bag'),
    (6, 'In the sulcus'),
    (2, 'Anterior chamber'),
    (12, 'Sutured posterior chamber'),
    (5, 'Iris fixated'),
    (13, 'Other');

SET @file = CONCAT(dir, '/episode_treatment_cataract', @time_now, '.csv');
SET @cmd = CONCAT(" (SELECT 'TreatmentId', 'IsFirstEye', 'PreparationDrugId', 'IncisionSiteId', 'IncisionLengthId', 'IncisionPlanesId', 'IncisionMerideanId', 'PupilSizeId', 'IOLPositionId', 'IOLModelId', 'IOLPower', 'PredictedPostOperativeRefraction', 'WoundClosureId')
                    UNION
                    (select pa.id AS TreatmentId,
					IFNULL((select
						IF(eye.`name` = 'First eye', 1, 0)
						from ophciexamination_cataractsurgicalmanagement_eye eye
						join et_ophciexamination_cataractsurgicalmanagement mng on eye.id = mng.eye_id
						join `event` as exam_event on mng.event_id = exam_event.id
						where exam_event.episode_id = episode.id
						and exam_event.event_date <= op_event.event_date
						order by exam_event.event_date desc
						limit 1
					), 1) as IsFirstEye,
					'' as PreparationDrugId,
					if(inc_site.`name` = 'Limbal', 5, IF(inc_site.`name` = 'Scleral', 8, 4)) as IncisionSiteId,
					cataract.length as IncisionLengthId,
					4 as IncisionPlanesId, #unkown
					cataract.meridian as IncisionMerideanId,
					if(cataract.pupil_size = 'Small', 1, if(cataract.pupil_size = 'Medium', 2, if(cataract.pupil_size = 'Large', 3, ''))) as PupilSizeId,
					tmp_iol_positions.nodcode as IolPositionId,
					ophtroperationnote_cataract_iol_type.`name` as IOLModelId,
					cataract.iol_power as IOLPower,
					cataract.predicted_refraction as PredictedPostOperativeRefraction,
					'' as WoundClosureId
					FROM ophtroperationnote_procedurelist_procedure_assignment pa
					JOIN et_ophtroperationnote_procedurelist ON pa.procedurelist_id = et_ophtroperationnote_procedurelist.id
					join `event` as op_event on et_ophtroperationnote_procedurelist.event_id = op_event.id
					join episode on op_event.episode_id = episode.id
					join et_ophtroperationnote_cataract as cataract on op_event.id = cataract.event_id
					join ophtroperationnote_cataract_incision_site as inc_site on cataract.incision_site_id = inc_site.id
					join ophtroperationnote_cataract_iol_position iol_pos on cataract.iol_position_id = iol_pos.id
					join tmp_iol_positions on iol_pos.`name` = tmp_iol_positions.term
					join ophtroperationnote_cataract_iol_type on cataract.iol_type_id = ophtroperationnote_cataract_iol_type.id
                INTO OUTFILE '", @file,
                "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
                "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

END;

                        -- EpisodeOperationCoPathology --

DROP PROCEDURE IF EXISTS get_episode_operation_pathology;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_operation_pathology(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @file = CONCAT(dir, '/episode_operation_pathology_', @time_now, '.csv');

DROP TEMPORARY TABLE IF EXISTS tmp_pathology_type;
CREATE TEMPORARY TABLE tmp_pathology_type (
	`nodcode` INT(10) UNSIGNED NOT NULL,
	`term` VARCHAR(100)
);

INSERT INTO tmp_pathology_type (`nodcode`, `term`)
VALUES
    (0, 'None'),
    (1, 'Age related macular degeneration'),
    (2, 'Amblyopia'),
    (4, 'Diabetic retinopathy'),
    (5, 'Glaucoma'),
    (7, 'Degenerative progressive high myopia'),
    (8, 'Ocular Hypertension'),
    (11, 'Stickler Syndrome'),
    (12, 'Uveitis'),
    (13, 'Pseudoexfoliation'),
    (13, 'phacodonesis'),
    (18, 'macular hole'),
    (19, 'epiretinal membrane'),
    (20, 'retinal detachment ');

SET @cmd = CONCAT(" (SELECT 'OperationId', 'Eye', 'ComplicationTypeId' )
                    UNION
                    (SELECT
                        op_event.id AS OperationId,
                        (SELECT
                                CASE
                                        WHEN (proc_list.eye_id = 3) THEN 'B'
                                        WHEN (proc_list.eye_id = 2) THEN 'R'
                                        WHEN (proc_list.eye_id = 1) THEN 'L'
                                    END
                            ) AS Eye,
                        IF(element_type.`name` = 'Trabeculectomy', 25,23)  AS ComplicationTypeId
                    FROM
                        `event` AS op_event
                            JOIN
                        `episode` ON op_event.episode_id = episode.id
                            JOIN
                        `event` AS previous_op_event ON previous_op_event.episode_id = episode.id
                            AND previous_op_event.event_type_id = (SELECT id FROM event_type WHERE `name` = 'Operation Note')
                            AND previous_op_event.created_date <= op_event.created_date
                            JOIN
                        `et_ophtroperationnote_procedurelist` AS proc_list ON proc_list.event_id = previous_op_event.id
                            JOIN
                        `ophtroperationnote_procedurelist_procedure_assignment` AS proc_list_asgn ON proc_list_asgn.procedurelist_id = proc_list.id
                            JOIN
                        proc ON proc_list_asgn.proc_id = proc.id
                            JOIN
                        ophtroperationnote_procedure_element ON ophtroperationnote_procedure_element.procedure_id = proc.id
                            JOIN
                        element_type ON ophtroperationnote_procedure_element.element_type_id = element_type.id
                    WHERE
                        element_type.`name` in ('Vitrectomy', 'Trabeculectomy')
                        AND op_event.event_type_id = (SELECT id FROM event_type WHERE `name` = 'Operation Note'))
                    UNION
                    (SELECT
                    op_event.id AS OperationId,
                    (SELECT
                            CASE
                                    WHEN (proc_list.eye_id = 3) THEN 'B'
                                    WHEN (proc_list.eye_id = 2) THEN 'R'
                                    WHEN (proc_list.eye_id = 1) THEN 'L'
                                END
                        ) AS Eye,
                    21 AS ComplicationTypeId
                    FROM
                        `event` AS op_event
                            JOIN
                        `episode` ON op_event.episode_id = episode.id
                            JOIN
                        `event` AS previous_op_event ON previous_op_event.episode_id = episode.id
                            AND previous_op_event.event_type_id = (SELECT id FROM event_type WHERE `name` = 'Operation Note')
                            AND previous_op_event.created_date <= op_event.created_date
                            JOIN `et_ophtroperationnote_procedurelist` AS proc_list ON proc_list.event_id = previous_op_event.id
                            JOIN `ophtroperationnote_procedurelist_procedure_assignment` AS proc_list_asgn ON proc_list_asgn.procedurelist_id = proc_list.id
                            JOIN proc ON proc_list_asgn.proc_id = proc.id
                            JOIN procedure_benefit ON procedure_benefit.proc_id = proc.id
                            JOIN benefit ON procedure_benefit.benefit_id = benefit.id
                    WHERE
                        benefit.`name` = 'to prevent retinal detachment'
                        AND op_event.event_type_id = (SELECT id FROM event_type WHERE `name` = 'Operation Note'))
                    UNION
                    (SELECT op_event.id AS OperationId,
                    (SELECT CASE
                        WHEN (left_cortical_id = 4 OR left_nuclear_id = 4) AND (right_cortical_id = 4 OR right_nuclear_id = 4) THEN 'B'
                        WHEN (left_cortical_id = 4 OR left_nuclear_id = 4) THEN 'L'
                        WHEN (right_cortical_id = 4 OR right_nuclear_id = 4) THEN 'R'
                        END
                    ) AS Eye,
                    14 AS ComplicationTypeId
                    From et_ophciexamination_anteriorsegment
                    JOIN `event` AS exam_event on et_ophciexamination_anteriorsegment.event_id = exam_event.id
                    JOIN `episode` ON exam_event.episode_id = episode.id
                    JOIN `event` AS op_event
                    ON episode.id = op_event.episode_id
                    AND op_event.event_type_id = (select id from event_type where `name` = 'Operation Note')
                    AND op_event.created_date >= exam_event.created_date
                    HAVING Eye IS NOT NULL)
                    UNION
                    (SELECT
                        event.id AS OperationId,
                        (SELECT CASE
                            WHEN secondary_diagnosis.eye_id = 1 THEN 'L'
                            WHEN secondary_diagnosis.eye_id = 2 THEN 'R'
                            WHEN secondary_diagnosis.eye_id = 3 THEN 'B'
                            END
                        ) AS Eye,
                        tmp_pathology_type.nodcode as ComplicationTypeId
                    FROM `event`
                    JOIN `episode` ON `event`.episode_id = episode.id
                    JOIN secondary_diagnosis ON episode.`patient_id` = secondary_diagnosis.`patient_id`
                    JOIN `disorder` ON  secondary_diagnosis.`disorder_id` = `disorder`.id
                    JOIN tmp_pathology_type on LOWER(disorder.term) = LOWER(tmp_pathology_type.term)
                    WHERE event_type_id = (SELECT id from event_type where `name` = 'Operation Note')
                INTO OUTFILE '", @file,
                "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
                "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

DROP TEMPORARY TABLE IF EXISTS tmp_pathology_type;

END;
                        -- EpisodePostOpComplication --

DROP PROCEDURE IF EXISTS get_episode_post_op_complication;
CREATE DEFINER=`root`@`localhost` PROCEDURE get_episode_post_op_complication(IN dir VARCHAR(255))
BEGIN
SET @time_now = UNIX_TIMESTAMP(NOW());
SET @file = CONCAT(dir, '/episode_post_op_complication_', @time_now, '.csv');
SET @cmd = CONCAT(" (SELECT 'EpisodeId', 'OperationId', 'Eye', 'ComplicationTypeId')
                    UNION
                    (SELECT 
                        episode.id AS EpisodeId, 
                        ophciexamination_postop_et_complications.`operation_note_id` AS OperationId, 
                        (SELECT CASE WHEN ophciexamination_postop_et_complications.`eye_id` = 1 THEN 'L' WHEN ophciexamination_postop_et_complications.`eye_id` = 2 THEN 'R' END ),
                        ophciexamination_postop_complications.`code` AS ComplicationTypeId
                        FROM episode
                        JOIN `event` ON episode.id = `event`.`episode_id`
                        JOIN et_ophciexamination_postop_complications ON `event`.id = et_ophciexamination_postop_complications.`event_id`
                        JOIN ophciexamination_postop_et_complications ON et_ophciexamination_postop_complications.id = ophciexamination_postop_et_complications.`element_id`
                        JOIN ophciexamination_postop_complications ON ophciexamination_postop_et_complications.`complication_id` = ophciexamination_postop_complications.id
                    
                INTO OUTFILE '", @file,
                "' FIELDS ENCLOSED BY '\"' TERMINATED BY ';'",
                "  LINES TERMINATED BY '\r\n')");

PREPARE statement FROM @cmd;
EXECUTE statement;

END;

                        -- Run Export Generation --
                        
DROP PROCEDURE IF EXISTS run_nod_export_generator;
CREATE DEFINER=`root`@`localhost` PROCEDURE run_nod_export_generator(IN dir VARCHAR(255))
BEGIN

#Drop temporary tables and view
#If the script dies than temp tables will not be deleted and tables cannot be re-created on the next run
DROP TEMPORARY TABLE IF EXISTS tmp_doctor_grade;
DROP TEMPORARY TABLE IF EXISTS temp_patients;
DROP TABLE IF EXISTS temp_patient_cvi_status;
DROP TABLE IF EXISTS temp_episodes_diagnosis;
DROP TABLE IF EXISTS temp_episode_diabetic_diagnosis;
DROP TEMPORARY TABLE IF EXISTS tmp_biometry;
DROP VIEW IF EXISTS nod_episode_drug;
DROP VIEW IF EXISTS nod_episode_iop;
DROP VIEW IF EXISTS nod_episode_preop_assessment;
DROP VIEW IF EXISTS nod_episode_refraction;
DROP VIEW IF EXISTS nod_episode_visual_acuity;
DROP TABLE IF EXISTS nod_episode_operation;
DROP TEMPORARY TABLE IF EXISTS tmp_complication_type;
DROP TEMPORARY TABLE IF EXISTS tmp_anesthesia_type;

CALL get_surgeons(dir);
CALL get_patients(dir);
CALL get_patient_cvi_status(dir);
CALL get_nod_episodes(dir);
CALL get_episodes_diagnosis(dir);
CALL get_episode_diabetic_diagnosis(dir);
CALL get_episode_drug(dir);
CALL get_episode_biometry(dir);
CALL get_episode_iop(dir);
CALL get_EpisodePreOpAssessment(dir);
CALL get_episode_refraction(dir);
CALL get_episode_visual_acuity(dir);
CALL get_episode_operation(dir);
CALL get_episode_operation_complication(dir);
CALL get_episode_operation_indication(dir);
CALL get_episode_operation_pathology(dir);
CALL get_episode_operation_anaesthesia(dir);
CALL get_episode_treatment(dir);                        
CALL get_episode_treatment_cataract(dir);
CALL get_episode_post_op_complication(dir);

END;

EOL;

        $this->execute($storedProcedure);

        return true;
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationnote_cataract', 'pupil_size');
        $this->dropColumn('et_ophtroperationnote_cataract_version', 'pupil_size');

        $storedProcedure = <<<EOL

DROP PROCEDURE IF EXISTS get_surgeons;
DROP PROCEDURE IF EXISTS get_patients;
DROP PROCEDURE IF EXISTS get_patient_cvi_status;
DROP PROCEDURE IF EXISTS get_nod_episodes;
DROP PROCEDURE IF EXISTS get_episodes_diagnosis;
DROP PROCEDURE IF EXISTS get_episode_diabetic_diagnosis;
DROP PROCEDURE IF EXISTS get_episode_drug;
DROP PROCEDURE IF EXISTS get_episode_biometry;
DROP PROCEDURE IF EXISTS get_episode_iop;
DROP PROCEDURE IF EXISTS get_EpisodePreOpAssessment;
DROP PROCEDURE IF EXISTS get_episode_refraction;
DROP PROCEDURE IF EXISTS get_episode_visual_acuity;
DROP PROCEDURE IF EXISTS get_episode_operation;
DROP PROCEDURE IF EXISTS get_episode_operation_complication;
DROP PROCEDURE IF EXISTS get_episode_operation_pathology;
DROP PROCEDURE IF EXISTS run_nod_export_generator;
DROP PROCEDURE IF EXSIST get_episode_post_op_complication;
EOL;
        $this->execute($storedProcedure);

        return true;
    }
}
