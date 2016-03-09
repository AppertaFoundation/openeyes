<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class NodExportController extends BaseController
{
	/**
	 * @var string the default layout for the views
	 */
	public $layout='//layouts/main';
        
        protected $export_path;
        protected $zip_name;

    protected $institution_code = "000001";

	private	$startDate;
	private	$endDate;

	private $allEpisodeIds;
	
	public function accessRules()
	{
		// TODO need to add NOD Export RBAC rule here!! now we restrict to admin only
		return array(
			array('allow',
				'roles' => array('admin'),
			),
		);
	}
	public function beforeAction($action)
	{
		return parent::beforeAction($action);
	}
        
	public function init()
	{
		$date = date('YmdHi');
		$this->export_path = realpath(dirname(__FILE__) . '/..') . '/runtime/nod-export/' . $this->institution_code . '/' . $date;
		$this->zip_name = $this->institution_code . '_' . $date . '_NOD_Export';
		
		if (!file_exists($this->export_path)) {
			mkdir($this->export_path, 0777, true);
		}

		$this->startDate = Yii::app()->request->getParam("startDate", '2015-01-01');
		$this->endDate =  Yii::app()->request->getParam("endDate", '2016-03-08');
		
		parent::init();
	}
	
	public function actionGetAllEpisodeId()
        {
		// TODO: we need to call all extraction functions from here!
		$this->allEpisodeIds = array_merge($this->getEpisodePostOpComplication(), $this->actionEpisodeOperationCoPathology(), $this->actionGetEpisodeDiagnosis);
		
		print_r($this->allEpisodeIds);
	}
	
        /**
         * Save CSV file and returns episodeIDs if $episodeIdField isset
         * 
         * @param string $dataQuery SQL query
         * @param string $filename
         * @param string $episodeIdField
         * @return null|array
         */
	private function saveCSVfile($dataQuery, $filename){
		
		$data = Yii::app()->db->createCommand($dataQuery)->queryAll();
		$csv = $this->array2Csv($data);
          
        file_put_contents($this->export_path . '/'.$filename.'.csv' , $csv);
		return $data;
	}
	
	private function getIdArray($data, $IdField){
		if($IdField && $data){
			foreach($data as $row){
				$objectIds[] = $row[$IdField];
			}
			return $objectIds;
		}
	}
	
	public function actionIndex()
	{
		// TODO: need to create views!!!
		$this->render('index');
	}
        
        /**
         * This table will contain the only person identifiable data (surgeon’s GMC number or national code
         * ) stored on the RCOphth NOD. This information will be used to match a surgeon to their 
         * own data on the RCOphth NOD website and in the prospective projects enable thematching of a surgeons’ 
         * record if they move between centres. This was not done with the ‘legacy’ data already in
         *  NOD and therefore at present we do not have the ability to identify individual surgeons.
         */
        public function actionGetSurgeons()
        {
            $create_tmp_doctor_grade_sql = <<<EOL
CREATE TABLE tmp_doctor_grade (
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
EOL;
            
                Yii::app()->db->createCommand($create_tmp_doctor_grade_sql)->execute();
        
$dataQuery = <<<EOL
    SELECT id as Surgeonid, IFNULL(registration_code, 'NULL') as GMCnumber, IFNULL(title, 'NULL') as Title, IFNULL(first_name, 'NULL') as FirstName,
    (
        SELECT `code` 
        FROM tmp_doctor_grade, doctor_grade
        WHERE user.`doctor_grade_id` = doctor_grade.id AND doctor_grade.`grade` = tmp_doctor_grade.desc
    ) AS CurrentGradeId
FROM user 
WHERE is_surgeon = 1 AND active = 1
EOL;
            
            $this->saveCSVfile($dataQuery, 'Surgeons');
            
            // cleanup
            Yii::app()->db->createCommand("DROP TABLE tmp_doctor_grade;")->execute();
        }
        
        /**
         * The extraction of patient data is psuedoanonymised. All tables prefixed with “Patient” link back to the 
         * “Patient” table via the ‘PatientId’ variable. Each patient on the RCOphth NOD will have one row in the “Patient” table.
         */
        public function actionGetPatients()
        {
            $dateWhere = "";
            
            $dataQuery = "SELECT id as PatientId, IFNULL( (SELECT CASE WHEN gender='F' THEN 2 WHEN gender='M' THEN 1 ELSE 9 END) , '') as GenderId, "
                                  . "IFNULL(ethnic_group_id, 'NULL') as EthnicityId, "
                                  . "IFNULL(dob, 'NULL') as DateOfBirth, "
                                  . "IFNULL(date_of_death, '') as DateOfDeath, '' as IMDScore, '' as IsPrivate "
                                . "FROM patient"
                                . " " . $dateWhere;
            
            $this->saveCSVfile($dataQuery, 'Patients');
        }
        
        
        public function actionGetPatientCviStatus()
        {
            $dataQuery = "SELECT 
                                episode.`patient_id` AS PatientId,
                                cvi_status_date AS `Date`,
                                (SELECT CASE WHEN DAYNAME(DATE) IS NULL THEN 1 END) AS IsDateApprox, 
                                (SELECT CASE WHEN cvi_status_id=4 THEN 1 END) AS IsCVIBlind, 
                                (SELECT CASE WHEN cvi_status_id=3 THEN 1 END) AS IsCVIPartial
                        FROM episode
                        JOIN patient ON episode.`patient_id` = patient.id
                        JOIN patient_oph_info ON patient.id = patient_oph_info.`patient_id`
                        WHERE episode.`patient_id` IN ( SELECT id FROM patient )";
            
            $this->saveCSVfile($dataQuery, 'PatientCviStatus');
        }
        
        public function actionGetEpisode()
        {
            $dataQuery = "SELECT patient_id, id, start_date FROM episode";
            $this->saveCSVfile($dataQuery, 'Episodes');
        }
        
        public function actionGetEpisodeDiagnosis()
        {
            $dataQuery = "SELECT 
                        id AS EpisodeId, 
                        (SELECT CASE WHEN eye_id = 1 THEN 'L' WHEN eye_id = 2 THEN 'R' WHEN eye_id = 3 THEN 'B' ELSE 'N' END ) AS Eye, 
                        last_modified_date AS `Date`,
                        (
                                SELECT (
                                        IFNULL(
                                                (SELECT last_modified_user_id FROM episode_version WHERE ep.id=id ORDER BY last_modified_date ASC LIMIT 1), 
                                                (SELECT last_modified_user_id FROM episode WHERE id = ep.id)
                                        )
                                )
                        ) AS SurgeonId,
                        (
                                SELECT service_subspecialty_assignment_id FROM firm WHERE id = ep.`firm_id`

                        ) AS ConditionId,
                        disorder_id AS DiagnosisTermId
                FROM episode ep";
                
                $data = $this->saveCSVfile($dataQuery, 'EpisodePostOpComplication');
				
		return $this->getIdArray($data, 'EpisodeId');
        }
        
        public function actionGetEpisodeDiabeticDiagnosis()
        {
            $disorder = Disorder::model()->findByPk(Disorder::SNOMED_DIABETES);
            $disorder_ids = implode(",", $disorder->descendentIds());
            
            $dataQuery = <<<EOL
                    SELECT 	
                    e.id AS EpisodeId, 
                    1 AS IsDiabetic,

                    (
                            SELECT CASE 
                                    WHEN d.id IN (23045005,28032008,46635009,190368000,190369008,190371008,190372001,199229001,237618001,290002008,313435000,314771006,314893005,
                                                               314894004,401110002,420270002,420486006,420514000,420789003,420825003,420868002,420918009,421165007,421305000,421365002,421468001,
                                                               421893009,421920002,422228004,422297002,425159004,425442003,426907004,427571000,428896009,11530004) THEN 1
                                    WHEN d.id IN (9859006,44054006,81531005,190388001,190389009,190390000,190392008,199230006,237599002,237604008,237614004,237650006,
                                                               313436004,314772004,314902007,314903002,314904008,359642000,395204000,420279001,420414003,420436000,420715001,420756003,
                                                               421326000,421631007,421707005,421750000,421779007,421847006,421986006,422014003,422034002,422099009,422166005,423263001,
                                                               424989000,427027005,427134009,428007007,359638003) THEN 2
                                    WHEN d.id IN (237626009,237627000,11687002,46894009,71546005,75022004,420491007,420738003,420989005,421223006,421389009,421443003,
                                                               422155003,76751001,199223000,199225007,199227004) THEN 3
                                    WHEN d.id IN (237619009,359939009) THEN 4
                                    WHEN d.id IN (14052004,28453007) THEN 5
                                    WHEN d.id IN (2751001,4307007,4783006,5368009,5969009,8801005,33559001,42954008,49817004,51002006,57886004,59079001,70694009,
                                                              73211009,75524006,75682002,111552007,111554008,127012008,190329007,190330002,190331003,190406000,190407009,190410002,
                                                              190411003,190412005,190416008,190447002,199226008,199228009,199231005,237600004,237601000,237603002,237611007,237612000,
                                                              237616002,237617006,237620003,238981002,275918005,276560009,408540003,413183008,420422005,420683009,421256007,421895002,
                                                              422088007,422183001,422275004,426705001,426875007,427089005,441628001,91352004,399144008) THEN 9
                                    END 

                    ) AS DiabetesTypeId,
                    "" AS DiabetesRegimeId,
                    IFNULL((DATEDIFF(`date`, dob)/365 ), "") AS AgeAtDiagnosis
            FROM secondary_diagnosis s
            JOIN disorder d ON d.id = s.disorder_id
            JOIN episode e ON e.patient_id = s.patient_id
            JOIN patient p ON e.patient_id = p.id
            WHERE d.id IN ( $disorder_ids )
EOL;
            
            $data = $this->saveCSVfile($dataQuery, 'EpisodeDiabeticDiagnosis' );
		
            return $this->getIdArray($data, 'EpisodeId');
        }
        
        public function actionGetEpisodeDrug()
        {
            $dataQuery = <<<EOL
                    SELECT e.id AS EpisodeId , 
                    (SELECT CASE WHEN option_id = 1 THEN 'L' WHEN option_id = 2 THEN 'R' WHEN option_id = 3 THEN 'B'  ELSE 'N' END) AS Eye,
                    (SELECT CASE WHEN m.drug_id IS NOT NULL THEN (SELECT NAME FROM drug WHERE id = m.drug_id) WHEN m.drug_id IS NULL THEN ''
                                    WHEN m.medication_drug_id IS NOT NULL THEN (SELECT NAME FROM medication_drug WHERE id = m.drug_id) WHEN m.medication_drug_id IS NULL THEN '' END) AS DrugId,
                    dr.id AS DrugRouteId,
                     (SELECT CASE WHEN m.start_date IS NULL THEN '' ELSE m.start_date END) AS StartDate,
                    (SELECT CASE WHEN m.end_date IS NULL THEN '' ELSE m.end_date END) AS StopDate,
                    (SELECT CASE WHEN opi.prescription_id IS NOT NULL THEN 1 ELSE 0 END ) AS IsAddedByPrescription, 



                    (SELECT CASE WHEN opi.continue_by_gp IS NULL THEN 0 ELSE opi.continue_by_gp END) AS IsContinueIndefinitely,
                     (SELECT CASE WHEN DAYNAME(m.start_date) IS NULL THEN 1 ELSE 0 END) AS IsStartDateApprox

                  FROM episode e
                  INNER JOIN medication m ON e.patient_id = m.patient_id
                  LEFT JOIN drug_route dr ON dr.id = m.route_id
                  LEFT JOIN `event` ev ON ev.episode_id = e.id
                  LEFT JOIN event_type evt ON evt.id = ev.event_type_id
                  LEFT JOIN et_ophdrprescription_details etp ON etp.event_id = ev.id
                  LEFT JOIN ophdrprescription_item opi ON etp.id = opi.prescription_id
                  GROUP BY episode_id;
EOL;
            
            $data = $this->saveCSVfile($dataQuery, 'GetEpisodeDrug' );
		
            return $this->getIdArray($data, 'EpisodeId');
        }
        
        public function actionGetEpisodeBiometry()
        {
            
            
            $prepareQuery = "DROP TABLE IF EXISTS tmp_biometry_formula;
                            CREATE TABLE tmp_biometry_formula (
                                    `code` INT(10) UNSIGNED NOT NULL,
                                    `desc` VARCHAR(100)
                            )";

            $dataQuery = <<<EOL

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
         )
                    
EOL;
            
        }
        
        
        
        
        
        
        protected function array2Csv(array $data)
        {
            if (count($data) == 0) {
                return null;
            }
            ob_start();
            $df = fopen("php://output", 'w');
            fputcsv($df, array_keys(reset($data)));
            foreach ($data as $row) {
                fputcsv($df, $row);
            }
            fclose($df);
            return ob_get_clean();
        }
      
	private function getDateWhere($tablename)
        {
		if($this->startDate != ""){
			$dateWhereStart = $tablename.".last_modified_date >= '".$this->startDate."'";
		}
		if($this->endDate != ""){
			$dateWhereEnd = $tablename.".last_modified_date <= '".$this->endDate."'";
		}
		
		$dateWhere = "";
		if(isset($dateWhereStart) && isset($dateWhereEnd)){
			$dateWhere = "AND ".$dateWhereStart." AND ".$dateWhereEnd;
		}else if(isset($dateWhereStart)){
			$dateWhere = "AND ".$dateWhereStart;
		}else if(isset($dateWhereEnd)){
			$dateWhere = "AND ".$dateWhereEnd;
		}
		
		return $dateWhere;
	}
	
       private function getEpisodePostOpComplication()
       {
		
		$dateWhere = $this->getDateWhere('et_ophciexamination_postop_complications');
		
		$dataQuery = "SELECT 
                        episode.id AS EpisodeId, 
                        ophciexamination_postop_et_complications.`operation_note_id` AS OperationId, 
                        (SELECT CASE WHEN ophciexamination_postop_et_complications.`eye_id` = 1 THEN 'L' WHEN ophciexamination_postop_et_complications.`eye_id` = 2 THEN 'R' END ) AS Eye,
                        ophciexamination_postop_complications.`code` AS ComplicationTypeId
                        FROM episode
                        JOIN `event` ON episode.id = `event`.`episode_id`
                        JOIN et_ophciexamination_postop_complications ON `event`.id = et_ophciexamination_postop_complications.`event_id`
                        JOIN ophciexamination_postop_et_complications ON et_ophciexamination_postop_complications.id = ophciexamination_postop_et_complications.`element_id`
                        JOIN ophciexamination_postop_complications ON ophciexamination_postop_et_complications.`complication_id` = ophciexamination_postop_complications.id 
						WHERE 1=1 ".$dateWhere;
		
		$data = $this->saveCSVfile($dataQuery, 'EpisodePostOpComplication' );
		
		return $this->getIdArray($data, 'EpisodeId');
	}
	
	public function actionEpisodeOperationCoPathology()
       {
		$tempTableQuery = <<<EOL
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
EOL;
		
        Yii::app()->db->createCommand($tempTableQuery)->execute();
		
		$dataQuery = "(SELECT
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
                        AND op_event.event_type_id = (SELECT id FROM event_type WHERE `name` = 'Operation Note') ".$this->getDateWhere('op_event').")
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
                        AND op_event.event_type_id = (SELECT id FROM event_type WHERE `name` = 'Operation Note') ".$this->getDateWhere('op_event')." )
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
                    WHERE 1=1 ".$this->getDateWhere('et_ophciexamination_anteriorsegment')."
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
                    WHERE event_type_id = (SELECT id from event_type where `name` = 'Operation Note') ".$this->getDateWhere('event').")";
		
		$data = $this->saveCSVfile($dataQuery, 'EpisodeOperationCoPathology' );
		
		return $this->getIdArray($data, 'OperationId');
	}
	
	public function actionEpisodeTreatmentCataract(){
		$tempTableQuery = <<<EOL
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
EOL;
		
	
        Yii::app()->db->createCommand($tempTableQuery)->execute();		
		
		$dataQuery = "
                    select pa.id AS TreatmentId,
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
					WHERE 1=1 ".$this->getDateWhere('pa');
					
		$data = $this->saveCSVfile($dataQuery, 'EpisodeTreatmentCataract');
		
		//TODO: need to select episodeIds here!
		return $this->getIdArray($data, 'TreatmentId');
		
	}
	
	public function actionEpisodeTreatment(){
		$dataQuery = "SELECT pa.id AS TreatmentId, pl.`event_id` AS OperationId, (SELECT CASE WHEN pl.eye_id = 1 THEN 'L' WHEN pl.eye_id = 2 THEN 'R' END) AS Eye, 
                           proc.`snomed_code` AS TreatmentTyeId
                    FROM ophtroperationnote_procedurelist_procedure_assignment pa
                    JOIN et_ophtroperationnote_procedurelist pl ON pa.`procedurelist_id` = pl.id 
					WHERE 1=1 ".$this->getDateWhere('pa');
					
		$data = $this->saveCSVfile($dataQuery, 'EpisodeTreatment');		
		//TODO: need to select episodeIds here!		
		return $this->getIdArray($data, 'TreatmentId');
		
	}
	
	public function actionEpisodeOperationAnaesthesia(){
		$tempTableQuery = <<<EOL
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
EOL;

        Yii::app()->db->createCommand($tempTableQuery)->execute();		

		$dataQuery = "SELECT event_id AS OperationId, 
                        (SELECT `nod_code` FROM tmp_anesthesia_type WHERE at.`name` = `name`) AS AnaesthesiaTypeId
                        FROM et_ophtroperationnote_anaesthetic a 
                        JOIN `anaesthetic_type` `at` ON a.`anaesthetic_type_id` = at.`id`";
						
		$data = $this->saveCSVfile($dataQuery, 'EpisodeOperationAnaesthesia');	
		
		return $this->getIdArray($data, 'OperationId');
	}

}