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
// this extract's execution time is more than the default 500sec
// for 5yrs time period it can last more than 30min
ini_set('max_execution_time', 3600);

class NodExportController extends BaseController
{
    /**
     * @var string the default layout for the views
     */
    public $layout = '//layouts/main';

    protected $exportPath;
    protected $zipName;

    protected $institutionCode;

    private $startDate = '';
    private $endDate = '';
    
    // Refactoring : 
    /**
     * This number will be appended after the tmp tables so the
     * two or more extract running at the same time can use different tmp tables
     * @var int 
     */
    private $extract_identifier;
    

    public function accessRules()
    {
        return array(
            array('allow',
                'roles' => array('NOD Export'),
            ),
        );
    }

    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    public function init()
    {
        $this->institutionCode = Yii::app()->params['institution_code'];
        $date = date('YmdHi');
        $this->exportPath = realpath(dirname(__FILE__) . '/..') . '/runtime/nod-export/' . $this->institutionCode . '/' . $date;
        $this->zipName = $this->institutionCode . '_' . $date . '_NOD_Export.zip';

        if (!file_exists($this->exportPath)) {
            mkdir($this->exportPath, 0777, true);
        }
        
        $startDate = Yii::app()->request->getParam("date_from", '');
        $endDate =  Yii::app()->request->getParam("date_to", '');
        
        $startDateTime = null;
        $endDateTime = null;
        
        if($startDate){
            $startDateTime = new DateTime($startDate);
        }
        
        if($endDate){
            $endDateTime = new DateTime($endDate);
        }

        // if start date is greater than end date we exchange the two dates
        if(($startDateTime instanceof DateTime && $endDateTime instanceof DateTime) && $endDateTime < $startDateTime){
            $tempDate = $endDateTime;
            $endDateTime = $startDateTime;
            $startDateTime = $tempDate;
            $tempDate = null;
        }
        
        if($startDate){
            $this->startDate = $startDateTime->format('Y-m-d');
        }
        
        if($endDate){
            $this->endDate = $endDateTime->format('Y-m-d');
        }
        
        // Refactoring : generate number from hour-minute-sec
        // this number will be appended to the name of tmp tables
        // tmp tables will be normal DB tables instead of real TEMPORARY tables because in some queries
        // we need to refer the tmp table (like sub-select) two or more times - and in MySQL a tmp table can be referred only once in a query
        // (this prevents error if someone starts 2 extract)
        $this->extract_identifier = date('His');

        parent::init();
    }
    
    public function actionIndex()
    {
        // TODO: need to create views!!!
        $this->render('//nodexport/index');
    }
    
    
     /**
     * Generates CSV and zip files then sends to the browser 
     */
    public function actionGenerate()
    {

        $this->generateExport();

        $this->createZipFile();

        if (file_exists($this->exportPath . '/' . $this->zipName)) {
            Yii::app()->getRequest()->sendFile($this->zipName, file_get_contents($this->exportPath . '/' . $this->zipName));
        } else {
        }
    }
    
    /**
     * Generates the CSV files
     */
    public function generateExport()
    {

        $query = $this->createAllTempTables();
        $query .= $this->populateAllTempTables();

        Yii::app()->db->createCommand($query)->execute();

        $this->getEpisodeDiabeticDiagnosis();
        $this->getEpisodeDrug();
        $this->getEpisodeBiometry();
        $this->getEpisodePostOpComplication();
        $this->getEpisodePreOpAssessment();
        $this->getEpisodeIOP();
        $this->getEpisodeVisualAcuity();
        $this->getEpisodeRefraction();
        $this->getEpisodeOperationCoPathology();
        $this->getEpisodeOperationAnaesthesia();
        $this->getEpisodeOperationIndication();
        $this->getEpisodeOperationComplication();
        $this->getEpisodeOperation();
        $this->getEpisodeTreatmentCataract();
        $this->getEpisodeTreatment();

        $this->getEpisodeDiagnosis();
        $this->getEpisode();
        
        $this->getSurgeons();
        
        $this->getPatientCviStatus();
        $this->getPatients();
        $this->clearAllTempTables();

    }

    /**
     * Save CSV file and returns episodeIDs if $episodeIdField isset
     *
     * @param string $dataQuery SQL query
     * @param string $filename
     * @param string $episodeIdField
     * @return null|array
     */
    private function saveCSVfile($dataQuery, $filename, $dataFormatter = null, $IdField = null)
    {
        $resultIds = array();
        $offset = 0;
        $chunk = 200000;

        if (!isset($dataQuery['query'])) {
            throw new Exception('Query not found: array key "query" not exist');
        }

        while (true) {
            $runQuery = $dataQuery['query']. " LIMIT ".$chunk." OFFSET ".$offset.";";
            $dataCmd = Yii::app()->db->createCommand($runQuery);

            $data = $dataCmd->queryAll();

            if($offset == 0 && (!count($data)>0)){
                file_put_contents($this->exportPath . '/' . $filename . '.csv', implode(',', $dataQuery['header']), FILE_APPEND);
            }

            if(count($data) > 0)
            {
                $csv = $this->array2Csv($data, ($offset == 0 && isset($dataQuery['header'])) ? $dataQuery['header'] : null, $dataFormatter);

                file_put_contents($this->exportPath . '/' . $filename . '.csv', $csv, FILE_APPEND);

                if($IdField) {
                    foreach ($data as $d) {
                        $resultIds[] = $d[$IdField];
                    }
                }

                $offset+=$chunk;
                unset($data);
            } else {
                break;
            }
        }
        return $resultIds;
    }



    private function createAllTempTables()
    {
        // DROP all tables if exsist before createing them
        $this->clearAllTempTables();
        
        $query = '';

        $query .= $this->createTmpRcoNodMainEventEpisodes();
        $query .= $this->createTmpRcoNodPatients();
        $query .= $this->createTmpRcoNodPatientCVIStatus();
        $query .= $this->createTmpRcoNodEpisodePreOpAssessment();
        $query .= $this->createTmpRcoNodEpisodeRefraction();
        $query .= $this->createTmpRcoNodEpisodeDrug();
        $query .= $this->createTmpRcoNodEpisodeIOP();
        $query .= $this->createTmpRcoNodEpisodeBiometry();
        $query .= $this->createTmpRcoNodSurgeon();
        $query .= $this->createTmpRcoNodEpisodeDiabeticDiagnosis();
        $query .= $this->createTmpRcoNodPostOpComplication();
        $query .= $this->createTmpRcoNodEpisodeOperationCoPathology();
        $query .= $this->createTmpRcoNodEpisodeOperation();
        $query .= $this->createTmpRcoNodEpisodeTreatment();
        $query .= $this->createTmpRcoNodEpisodeTreatmentCataract();
        $query .= $this->createTmpRcoNodEpisodeOperationAnesthesia();
        $query .= $this->createTmpRcoNodEpisodeOperationIndication();
        $query .= $this->createTmpRcoNodEpisodeOperationComplication();
        $query .= $this->createTmpRcoNodEpisodeVisualAcuity();
        $query .= $this->createTmpRcoNodEpisodeDiagnosis();
        
        $createTempQuery = <<<EOL

			DROP TABLE IF EXISTS tmp_episode_ids;
			CREATE TABLE tmp_episode_ids(
				id  int(10) UNSIGNED NOT NULL UNIQUE,
				KEY `tmp_episode_ids_id` (`id`)
			);
			
			DROP TEMPORARY TABLE IF EXISTS tmp_operation_ids;
			
			CREATE TEMPORARY TABLE tmp_operation_ids(
				id  int(10) UNSIGNED NOT NULL UNIQUE,
				KEY `tmp_operation_ids_id` (`id`)
			);
			
			DROP TABLE IF EXISTS tmp_treatment_ids;
			
			CREATE TABLE tmp_treatment_ids(
				id  int(10) UNSIGNED NOT NULL UNIQUE,
				KEY `tmp_treatment_ids_id` (`id`)
			);
			

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

			DROP TEMPORARY TABLE IF EXISTS tmp_anesthesia_type;

			CREATE TEMPORARY TABLE tmp_anesthesia_type(
				`id` INT(10) UNSIGNED NOT NULL,
				`name` VARCHAR(50),
				`code` VARCHAR(50),
				`nod_code` VARCHAR(50),
				`nod_desc` VARCHAR(50),
				KEY `tmp_anesthesia_type_name` (`name`)
			);

			INSERT INTO tmp_anesthesia_type(`id`, `name`, `code`, `nod_code`, `nod_desc`)
			VALUE
			(1, 'Topical', 'Top', 4, 'Topical anaesthesia alone'),
			(2, 'LAC',     'LAC', 2, 'Local anaesthesia alone'),
			(3, 'LA',      'LA',  2, 'Local anaesthesia alone'),
			(4, 'LAS',     'LAS', 2, 'Local anaesthesia alone'),
			(5, 'GA',      'GA',  1, 'General anaesthesia alone');
			
					
		DROP TABLE IF EXISTS tmp_complication_type;

		CREATE TABLE tmp_complication_type (
			`code` INT(10) UNSIGNED NOT NULL,
			`name` VARCHAR(100)
		);

		INSERT INTO tmp_complication_type (`code`, `name`)
		VALUES
			(0, 'None'),
			(1, 'choroidal / suprachoroidal haemorrhage'),
			(2, 'corneal burn'),
			(3, 'corneal epithelial abrasion'),
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
                
                        DROP TEMPORARY TABLE IF EXISTS tmp_complication;

                        CREATE TEMPORARY TABLE tmp_complication (
                                `oe_id` INT(10) UNSIGNED NOT NULL,
                                `oe_desc` VARCHAR(100),
                                `nod_id` INT(10) UNSIGNED NOT NULL,
                                `nod_desc` VARCHAR(100)
                        );
                
#complication mapping is not finished yet

                        INSERT INTO tmp_complication (`oe_id`, `oe_desc`, `nod_id`, `nod_desc` )
                        VALUES
                        (1, 'Eyelid haemorrage/bruising', 2, 'Eyelid haemorrhage / bruising'),
                        (2, 'Conjunctivital chemosis', 1, 'Conjunctival chemosis'),
                        (3, 'Retro bulbar / peribulbar haemorrage', 8, 'Retrobulbar / peribulbar haemorrhage'),
                        (4, 'Globe/optic nerve penetration', 4, 'Globe / optic nerve perforation'),
                        
                        (6, 'Patient pain - Mild', 5, 'Patient discomfort / pain mild;'),
                        (7, 'Patient pain - Moderate', 6, 'Patient discomfort / pain moderate;'),
                        (8, 'Patient pain - Severe', 7, 'Patient discomfort / pain severe;'),
                        (9, 'Systemic problems', 10, 'Systemic problems (bradycardia / hypotension / apnoea etc.)'),
                        (10, 'Operation abandoned due to complication', 11, 'Operation cancelled due to complication'),
                        (11, 'None', 0, 'None');
                        
                        -- (5, 'Inadequate akinesia', 0, ''),
                        
                        -- (0, '', 9, 'Sub-conjunctival haemorrhage'),
                        -- (0, '', 3, 'Excessive eye movement'),
                        -- (0, '', 12, 'Other'),
                        -- (0, '', 99, 'Not recorded');

#anaesthetic delivery mapping is not finished yet
                    
                        
                        DROP TEMPORARY TABLE IF EXISTS tmp_anaesthetic_delivery;

                        CREATE TEMPORARY TABLE tmp_anaesthetic_delivery (
                                `oe_id` INT(10) UNSIGNED NOT NULL,
                                `oe_desc` VARCHAR(100),
                                `nod_id` INT(10) UNSIGNED NOT NULL,
                                `nod_desc` VARCHAR(100)
                        );

                        INSERT INTO tmp_anaesthetic_delivery (`oe_id`, `oe_desc`, `nod_id`, `nod_desc` )
                        VALUES
                        (1, 'Retrobulbar', 2, 'Retrobulbar'),
                        (2, 'Peribulbar', 1, 'Peribulbar'),	
                        (3, 'Subtenons', 3, 'Sub-Tenon');
                        -- (4, 'Subconjunctival', 0, ''),
                        -- (5, 'Topical', 0, ''),
                        -- (6, 'Topical and intracameral', 0, ''),	
                        -- (6, 'Other', 0, '');
                        
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
                        
                        DROP TABLE IF EXISTS tmp_episode_diagnosis;
                        CREATE TABLE tmp_episode_diagnosis (
                           `oe_subspecialty_name` VARCHAR(50),
                           `rco_condition_name` VARCHAR(50),
                           `oe_subspecialty_id` INT(10) UNSIGNED NOT NULL,
                           `rco_condition_id` INT(10) UNSIGNED NOT NULL
                        );
                
                        INSERT INTO tmp_episode_diagnosis (`oe_subspecialty_name`, `rco_condition_name`, `oe_subspecialty_id`, `rco_condition_id`)
                        VALUES
                        ('Adnexal', 'Lacrimal Orbital & Socket', 2, 12),
                        ('Cataract', 'Cataract', 4, 2),
                        ('Cornea', 'Corneal', 5, 4),
                        ('External', 'External', 6, 20),
                        ('Refractive', 'Refractive', 13, 17),
                        ('Accident & Emergency', 'Eye Casualty', 1, 7),
                        ('General Ophthalmology', 'General', 12, 10),
                        ('Glaucoma', 'Glaucoma', 7, 11),
                        ('Medical Retinal', 'Medical retina', 8, 13),
                        ('Uveitis', 'Medical retina', 15, 13),
                        ('Oncology', 'Ocular Oncology', 10, 21),
                        ('Neuro-ophthalmology', 'Neuroophthalmology', 9, 14),
                        ('Strabismus', 'Strabismus & Paediatric', 14, 18),
                        ('Paediatrics', 'Strabismus & Paediatric', 11, 18),
                        ('Vitreoretinal', 'Vitreoretinal', 16, 19);
                

                    DROP TABLE IF EXISTS tmp_episode_drug_route;
                
                    CREATE TABLE tmp_episode_drug_route (
                        `oe_route_id` INT(10) UNSIGNED,
                        `oe_route_name` VARCHAR(50), 
                        `oe_option_id` INT(10) UNSIGNED DEFAULT NULL, 
                        `oe_option_name` VARCHAR(50), 
                        `nod_id` INT(10) UNSIGNED DEFAULT NULL, 
                        `nod_name` VARCHAR(50),
                        KEY `tmp_episode_drug_route_oe_route_id` (`oe_route_id`)
                    ); 
                
                    INSERT INTO `tmp_episode_drug_route` ( `oe_route_id`, `oe_route_name`, `oe_option_id`, `oe_option_name`, `nod_id`, `nod_name` )
                        VALUES
                         (1, 'Eye', 1, 'Left', 1, 'Left eye'), 
                         (1, 'Eye', 2, 'Right', 2, 'Right eye'), 
                         (1, 'Eye', 3, 'Both', 4, 'Both eyes'), 
                         (2, 'IM', NULL, "", 7, 'Intramuscular injection'), 
                         (3, 'Inhalation', NULL, "", 6, 'Inhaled'), 
                         (4, 'Intracameral', NULL, "", 5,'Intracameral'), 
                         (5, 'Intradermal', NULL, "", 99, 'Other'), 
                         (6, 'Intravitreal', NULL, "", 99, 'Other'), 
                         (7, 'IV', NULL, "", 9, 'Intravenously'), 
                         (8, 'Nose', NULL, "", 8, 'Intranasally'), 
                         (9, 'Ocular muscle', NULL, "", 7, 'Intramuscular injection'), 
                         (10, 'PO', NULL, "", 12, 'Orally'), 
                         (11, 'PR', NULL, "", 15, 'Per rectum'), 
                         (12, 'PV', NULL, "", 99, 'Other'), 
                         (13, 'Sub-Conj', NULL, "", 18, 'Subconjunctival'), 
                         (14, 'Sub-lingual', NULL, "", 19, 'Sub-lingual'), 
                         (15, 'Subcutaneous', NULL, "", 17, 'Subcutaneously'), 
                         (16, 'To Nose', NULL, "", 8, 'Intranasally'), 
                         (17, 'To skin', NULL, "", 24, 'Trans-cutaneous'), 
                         (18, 'Topical', NULL, "", 23, 'Topically'), 
                         (19, 'n/a', NULL, "", 99, 'Other'), 
                         (20, 'Other', NULL, "", 99, 'Other');
     
EOL;

        return $query . $createTempQuery;
    }
    
    // Refactoring :
    /**
     * This function will call the functions one by one to populate each tmp tables belongs to a csv file
     */
    private function populateAllTempTables()
    {
        $query = '';
        
        $query .= $this->populateTmpRcoNodMainEventEpisodes();
        $query .= $this->populateTmpRcoNodPatients();
        $query .= $this->populateTmpRcoNodEpisodePreOpAssessment();
        $query .= $this->populateTmpRcoNodPatientCVIStatus();
        $query .= $this->populateTmpRcoNodEpisodeRefraction();
        $query .= $this->populateTmpRcoNodEpisodeDrug();
        $query .= $this->populateTmpRcoNodEpisodeIOP();
        $query .= $this->populateTmpRcoNodEpisodeBiometry();
        $query .= $this->populateTmpRcoNodSurgeon();
        $query .= $this->populateTmpRcoNodEpisodeDiabeticDiagnosis();
        $query .= $this->populateTmpRcoNodPostOpComplication();
        $query .= $this->populateTmpRcoNodEpisodeOperationCoPathology();
        $query .= $this->populateTmpRcoNodEpisodeOperation();
        $query .= $this->populateTmpRcoNodEpisodeTreatment();
        $query .= $this->populateTmpRcoNodEpisodeTreatmentCataract();
        $query .= $this->populateTmpRcoNodEpisodeOperationAnesthesia();
        $query .= $this->populateTmpRcoNodEpisodeOperationIndication();
        $query .= $this->populateTmpRcoNodEpisodeOperationComplication();
        $query .= $this->populateTmpRcoNodEpisodeDiagnosis();
        $query .= $this->populateTmpRcoNodEpisodeVisualAcuity();

        return $query;
    }
    
    private function clearAllTempTables()
    {
        $cleanQuery = <<<EOL
                
                DROP TABLE IF EXISTS tmp_rco_nod_main_event_episodes_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_patients_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodePreOpAssessment_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_PatientCVIStatus_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeRefraction_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeDrug_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeIOP_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeBiometry_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_Surgeon_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeDiabeticDiagnosis_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationCoPathology_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodePostOpComplication_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperation_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeTreatment_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeTreatmentCataract_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationAnesthesia_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationComplication_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationIndication_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeTreatment_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeVisualAcuity_{$this->extract_identifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeDiagnoses_{$this->extract_identifier};
                
                DROP TEMPORARY TABLE IF EXISTS tmp_complication;
                DROP TEMPORARY TABLE IF EXISTS tmp_anesthesia_type;
                DROP TEMPORARY TABLE IF EXISTS tmp_anaesthetic_delivery;
                DROP TEMPORARY TABLE IF EXISTS tmp_iol_positions;
                DROP TEMPORARY TABLE IF EXISTS tmp_pathology_type;
                DROP TEMPORARY TABLE IF EXISTS tmp_operation_ids;
                DROP TABLE IF EXISTS tmp_complication_type;
                DROP TABLE IF EXISTS tmp_biometry_formula;
                DROP TABLE IF EXISTS tmp_episode_diagnosis;
                DROP TABLE IF EXISTS tmp_episode_drug_route;
                DROP TABLE IF EXISTS tmp_episode_ids;
                DROP TABLE IF EXISTS tmp_treatment_ids;

EOL;

        Yii::app()->db->createCommand($cleanQuery)->execute();
    }
    
    
    
    
    
    /********** Surgeon **********/

    private function createTmpRcoNodSurgeon()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_Surgeon_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_Surgeon_{$this->extract_identifier} (
                    Surgeonid INT(10) NOT NULL,
                    GMCnumber VARCHAR(250) DEFAULT NULL,
                    Title VARCHAR(40) NOT NULL,
                    FirstName VARCHAR(40) NOT NULL,
                    CurrentGradeId VARCHAR(10) DEFAULT NULL,
                    PRIMARY KEY (Surgeonid)
            );
EOL;
        return $query;
    }

    /**
     * This table will contain the only person identifiable data (surgeon’s GMC number or national code
     * ) stored on the RCOphth NOD. This information will be used to match a surgeon to their
     * own data on the RCOphth NOD website and in the prospective projects enable thematching of a surgeons’
     * record if they move between centres. This was not done with the ‘legacy’ data already in
     *  NOD and therefore at present we do not have the ability to identify individual surgeons.
     */
    
    private function populateTmpRcoNodSurgeon()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_Surgeon_{$this->extract_identifier} (
                Surgeonid,
                GMCnumber,
                Title,
                FirstName,
                CurrentGradeId
            )
            SELECT 
                id AS Surgeonid, 
                IFNULL(registration_code, '') AS GMCnumber, 
                IFNULL(title, '') AS Title,
                IFNULL(first_name, '') AS FirstName,
                IFNULL(user.doctor_grade_id, '')  AS CurrentGradeId
            FROM user
            WHERE is_surgeon = 1 AND active = 1;
EOL;
        #Yii::app()->db->createCommand($query)->execute();
        return $query;
    }

    private function getSurgeons()
    {

        $query = <<<EOL
                SELECT * 
                FROM tmp_rco_nod_Surgeon_{$this->extract_identifier}
EOL;

        $dataQuery = array(
            'query' => $query,
            'header' => array('Surgeonid', 'GMCnumber', 'Title', 'FirstName', 'CurrentGradeId'),
        );

        $this->saveCSVfile($dataQuery, 'Surgeon');

    }
    
    /********** end of Surgeon **********/

    
    
    
    
    /********** EpisodeDiabeticDiagnosis **********/
    
    private function createTmpRcoNodEpisodeDiabeticDiagnosis()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeDiabeticDiagnosis_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeDiabeticDiagnosis_{$this->extract_identifier} (
                oe_event_id int(10) NOT NULL,
                IsDiabetic char(1) DEFAULT NULL COMMENT '0 = no, 1 = yes',
                DiabetesTypeId  VARCHAR(10),
                DiabetesRegimeId VARCHAR(11), /* empty string */
                AgeAtDiagnosis VARCHAR(3) /* empty string or DATE*/
            );
EOL;
        return $query;
    }

    private function populateTmpRcoNodEpisodeDiabeticDiagnosis()
    {
        $disorder = Disorder::model()->findByPk(Disorder::SNOMED_DIABETES);
        $disorder_ids = implode(",", $disorder->descendentIds());

        $query = <<<EOL
                    INSERT INTO tmp_rco_nod_EpisodeDiabeticDiagnosis_{$this->extract_identifier} (
                        oe_event_id,
                        IsDiabetic,
                        DiabetesTypeId,
                        DiabetesRegimeId,
                        AgeAtDiagnosis
                    )
                    SELECT
                    c.oe_event_id,
                    (SELECT CASE WHEN d.id IN ( $disorder_ids ) THEN 1 ELSE 0 END) AS IsDiabetic,
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
                                    ELSE ""
                                    END

                    ) AS DiabetesTypeId,
                    "" AS DiabetesRegimeId,
                    (
			SELECT CASE WHEN DATE != ''
                                    THEN IFNULL((DATE_FORMAT(`date`, '%Y') - DATE_FORMAT(p.dob, '%Y') - (DATE_FORMAT(`date`, '00-%m-%d') < DATE_FORMAT(p.dob, '00-%m-%d'))), "") 
                                    ELSE ""
				END
                    )
		    AS AgeAtDiagnosis
            FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
            JOIN patient p ON c.patient_id = p.id
            JOIN secondary_diagnosis s ON s.patient_id = p.id
            JOIN disorder d ON d.id = s.disorder_id;
EOL;
        return $query;
    }
    
    private function getEpisodeDiabeticDiagnosis()
    {
       $query = <<<EOL
                SELECT c.nod_episode_id as EpisodeId, d.IsDiabetic, d.DiabetesTypeId, d.DiabetesRegimeId, d.AgeAtDiagnosis
                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                JOIN tmp_rco_nod_EpisodeDiabeticDiagnosis_{$this->extract_identifier} d ON c.oe_event_id = d.oe_event_id
EOL;


        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'IsDiabetic', 'DiabetesTypeId', 'DiabetesRegimeId', 'AgeAtDiagnosis'),
        );

        $output = $this->saveCSVfile($dataQuery, 'EpisodeDiabeticDiagnosis', null, 'EpisodeId');
        
        return $output;
    }
    
    /********** end of EpisodeDiabeticDiagnosis **********/



    
    
    /********** Patient **********/
    
    /**
     * Create tmp_rco_nod_patients_{$this->extract_identifier} table
     */
    private function createTmpRcoNodPatients()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_patients_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_patients_{$this->extract_identifier} (
                PatientId INT(10) NOT NULL,
                GenderId TINYINT(1) NOT NULL,
                EthnicityId VARCHAR(2) NOT NULL,
                DateOfBirth DATE NOT NULL,
                DateOfDeath DATE DEFAULT NULL,
                IMDScore FLOAT DEFAULT NULL,
                IsPrivate TINYINT(1) DEFAULT NULL,
                PRIMARY KEY (PatientId)
            );
EOL;
        return $query;
    }
    
    /**
     *  Load nod_patients data (using previously identified patients in control table)
     */
    private function populateTmpRcoNodPatients()
    {
        $query = <<<EOL
                INSERT INTO tmp_rco_nod_patients_{$this->extract_identifier} (
                    PatientId,
                    GenderId,
                    EthnicityId,
                    DateOfBirth,
                    DateOfDeath,
                    IMDScore,
                    IsPrivate
                  ) 
                  SELECT
                          p.id AS PatientId,
                          (SELECT CASE WHEN gender='F' THEN 2 WHEN gender='M' THEN 1 ELSE 9 END) AS GenderId,
                          IFNULL((SELECT ethnic_group.code FROM ethnic_group WHERE ethnic_group.id = p.ethnic_group_id), 'Z') AS EthnicityId,
                          IFNULL( DATE_ADD(dob, INTERVAL ROUND((RAND() * (3-1))+1) MONTH) , '') AS DateOfBirth,
                          IFNULL(DATE(date_of_death), NULL) AS DateOfDeath,
                          NULL AS IMDScore,
                          NULL AS IsPrivate
                  FROM patient p
                  WHERE p.id IN
                    (
                        SELECT DISTINCT(c.patient_id)
                        FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                    ); 
EOL;
        return $query;
    }
    
    /**
     * The extraction of patient data is psuedoanonymised. All tables prefixed with “Patient” link back to the
     * “Patient” table via the ‘PatientId’ variable. Each patient on the RCOphth NOD will have one row in the “Patient” table.
     */
    private function getPatients()
    {
        $query = <<<EOL
                SELECT * 
                FROM tmp_rco_nod_patients_{$this->extract_identifier}
EOL;
        
        $dataQuery = array(
            'query' => $query,
            'header' => array('PatientId', 'GenderId', 'EthnicityId', 'DateOfBirth', 'DateOfDeath', 'IMDScore', 'IsPrivate'),
        );

        $this->saveCSVfile($dataQuery, 'Patient');
    }
    
    /********** end of Patient **********/
    
    
    
    
    
    /********** PatientCVIStatus **********/
    
    private function createTmpRcoNodPatientCVIStatus()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_PatientCVIStatus_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_PatientCVIStatus_{$this->extract_identifier} (
                PatientId int(10) NOT NULL,
                date date NOT NULL,
                IsDateApprox tinyint(1) DEFAULT NULL,
                IsCVIBlind tinyint(1) DEFAULT NULL,
                IsCVIPartial tinyint(1) DEFAULT NULL,
                UNIQUE KEY PatientId (PatientId,date)
            );
EOL;
        return $query;
    }
    
    /**
     * Populate Patient CVI Status
     */
    private function populateTmpRcoNodPatientCVIStatus()
    {
        $query = <<<EOL
                INSERT INTO tmp_rco_nod_PatientCVIStatus_{$this->extract_identifier} (
                        PatientId,
                        date,
                        IsDateApprox,
                        IsCVIBlind,
                        IsCVIPartial )
                SELECT
                poi.patient_id AS PatientId,
                poi.cvi_status_date AS `Date`,
                (SELECT CASE WHEN DAYNAME(DATE) IS NULL THEN 1 ELSE 0 END) AS IsDateApprox,
                (SELECT CASE WHEN poi.cvi_status_id=4 THEN 1 ELSE 0 END) AS IsCVIBlind,
                (SELECT CASE WHEN poi.cvi_status_id=3 THEN 1 ELSE 0 END) AS IsCVIPartial
                FROM patient_oph_info poi

                /* Restriction: patients in control events */
                WHERE poi.patient_id IN ( SELECT c.patient_id FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier}  c );
EOL;
        return $query;
    }       
    
    private function getPatientCviStatus()
    {
        $query = <<<EOL
                SELECT *
                FROM tmp_rco_nod_PatientCVIStatus_{$this->extract_identifier}
EOL;
                
        $dataQuery = array(
            'query' => $query,
            'header' => array('PatientId', 'Date', 'IsDateApprox', 'IsCVIBlind', 'IsCVIPartial'),
        );

        $this->saveCSVfile($dataQuery, 'PatientCVIStatus');
    }
    
    /********** end of PatientCVIStatus **********/
    
    
    
    
    
    /********** Episode **********/
    
    private function createTmpRcoNodMainEventEpisodes()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_main_event_episodes_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_main_event_episodes_{$this->extract_identifier} (
                oe_event_id int(10) NOT NULL,
                patient_id int(10) NOT NULL,
                nod_episode_id int(10) NOT NULL,
                nod_date date NOT NULL,
                oe_event_type tinyint(2) NOT NULL,
                PRIMARY KEY (oe_event_id)
            );
EOL;
        return $query;
    }
    
    /**
     * Load main control table with ALL events
     */
    private function populateTmpRcoNodMainEventEpisodes()
    {
        $query = <<<EOL
                #Load main control table with ALL operation events
                INSERT INTO tmp_rco_nod_main_event_episodes_{$this->extract_identifier} (
                    oe_event_id,
                    patient_id,
                    nod_episode_id,
                    nod_date,
                    oe_event_type
                )
                SELECT
                    event.id AS oe_event_id,
                    episode.patient_id AS patient_id,
                    event.id AS nod_episode_id,
                    DATE(event.event_date) AS nod_date,
                    event_type_id AS oe_event_type
                FROM event
                JOIN episode ON event.episode_id = episode.id
                JOIN event_type ON event.event_type_id = event_type.id AND event_type.name = 'Operation Note'
                WHERE event.deleted = 0;
                
                
                #Load main control table with ALL examination events (using previously identified patients in control table)
                INSERT INTO  tmp_rco_nod_main_event_episodes_{$this->extract_identifier} (
                                oe_event_id,
                                patient_id,
                                nod_episode_id,
                                nod_date,
                                oe_event_type 
                )
                SELECT
                        event.id AS oe_event_id,
                        episode.patient_id AS patient_id,
                        event.id AS nod_episode_id,
                        DATE(event.event_date) AS nod_date,
                        event.event_type_id AS oe_event_type
                FROM event
                JOIN episode ON event.episode_id = episode.id
                WHERE  episode.patient_id IN (SELECT c.patient_id FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c)
                AND event.event_type_id IN
                    (
                        SELECT event_type.id
                        FROM event_type
                        WHERE event_type.`name` IN ('Examination', 'Biometry', 'Prescription')
                    )
                AND event.deleted = 0;
EOL;
        return $query;
    }
    
    private function getEpisode()
    {
        $query = <<<EOL
                SELECT c.patient_id as PatientId, c.nod_episode_id as EpisodeId, c.nod_date as Date
                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('PatientId', 'EpisodeId', 'Date'),
        );

        $this->saveCSVfile($dataQuery, 'Episode');
    }
    
    /********** end of Episode **********/
    
    
    
    
    
    /********** EpisodePreOpAssessment **********/
    
    private function createTmpRcoNodEpisodePreOpAssessment()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodePreOpAssessment_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodePreOpAssessment_{$this->extract_identifier} (
                oe_event_id int(10) NOT NULL,
                Eye char(1) NOT NULL COMMENT 'L / R',
                IsAbleToLieFlat char(1) DEFAULT NULL COMMENT '0 = no, 1 = yes',
                IsInabilityToCooperate char(1) DEFAULT NULL COMMENT '0 = no, 1 = yes',
                UNIQUE KEY oe_event_id (oe_event_id,Eye)
            );
EOL;
        return $query;
    }
    
    private function populateTmpRcoNodEpisodePreOpAssessment()
    {
        $query = <<<EOL
                
                INSERT INTO tmp_rco_nod_EpisodePreOpAssessment_{$this->extract_identifier} (
                    oe_event_id,
                    Eye,
                    IsAbleToLieFlat,
                    IsInabilityToCooperate
                  )
                SELECT 
                    c.oe_event_id,
                    'L' AS Eye,
                    (SELECT CASE WHEN pr.risk_id IS NULL THEN 0 WHEN pr.risk_id = 1 THEN 1 ELSE 0 END) AS IsAbleToLieFlat,
                    (SELECT CASE WHEN pr.risk_id IS NULL THEN 0 WHEN pr.risk_id = 4 THEN 1 ELSE 0 END) AS IsInabilityToCooperate

                    /* Restriction: Start with control events */
                    FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c 

                    /* Join: Associated procedures, Implicit Restriction: Operations with procedures */
                    JOIN et_ophtroperationnote_procedurelist pl ON pl.event_id = c.oe_event_id

                    /* Outer Join: patient risks, Implicit Cartesian: all risk_ids  */
                    LEFT OUTER JOIN patient_risk_assignment pr ON pr.patient_id = c.patient_id # specify LEFT OUTER JOIN syntax in full

                    /* Restrict: LEFT/BOTH eyes */
                    WHERE pl.eye_id IN (1, 3)

                    /* Group by required as may have multiple procedures on eye */
                    GROUP BY oe_event_id, Eye, IsAbleToLieFlat, IsInabilityToCooperate;
                
                
                INSERT INTO tmp_rco_nod_EpisodePreOpAssessment_{$this->extract_identifier} (
                    oe_event_id,
                    Eye,
                    IsAbleToLieFlat,
                    IsInabilityToCooperate
                  )
                SELECT 
                    c.oe_event_id,
                    'R' AS Eye,
                    (SELECT CASE WHEN pr.risk_id IS NULL THEN 0 WHEN pr.risk_id = 1 THEN 1 ELSE 0 END) AS IsAbleToLieFlat,
                    (SELECT CASE WHEN pr.risk_id IS NULL THEN 0 WHEN pr.risk_id = 4 THEN 1 ELSE 0 END) AS IsInabilityToCooperate

                    /* Restriction: Start with control events */
                    FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c 

                    /* Join: Associated procedures, Implicit Restriction: Operations with procedures */
                    JOIN et_ophtroperationnote_procedurelist pl ON pl.event_id = c.oe_event_id

                    /* Outer Join: patient risks, Implicit Cartesian: all risk_ids  */
                    LEFT OUTER JOIN patient_risk_assignment pr ON pr.patient_id = c.patient_id # specify LEFT OUTER JOIN syntax in full

                    /* Restrict: LEFT/BOTH eyes */
                    WHERE pl.eye_id IN (2, 3)

                    /* Group by required as may have multiple procedures on eye */
                    GROUP BY oe_event_id, Eye, IsAbleToLieFlat, IsInabilityToCooperate;
EOL;
        return $query;
    }
    
    private function getEpisodePreOpAssessment()
    {

        $query = <<<EOL
                SELECT c.nod_episode_id as EpisodeId, p.Eye, p.isAbleToLieFlat, p.IsInabilityToCooperate
                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                JOIN tmp_rco_nod_EpisodePreOpAssessment_{$this->extract_identifier} p ON c.oe_event_id = p.oe_event_id
EOL;

        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'Eye', 'IsAbleToLieFlat', 'IsInabilityToCooperate'),
        );

        return $this->saveCSVfile($dataQuery, 'EpisodePreOpAssessment');
    }
    
    /********** end of EpisodePreOpAssessment **********/
    
    
    
    
    
    /********** EpisodeRefraction **********/
    
    private function createTmpRcoNodEpisodeRefraction()
    {
        $query = <<<EOL
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeRefraction_{$this->extract_identifier};
                CREATE TABLE tmp_rco_nod_EpisodeRefraction_{$this->extract_identifier} (
                    oe_event_id INT(10) NOT NULL,
                    Eye CHAR(1) NOT NULL,
                    RefractionTypeId CHAR(1) DEFAULT NULL,
                    Sphere DECIMAL(5,2) DEFAULT NULL,
                    Cylinder DECIMAL(5,2) DEFAULT NULL,
                    Axis INT(3) DEFAULT NULL,
                    ReadingAdd CHAR(1) DEFAULT NULL
                );
EOL;
                
        return $query;
    }
    /**
     * Populate  tmp_rco_nod_EpisodeRefraction_* table
     */
    private function populateTmpRcoNodEpisodeRefraction()
    {
        $query = <<<EOL
                INSERT INTO tmp_rco_nod_EpisodeRefraction_{$this->extract_identifier} (
                    oe_event_id,
                    Eye,
                    RefractionTypeId,
                    Sphere,
                    Cylinder,
                    Axis,
                    ReadingAdd
                  )
                SELECT
                      c.oe_event_id,
                      'L' AS Eye,
                      '' AS RefractionTypeId,
                      r.left_sphere AS Sphere,
                      r.left_cylinder AS Cylinder,
                      r.left_axis AS Axis, 
                      '' AS ReadingAdd
                
                /* Restriction: Start with control events */
                FROM  tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                JOIN et_ophciexamination_refraction r ON r.event_id = c.oe_event_id
                
                /* Restrict: LEFT/BOTH eyes */
                WHERE r.eye_id IN (1,3);
                
                
                INSERT INTO tmp_rco_nod_EpisodeRefraction_{$this->extract_identifier} (
                    oe_event_id,
                    Eye,
                    RefractionTypeId,
                    Sphere,
                    Cylinder,
                    Axis,
                    ReadingAdd
                  )
                SELECT
                      c.oe_event_id,
                      'R' AS Eye,
                      '' AS RefractionTypeId,
                      r.left_sphere AS Sphere,
                      r.left_cylinder AS Cylinder,
                      r.left_axis AS Axis, 
                      '' AS ReadingAdd
                
                /* Restriction: Start with control events */
                FROM  tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                JOIN et_ophciexamination_refraction r ON r.event_id = c.oe_event_id
                
                /* Restrict: RIGHT/BOTH eyes */
                WHERE r.eye_id IN (2,3);
EOL;
                
        return $query;
        
    }
    
    private function getEpisodeRefraction()
    {
        $query = <<<EOL
                SELECT c.nod_episode_id as EpisodeId, r.Eye, r.RefractionTypeId, r.Sphere, r.Cylinder, r.Axis, r.ReadingAdd
                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                JOIN tmp_rco_nod_EpisodeRefraction_{$this->extract_identifier} r ON c.oe_event_id = r.oe_event_id
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'Sphere', 'Cylinder', 'Axis', 'RefractionTypeId', 'ReadingAdd', 'Eye'),
        );

        return $this->saveCSVfile($dataQuery, 'EpisodeRefraction');
    }
    
    /********** end of EpisodeRefraction **********/
    
    
    

    /********** EpisodeDiagnosis **********/
    
    private function createTmpRcoNodEpisodeDiagnosis()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeDiagnoses_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeDiagnoses_{$this->extract_identifier} (
                oe_event_id INT(10) NOT NULL,
                Eye CHAR(1) NOT NULL,
                Date DATE DEFAULT NULL,
                SurgeonId INT(10) DEFAULT NULL,
                ConditionId INT(11) DEFAULT NULL,
                DiagnosisTermId INT(10) DEFAULT NULL
            );
EOL;
            
        return $query;
    }
    
    
    private function populateTmpRcoNodEpisodeDiagnosis()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeDiagnoses_{$this->extract_identifier} (
                oe_event_id,
                Eye,
                Date,
                SurgeonId,
                ConditionId,
                DiagnosisTermId
            ) 
            SELECT
                c.oe_event_id,
                (SELECT CASE WHEN eye_id = 1 THEN 'L' WHEN eye_id = 2 THEN 'R' WHEN eye_id = 3 THEN 'B' ELSE 'N' END ) AS Eye,
                DATE(last_modified_date) AS Date,
                (
                        SELECT (
                                IFNULL(
                                        (SELECT last_modified_user_id FROM episode_version WHERE ep.id=id ORDER BY last_modified_date ASC LIMIT 1),
                                        (SELECT last_modified_user_id FROM episode WHERE id = ep.id)
                                )
                        )
                ) AS SurgeonId,
                (
                        SELECT rco_condition_id FROM tmp_episode_diagnosis WHERE oe_subspecialty_id = (
                        SELECT service_subspecialty_assignment.subspecialty_id FROM firm 
                        JOIN service_subspecialty_assignment ON firm.service_subspecialty_assignment_id = service_subspecialty_assignment.id
                        WHERE firm.id = ep.firm_id)

                ) AS ConditionId,
                IFNULL(disorder_id, '') AS DiagnosisTermId
            FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
            JOIN episode ep ON c.oe_event_id = ep.id
            WHERE ep.firm_id IS NOT NULL;
EOL;
            
        return $query;
    }
    
    
    private function getEpisodeDiagnosis()
    {
        $query = <<<EOL
                SELECT c.nod_episode_id as EpisodeId, d.Eye, d.Date, d.SurgeonId, d.ConditionId, d.DiagnosisTermId
                FROM tmp_rco_nod_EpisodeDiagnoses_{$this->extract_identifier} d
                JOIN tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c ON d.oe_event_id = c.oe_event_id
                
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'Eye', 'Date', 'SurgeonId', 'ConditionId', 'DiagnosisTermId'),
        );

        $output =  $this->saveCSVfile($dataQuery, 'EpisodeDiagnosis');
        
        return $output;
    }
    
    /********** end of EpisodeDiagnosis **********/





    /********** EpisodeDrug **********/
    
    private function createTmpRcoNodEpisodeDrug()
    {
        
        $query = <<<EOL
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeDrug_{$this->extract_identifier};
                CREATE TABLE tmp_rco_nod_EpisodeDrug_{$this->extract_identifier} (
                        oe_event_id INT(10) NOT NULL,
                        Eye CHAR(1) NOT NULL,
                        DrugId VARCHAR(150) DEFAULT NULL, 
                        DrugRouteId INT(10) UNSIGNED DEFAULT NULL,
                        StartDate VARCHAR(10) DEFAULT NULL,
                        StopDate  VARCHAR(10) DEFAULT NULL,
                        IsAddedByPrescription  TINYINT(1) DEFAULT NULL,
                        IsContinueIndefinitely  TINYINT(1) DEFAULT NULL,
                        IsStartDateApprox TINYINT(1) DEFAULT NULL
                );
EOL;
        return $query;
        
    }
    
    private function populateTmpRcoNodEpisodeDrug()
    {
        $query = <<<EOL
                INSERT INTO tmp_rco_nod_EpisodeDrug_{$this->extract_identifier} (
                    oe_event_id,
                    Eye,
                    DrugId,
                    DrugRouteId,
                    StartDate,
                    StopDate,
                    IsAddedByPrescription,
                    IsContinueIndefinitely,
                    IsStartDateApprox
                  ) 
                  SELECT c.nod_episode_id AS EpisodeId,
                        (SELECT CASE WHEN option_id = 1 THEN 'L' WHEN option_id = 2 THEN 'R' WHEN option_id = 3 THEN 'B'  ELSE 'N' END) AS Eye,
                        (SELECT CASE WHEN m.drug_id IS NOT NULL THEN (SELECT name  FROM drug WHERE id = m.drug_id)
                                    WHEN m.medication_drug_id IS NOT NULL THEN (SELECT name FROM medication_drug WHERE id = m.medication_drug_id)
                                    WHEN m.medication_drug_id IS NULL THEN '' END) AS DrugId,
                      IFNULL(
                      (
                        SELECT nod_id FROM tmp_episode_drug_route
                        WHERE 
                       (opi.route_id = tmp_episode_drug_route.oe_route_id AND tmp_episode_drug_route.oe_option_id = opi.route_option_id) OR
                       (opi.route_id = tmp_episode_drug_route.oe_route_id AND tmp_episode_drug_route.oe_option_id IS NULL) )
                        , ""
                    ) AS DrugRouteId,
                        (SELECT CASE WHEN m.start_date IS NULL THEN '' ELSE m.start_date END) AS StartDate,
                        (SELECT CASE WHEN m.end_date IS NULL THEN '' ELSE m.end_date END) AS StopDate,
                        (SELECT CASE WHEN opi.prescription_id IS NOT NULL THEN 1 ELSE 0 END ) AS IsAddedByPrescription,
                        (SELECT CASE WHEN opi.continue_by_gp IS NULL THEN 0 ELSE opi.continue_by_gp END) AS IsContinueIndefinitely,
                        (SELECT CASE WHEN DAYNAME(m.start_date) IS NULL THEN 1 ELSE 0 END) AS IsStartDateApprox

                    FROM  tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c 
                    JOIN medication m ON c.patient_id = m.patient_id
                    LEFT JOIN ophdrprescription_item opi ON m.prescription_item_id = opi.id;
                
                
  
                INSERT INTO tmp_rco_nod_EpisodeDrug_{$this->extract_identifier} (
                    oe_event_id,
                    Eye,
                    DrugId,
                    DrugRouteId,
                    StartDate,
                    StopDate,
                    IsAddedByPrescription,
                    IsContinueIndefinitely,
                    IsStartDateApprox
                )
                SELECT
                    c.oe_event_id AS EpisodeId,
                    (SELECT CASE WHEN route_option_id = 1 THEN 'L' WHEN route_option_id = 2 THEN 'R' WHEN route_option_id = 3 THEN 'B'  ELSE 'N' END) AS Eye,
                    drug.name AS DrugId,

                    IFNULL(
                    (
                      SELECT nod_id FROM tmp_episode_drug_route
                      WHERE
                      (opi.route_id = tmp_episode_drug_route.oe_route_id AND tmp_episode_drug_route.oe_option_id = opi.route_option_id) OR
                      (opi.route_id = tmp_episode_drug_route.oe_route_id AND tmp_episode_drug_route.oe_option_id IS NULL) )
                      , ""
                    ) AS DrugRouteId,

                    DATE(event.event_date) AS StartDate,

                    CASE WHEN LOCATE('day', drug_duration.name) > 0 THEN
                        DATE_FORMAT(DATE_ADD(event.event_date, INTERVAL SUBSTR(drug_duration.name, 1, LOCATE('day', drug_duration.name)-1) DAY), '%Y-%m-%d')
                     WHEN LOCATE('month', drug_duration.name) > 0 THEN
                        DATE_FORMAT(DATE_ADD(event.event_date, INTERVAL SUBSTR(drug_duration.name, 1, LOCATE('month', drug_duration.name)-1) MONTH), '%Y-%m-%d')
                     WHEN LOCATE('week', drug_duration.name) > 0 THEN
                        DATE_FORMAT(DATE_ADD(event.event_date, INTERVAL SUBSTR(drug_duration.name, 1, LOCATE('week', drug_duration.name)-1) WEEK), '%Y-%m-%d')
                     ELSE ''
                    END
                    AS StopDate,

                    1 AS IsAddedByPrescription,
                    continue_by_gp AS IsContinueIndefinitely,
                    0 AS IsStartDateApprox

                    FROM ophdrprescription_item AS opi
                    JOIN et_ophdrprescription_details ON opi.prescription_id = et_ophdrprescription_details.id

                    JOIN tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c ON et_ophdrprescription_details.event_id = c.oe_event_id
                    JOIN event ON c.oe_event_id = event.id

                    JOIN drug ON opi.drug_id = drug.id
                    JOIN drug_duration ON opi.duration_id = drug_duration.id
                    LEFT JOIN medication ON medication.prescription_item_id = opi.id
                    WHERE medication.id is NULL;
EOL;
                      
        return $query;
    }
            
    private function getEpisodeDrug()
    {
        $query = <<<EOL
                SELECT c.nod_episode_id as EpisodeId, d.Eye, d.DrugId, d.DrugRouteId, d.StartDate, d.StopDate, d.IsAddedByPrescription, d.IsContinueIndefinitely, d.IsStartDateApprox
                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                JOIN tmp_rco_nod_EpisodeDrug_{$this->extract_identifier} d ON c.oe_event_id = d.oe_event_id
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'Eye', 'DrugId', 'DrugRouteId', 'StartDate', 'StopDate', 'IsAddedByPrescription', 'IsContinueIndefinitely', 'IsStartDateApprox'),
        );

        $output = $this->saveCSVfile($dataQuery, 'EpisodeDrug');
        
        return $output;
    }
    /********** end of EpisodeDrug **********/
    
    
    
    
    
    
    
    /********** EpisodeBiometry **********/
    
    
    private function createTmpRcoNodEpisodeBiometry()
    {
        
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeBiometry_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeBiometry_{$this->extract_identifier} (
                    oe_event_id INT(10) NOT NULL,
                    Eye CHAR(1) NOT NULL,
                    AxialLength DECIMAL(6,2) DEFAULT NULL, 
                    BiometryAScanId CHAR(1) DEFAULT NULL,
                    BiometryKeratometerId INT(10) DEFAULT NULL,
                    BiometryFormulaId INT(10) DEFAULT NULL,
                    K1PreOperative DECIMAL(6,2),
                    K2PreOperative DECIMAL(6,2),
                    AxisK1 DECIMAL(5,1),
                    AxisK2 DECIMAL(5,1),
                    ACDepth DECIMAL(6,2),
                    SNR DECIMAL(6,1)
            );
EOL;
        
        return $query;
    }
    
    private function populateTmpRcoNodEpisodeBiometry()
    {
        $query = <<<EOL
                INSERT INTO tmp_rco_nod_EpisodeBiometry_{$this->extract_identifier} (
                    oe_event_id,
                    Eye,
                    AxialLength,
                    BiometryAScanId,
                    BiometryKeratometerId,
                    BiometryFormulaId,
                    K1PreOperative,
                    K2PreOperative,
                    AxisK1,
                    AxisK2,
                    ACDepth,
                    SNR
                )
                SELECT
                    c.oe_event_id,
                    'L' AS Eye,
                    axial_length_left AS AxialLength,
                    '' AS BiometryAScanId,
                    (   SELECT CASE
                                WHEN ophinbiometry_imported_events.device_model = 'IOLmaster 500'  THEN 1
                                WHEN ophinbiometry_imported_events.device_model = 'Haag-Streit LensStar' THEN 2
                                WHEN ophinbiometry_imported_events.device_model = 'Other' THEN 9
                        END
                    ) AS BiometryKeratometerId,
                    ( 
                        SELECT code 
                        FROM tmp_biometry_formula 
                        WHERE tmp_biometry_formula.desc = ophinbiometry_calculation_formula.name COLLATE utf8_general_ci
                    ) AS BiometryFormulaId,
                    k1_left AS K1PreOperative,
                    k2_left AS K2PreOperative,
                    axis_k1_left AS AxisK1,
                    ms.k2_axis_left AS AxisK2,
                    ms.acd_left AS ACDepth,
                    ms.snr_left AS SNR
		
                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c

                JOIN et_ophinbiometry_measurement ms ON c.oe_event_id = ms.event_id

                LEFT JOIN ophinbiometry_imported_events ON c.oe_event_id  = ophinbiometry_imported_events.event_id
                LEFT JOIN et_ophinbiometry_selection ON c.oe_event_id  = et_ophinbiometry_selection.event_id
                        /* Restrict: LEFT/BOTH eyes */
                        AND et_ophinbiometry_selection.eye_id = 1 OR et_ophinbiometry_selection.eye_id = 3

                LEFT JOIN ophinbiometry_calculation_formula 
                        ON et_ophinbiometry_selection.formula_id_left = ophinbiometry_calculation_formula.id

                WHERE ms.deleted = 0 ;
                
                
                
                INSERT INTO tmp_rco_nod_EpisodeBiometry_{$this->extract_identifier} (
                    oe_event_id,
                    Eye,
                    AxialLength,
                    BiometryAScanId,
                    BiometryKeratometerId,
                    BiometryFormulaId,
                    K1PreOperative,
                    K2PreOperative,
                    AxisK1,
                    AxisK2,
                    ACDepth,
                    SNR
                )
                SELECT
                    c.oe_event_id,
                    'R' AS Eye,
                    axial_length_left AS AxialLength,
                    '' AS BiometryAScanId,
                    (SELECT CASE
                    WHEN ophinbiometry_imported_events.device_model = 'IOLmaster 500'  THEN 1
                    WHEN ophinbiometry_imported_events.device_model = 'Haag-Streit LensStar' THEN 2
                    WHEN ophinbiometry_imported_events.device_model = 'Other' THEN 9
                    END) AS BiometryKeratometerId,
                    ( SELECT code FROM tmp_biometry_formula WHERE tmp_biometry_formula.desc = ophinbiometry_calculation_formula.name COLLATE utf8_general_ci) AS BiometryFormulaId,
                    k1_left AS K1PreOperative,
                    k2_left AS K2PreOperative,
                    axis_k1_left AS AxisK1,
                    ms.k2_axis_left AS AxisK2,
                    ms.acd_left AS ACDepth,
                    ms.snr_left AS SNR

                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c

                JOIN et_ophinbiometry_measurement ms ON c.oe_event_id = ms.event_id

                LEFT JOIN ophinbiometry_imported_events ON c.oe_event_id = ophinbiometry_imported_events.event_id
                LEFT JOIN et_ophinbiometry_selection ON c.oe_event_id  = et_ophinbiometry_selection.event_id 
                        /* Restrict: RIGHT/BOTH eyes */
                        AND et_ophinbiometry_selection.eye_id = 2 OR et_ophinbiometry_selection.eye_id = 3

                LEFT JOIN ophinbiometry_calculation_formula
                        ON et_ophinbiometry_selection.formula_id_left = ophinbiometry_calculation_formula.id

                WHERE ms.deleted = 0;
                
EOL;
        return $query;
    }

    private function getEpisodeBiometry()
    {
        
        $query = <<<EOL
            SELECT  c.nod_episode_id,
                    b.Eye,
                    b.AxialLength,
                    b.BiometryAScanId,
                    b.BiometryKeratometerId,
                    b.BiometryFormulaId,
                    b.K1PreOperative,
                    b.K2PreOperative,
                    b.AxisK1,
                    b.AxisK2,
                    b.ACDepth,
                    b.SNR
            FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
            JOIN tmp_rco_nod_EpisodeBiometry_{$this->extract_identifier} b ON c.oe_event_id = b.oe_event_id
EOL;
        
        $dataQuery = array(
            'query' => $query,
            'header' => array(
                'EpisodeId',
                'Eye',
                'AxialLength',
                'BiometryAScanId',
                'BiometryKeratometerId',
                'BiometryFormulaId',
                'K1PreOperative',
                'K2PreOperative',
                'AxisK1',
                'AxisK2',
                'ACDepth',
                'SNR'
            ),
        );

        $output = $this->saveCSVfile($dataQuery, 'EpisodeBiometry');


        return $output;
    }
    
    /********** EpisodeBiometry **********/
    
    
    
    
    

    
    /********** EpisodeIOP **********/
    
    /**
     * Create tmp_rco_nod_EpisodeIOP_ table
     */
    private function createTmpRcoNodEpisodeIOP()
    {
        
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeIOP_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeIOP_{$this->extract_identifier} (
                oe_event_id INT(10) NOT NULL,
                Eye CHAR(1) NOT NULL,
                type CHAR(1) DEFAULT NULL, 
                GlaucomaMedicationStatusId TINYINT(1) UNSIGNED DEFAULT 9,
                value INT(10) DEFAULT NULL
            );
EOL;
        
        return $query;
    }
    
    /**
     * Load Episode IOP data
     */
    private function populateTmpRcoNodEpisodeIOP()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeIOP_{$this->extract_identifier} (
                oe_event_id,
                Eye,
                Type,
                GlaucomaMedicationStatusId,
                Value
            )
            SELECT
                c.nod_episode_id AS EpisodeId,
                'L' AS Eye,
                '' AS Type,
                9 AS GlaucomaMedicationStatusId,
                (oipvr.value + 0.0) AS VALUE

                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c

                JOIN et_ophciexamination_intraocularpressure etoi ON etoi.event_id = c.oe_event_id
                JOIN ophciexamination_intraocularpressure_value oipv ON oipv.element_id = etoi.id
                JOIN ophciexamination_intraocularpressure_reading oipvr ON oipv.reading_id = oipvr.id
                
                /* Restrict: LEFT/BOTH eyes */
                WHERE oipv.eye_id IN (1,3);
            
            	
            INSERT INTO tmp_rco_nod_EpisodeIOP_{$this->extract_identifier} (
                oe_event_id,
                Eye,
                Type,
                GlaucomaMedicationStatusId,
                Value
            )
            SELECT  c.nod_episode_id AS EpisodeId,
                    'R' AS Eye,
                    '' AS Type,
                    9 AS GlaucomaMedicationStatusId,
                    (oipvr.value + 0.0) AS VALUE

                    FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                    JOIN et_ophciexamination_intraocularpressure etoi ON etoi.event_id = c.oe_event_id
                    JOIN ophciexamination_intraocularpressure_value oipv ON oipv.element_id = etoi.id
                    JOIN ophciexamination_intraocularpressure_reading oipvr ON oipv.reading_id = oipvr.id
                    
                    /* Restrict: RIGHT/BOTH eyes */
                    WHERE oipv.eye_id IN (2,3);
            
EOL;
        return $query;
    }
    
    private function getEpisodeIOP()
    {
        $query = <<<EOL
                SELECT  c.nod_episode_id as EpisodeId, iop.Eye, iop.Type, iop.GlaucomaMedicationStatusId, iop.Value
                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                JOIN tmp_rco_nod_EpisodeIOP_{$this->extract_identifier} iop ON c.oe_event_id = iop.oe_event_id
EOL;

        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'Eye', 'Type', 'GlaucomaMedicationStatusId', 'Value'),
        );

        $output = $this->saveCSVfile($dataQuery, 'EpisodeIOP');

        return $output;
    }
    
    /********** end of EpisodeIOP **********/

    protected function array2Csv(array $data, $header = null, $dataFormatter = null)
    {

        ob_start();
        $df = fopen("php://output", 'w');

        if (count($data) !== 0) {
            fputcsv($df, array_keys(reset($data)));
            foreach ($data as $row) {
                if (method_exists($this, $dataFormatter)) {
                    $row = $this->$dataFormatter($row);
                }
                fputcsv($df, $row);
            }
        } else if ($header) {
            fputcsv($df, $header);
        }

        fclose($df);
        return ob_get_clean();
    }
    

    
    
    
    /********** EpisodePostOpComplication **********/
    
    private function createTmpRcoNodPostOpComplication()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodePostOpComplication_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodePostOpComplication_{$this->extract_identifier} (
                    oe_event_id INT(10) NOT NULL,
                    Eye CHAR(1) NOT NULL,
                    ComplicationTypeId INT(10) DEFAULT NULL
            );
EOL;
        return $query;
    }
    
    private function populateTmpRcoNodPostOpComplication()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodePostOpComplication_{$this->extract_identifier} (
                oe_event_id,
                Eye,
                ComplicationTypeId
            ) 
            SELECT
              c.oe_event_id,
              'L' AS Eye,
              ophciexamination_postop_complications.code AS ComplicationTypeId

            FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
            JOIN et_ophciexamination_postop_complications ON c.oe_event_id = et_ophciexamination_postop_complications.event_id
            JOIN ophciexamination_postop_et_complications ON et_ophciexamination_postop_complications.id = ophciexamination_postop_et_complications.element_id
            JOIN ophciexamination_postop_complications ON ophciexamination_postop_et_complications.complication_id = ophciexamination_postop_complications.id 
            AND ( ophciexamination_postop_et_complications.eye_id = 1 OR ophciexamination_postop_et_complications.eye_id = 3);
                
                
            INSERT INTO tmp_rco_nod_EpisodePostOpComplication_{$this->extract_identifier} (
                oe_event_id,
                Eye,
                ComplicationTypeId
            ) 
            SELECT
              c.oe_event_id,
              'L' AS Eye,
              ophciexamination_postop_complications.code AS ComplicationTypeId

            FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
            JOIN et_ophciexamination_postop_complications ON c.oe_event_id = et_ophciexamination_postop_complications.event_id
            JOIN ophciexamination_postop_et_complications ON et_ophciexamination_postop_complications.id = ophciexamination_postop_et_complications.element_id
            JOIN ophciexamination_postop_complications ON ophciexamination_postop_et_complications.complication_id = ophciexamination_postop_complications.id 
            AND ( ophciexamination_postop_et_complications.eye_id = 2 OR ophciexamination_postop_et_complications.eye_id = 3);
                
EOL;
        return $query;
        
    }
    
    private function getEpisodePostOpComplication()
    {

        $query = <<<EOL
                SELECT c.nod_episode_id as EpisodeId, c.oe_event_id as OperationId, p.Eye, p.ComplicationTypeId
                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                JOIN tmp_rco_nod_EpisodePostOpComplication_{$this->extract_identifier} p ON c.oe_event_id = p.oe_event_id
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'OperationId', 'Eye', 'ComplicationTypeId'),
        );

        $output = $this->saveCSVfile($dataQuery, 'EpisodePostOpComplication', null, 'EpisodeId');
        
        return $output;
    }
    
    /********** end of EpisodePostOpComplication **********/
    
    
    
    
    
    /********** EpisodeOperationCoPathology **********/

    private function createTmpRcoNodEpisodeOperationCoPathology()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationCoPathology_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeOperationCoPathology_{$this->extract_identifier} (
                oe_event_id INT(10) NOT NULL,
                Eye CHAR(1) NOT NULL,
                CoPathologyId INT(10) DEFAULT NULL
            );
EOL;
            return $query;
    }
    
    private function populateTmpRcoNodEpisodeOperationCoPathology()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeOperationCoPathology_{$this->extract_identifier} (
                oe_event_id,
                Eye,
                CoPathologyId
            )
            SELECT
                c.oe_event_id AS OperationId,
                (SELECT
                        CASE
                                WHEN (proc_list.eye_id = 3) THEN 'B'
                                WHEN (proc_list.eye_id = 2) THEN 'R'
                                WHEN (proc_list.eye_id = 1) THEN 'L'
                            END
                ) AS Eye,
                IF(element_type.name = 'Trabeculectomy', 25,23)  AS CoPathologyId

                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
                JOIN et_ophtroperationnote_procedurelist AS proc_list ON proc_list.event_id = c.oe_event_id
                JOIN ophtroperationnote_procedurelist_procedure_assignment AS proc_list_asgn ON proc_list_asgn.procedurelist_id = proc_list.id
                JOIN proc ON proc_list_asgn.proc_id = proc.id
                JOIN ophtroperationnote_procedure_element ON ophtroperationnote_procedure_element.procedure_id = proc.id

                JOIN element_type ON ophtroperationnote_procedure_element.element_type_id = element_type.id
                WHERE
                element_type.name IN ('Vitrectomy', 'Trabeculectomy');
                    
            
            
            INSERT INTO tmp_rco_nod_EpisodeOperationCoPathology_{$this->extract_identifier} (
                `oe_event_id`,
                `Eye`,
                `CoPathologyId`
              ) 		
            SELECT
            c.oe_event_id AS OperationId,
            (SELECT
                CASE
                        WHEN (proc_list.eye_id = 3) THEN 'B'
                        WHEN (proc_list.eye_id = 2) THEN 'R'
                        WHEN (proc_list.eye_id = 1) THEN 'L'
                    END
            ) AS Eye,
            21 AS CoPathologyId
            FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c

            JOIN `et_ophtroperationnote_procedurelist` AS proc_list ON proc_list.event_id = c.oe_event_id
            JOIN `ophtroperationnote_procedurelist_procedure_assignment` AS proc_list_asgn ON proc_list_asgn.procedurelist_id = proc_list.id
            JOIN proc ON proc_list_asgn.proc_id = proc.id
            JOIN procedure_benefit ON procedure_benefit.proc_id = proc.id
            JOIN benefit ON procedure_benefit.benefit_id = benefit.id
	    
            WHERE benefit.`name` = 'to prevent retinal detachment';
                
            INSERT INTO tmp_rco_nod_EpisodeOperationCoPathology_{$this->extract_identifier} (
                `oe_event_id`,
                `Eye`,
                `CoPathologyId`
            ) 
            SELECT 
                c.oe_event_id,
                (SELECT CASE
                        WHEN (left_cortical_id = 4 OR left_nuclear_id = 4) AND (right_cortical_id = 4 OR right_nuclear_id = 4) THEN 'B'
                        WHEN (left_cortical_id = 4 OR left_nuclear_id = 4) THEN 'L'
                        WHEN (right_cortical_id = 4 OR right_nuclear_id = 4) THEN 'R'
                        END
                ) AS Eye,
                14 AS CoPathologyId

                FROM et_ophciexamination_anteriorsegment a
                JOIN tmp_rco_nod_main_event_episodes_ c ON c.oe_event_id = a.event_id
                HAVING Eye IS NOT NULL;
                
                
                INSERT INTO tmp_rco_nod_EpisodeOperationCoPathology_{$this->extract_identifier} (
                    `oe_event_id`,
                    `Eye`,
                    `CoPathologyId`
                ) 
                SELECT
                    c.oe_event_id,
                    (SELECT CASE
                            WHEN secondary_diagnosis.eye_id = 1 THEN 'L'
                            WHEN secondary_diagnosis.eye_id = 2 THEN 'R'
                            WHEN secondary_diagnosis.eye_id = 3 THEN 'B'
                    END
                    ) AS Eye,
                    tmp_pathology_type.nodcode AS CoPathologyId

                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c

                JOIN secondary_diagnosis ON c.`patient_id` = secondary_diagnosis.`patient_id`
                JOIN `disorder` ON  secondary_diagnosis.`disorder_id` = `disorder`.id
                JOIN tmp_pathology_type ON LOWER(disorder.term) = LOWER(tmp_pathology_type.term);
EOL;
    }
    
    
    private function getEpisodeOperationCoPathology()
    {

        $query = <<<EOL
                SELECT p.oe_event_id as OperationId, p.Eye, p.CoPathologyId
                FROM tmp_rco_nod_EpisodeOperationCoPathology_{$this->extract_identifier} p;
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('OperationId', 'Eye', 'CoPathologyId'),
        );

        $output = $this->saveCSVfile($dataQuery, 'EpisodeOperationCoPathology', null, 'OperationId');

        return $output;
    }
    
    /********** end of EpisodeOperationCoPathology **********/
    
    
    
    
    
    /********** EpisodeTreatmentCataract **********/
    
    private function createTmpRcoNodEpisodeTreatmentCataract()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeTreatmentCataract_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeTreatmentCataract_{$this->extract_identifier} (
                oe_event_id int(10) NOT NULL,
                TreatmentId int(10) NOT NULL,
                IsFirstEye tinyint(1) NOT NULL,
                PreparationDrugId varchar(1) DEFAULT NULL,
                IncisionSiteId int(10) DEFAULT NULL,
                IncisionLengthId varchar(5) DEFAULT '2.8',
                IncisionPlanesId int(2) DEFAULT '4',
                IncisionMeridean varchar(5) DEFAULT '180',
                PupilSizeId int(10) DEFAULT NULL,
                IOLPositionId int(10) DEFAULT NULL,
                IOLModelId varchar(255) DEFAULT NULL,
                IOLPower varchar(5) DEFAULT NULL,
                PredictedPostOperativeRefraction decimal(4,2) DEFAULT NULL,
                WoundClosureId varchar(1) DEFAULT NULL
            );
EOL;
        return $query;
    }
    
    private function populateTmpRcoNodEpisodeTreatmentCataract()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeTreatmentCataract_{$this->extract_identifier} (
                oe_event_id,
                TreatmentId,
                IsFirstEye,
                PreparationDrugId,
                IncisionSiteId,
                IncisionLengthId,
                IncisionPlanesId,
                IncisionMeridean,
                PupilSizeId,
                IOLPositionId,
                IOLModelId,
                IOLPower,
                PredictedPostOperativeRefraction,
                WoundClosureId
              ) 
            SELECT 
                  ct.oe_event_id
                , ct.TreatmentId
                , IF(
                IFNULL(
                  /* Correlated SCALAR subquery to get EYE NAME from cateract management for clostest event(exam) to event(opnote) within episode_id */
                  /* This will return EYE NAME or null */
                  ( 
                    SELECT excme.name 
                    /* Start with all events(examination) for the same episode_id for the seed opnote (correlated in WHERE clause) */
                    FROM event eex
                    /* Hard Join: Event to cateract management, Implicit Restriction: reduce events to events(examinatio) */
                    /* Assumption made that the Event(OpNote) and Event(Examination) will be in same episode_id at least */
                    /* This is reasonable as even if CAT surgury is performed under another firm, e.g. GL, the examination would also be in GL) */
                    JOIN et_ophciexamination_cataractsurgicalmanagement excm
                      ON excm.event_id = eex.id
                    /* Join: Look up cateract management EYE NAME (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
                    JOIN ophciexamination_cataractsurgicalmanagement_eye excme
                      ON excme.id = excm.eye_id 
                    /* Restriction (correlated subquery): events(examination) with same episode_id as outer query event(opnote) */
                    WHERE eex.episode_id = eon.episode_id
                    /* Restriction: only events(examination) that are same date or before event(opnote) date */
                    AND eex.event_date <= eon.event_date
                    /* Sort most recent to top and limit results to just first result */
                    ORDER BY 
                      eex.event_date DESC
                    LIMIT 1      
                    )
                  , 'First eye'
                  ) = 'First eye'
                , 1
                , 0
                ) AS IsFirstEye
              , '' AS PreparationDrugId
              , IF(
                  oci.name = 'Limbal'
                , 5
                , IF(
                    oci.name = 'Scleral'
                  , 8
                  , 4
                  )
                ) AS IncisionSiteId
              , oc.length AS IncisionLengthId
              , 4 AS IncisionPlanesId /* TODO what was #unkown about in original implementation */
              , oc.meridian AS IncisionMeridean
              , IF(
                  oc.pupil_size = 'Small'
                , 1
                , IF(
                    oc.pupil_size = 'Medium'
                    , 2
                    , IF(
                        oc.pupil_size = 'Large'
                      , 3
                      , ''
                    )
                  )
                ) AS PupilSizeId
                , ocpt.nodcode AS IOLPositionId
                , oclt.name AS IOLModelId
                , oc.iol_power AS IOLPower
                , oc.predicted_refraction AS PredictedPostOperativeRefraction
                , '' AS WoundClosureId
            /* Restriction: Start with treatment records (processed previously), seeded from control events */
            FROM tmp_rco_nod_EpisodeTreatment_{$this->extract_identifier} ct
            /* Join: Look up Cataract operation detail, Implicit Restriction: reduces = treatment records those only cataract operations */
            JOIN et_ophtroperationnote_cataract oc 
              ON oc.event_id = ct.oe_event_id 
            /* Join: Look up INCISION SITE NAME (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            LEFT OUTER JOIN ophtroperationnote_cataract_incision_site AS oci 
              ON oci.id = oc.incision_site_id 
            /* Join: Look up IOL POSITION NAME (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            LEFT OUTER JOIN ophtroperationnote_cataract_iol_position ocp 
              ON ocp.id = oc.iol_position_id
            /* Join: Look up IOL POSITION CODE (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            LEFT OUTER JOIN tmp_iol_positions ocpt
              ON ocpt.term = ocp.name
            /* Join: Look up LENS TYPE (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            LEFT OUTER JOIN ophtroperationnote_cataract_iol_type oclt
              ON oclt.id = oc.iol_type_id

            /* Join: Look up original operation note event (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            LEFT OUTER JOIN event eon
              ON eon.id = ct.oe_event_id ;
EOL;
        return $query;
    }
    
    
    private function getEpisodeTreatmentCataract()
    {
        $query = <<<EOL
            SELECT  tc.TreatmentId, tc.IsFirstEye, tc.PreparationDrugId, tc.IncisionSiteId, tc.IncisionLengthId, tc.IncisionPlanesId,
                    tc.IncisionMeridean, tc.PupilSizeId, tc.IOLPositionId, tc.IOLModelId, tc.IOLPower, tc.PredictedPostOperativeRefraction, tc.WoundClosureId
            FROM tmp_rco_nod_EpisodeTreatmentCataract_{$this->extract_identifier} tc
EOL;
        
        $dataQuery = array(
            'query' => $query,
            'header' => array(
                'TreatmentId',
                'IsFirstEye',
                'PreparationDrugId',
                'IncisionSiteId',
                'IncisionLengthId',
                'IncisionPlanesId',
                'IncisionMeridean',
                'PupilSizeId',
                'IolPositionId',
                'IOLModelId',
                'IOLPower',
                'PredictedPostOperativeRefraction',
                'WoundClosureId',
            ),
        );

        return $this->saveCSVfile($dataQuery, 'EpisodeTreatmentCataract');

    }
    
    /********** end of EpisodeTreatmentCataract **********/
    
    
    
    
    
    /*********** EpisodeOperationAnesthesia ****************/
    
    private function createTmpRcoNodEpisodeOperationAnesthesia()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationAnesthesia_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeOperationAnesthesia_{$this->extract_identifier} (
                oe_event_id INT(10) NOT NULL,
                AnaesthesiaTypeId INT(10),
                AnaesthesiaNeedle INT(10),
                Sedation INT(10),
                SurgeonId INT(10),
                ComplicationId INT(10)
            );
EOL;
        return $query;
    }
    
    private function populateTmpRcoNodEpisodeOperationAnesthesia()
    {
        $query = "INSERT INTO tmp_rco_nod_EpisodeOperationAnesthesia_{$this->extract_identifier}(
                      oe_event_id,
                      AnaesthesiaTypeId,
                      AnaesthesiaNeedle,
                      Sedation,
                      SurgeonId,
                      ComplicationId
                  )
                      SELECT event_id AS oe_event_id,
                        (SELECT `nod_code` FROM tmp_anesthesia_type WHERE at.`name` = `name`) AS AnaesthesiaTypeId,
                        IFNULL(
                            (SELECT nod_id FROM tmp_anaesthetic_delivery WHERE a.anaesthetic_delivery_id = oe_id),
                            0
                        ) AS AnaesthesiaNeedle,
                        '9' as Sedation,
                        '' as SurgeonId,
                        (
                            SELECT tmp_complication.nod_id FROM tmp_complication WHERE oe_id = acs.id
                        ) as ComplicationId

                        FROM et_ophtroperationnote_anaesthetic a
                        JOIN `anaesthetic_type` `at` ON a.`anaesthetic_type_id` = at.`id`
                        JOIN ophtroperationnote_anaesthetic_anaesthetic_complication ac ON a.`id` = ac.`et_ophtroperationnote_anaesthetic_id`
                        JOIN ophtroperationnote_anaesthetic_anaesthetic_complications acs ON ac.`anaesthetic_complication_id` = acs.id
                        JOIN tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c ON c.oe_event_id = a.event_id;";

        return $query;
    }
    
    private function getEpisodeOperationAnaesthesia()
    {
        $query = "SELECT oe_event_id AS OperationId, AnaesthesiaTypeId, AnaesthesiaNeedle, Sedation, SurgeonId, ComplicationId
                    FROM tmp_rco_nod_EpisodeOperationAnesthesia_{$this->extract_identifier}";

        $dataQuery = array(
            'query' => $query,
            'header' => array('OperationId', 'AnaesthesiaTypeId', 'AnaesthesiaNeedle', 'Sedation', 'SurgeonId', 'ComplicationId'),
        );

        return $this->saveCSVfile($dataQuery, 'EpisodeOperationAnaesthesia', null, 'OperationId');
    }

    /********* end of EpisodeOperationAnesthesia***********/  
    
    
    
    
    
    /********** EpisodeTreatment **********/

    private function createTmpRcoNodEpisodeTreatment()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeTreatment_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeTreatment_{$this->extract_identifier} (
                oe_event_id INT(10) NOT NULL,
                TreatmentId INT(10) NOT NULL,
                OperationId INT(10) NOT NULL,
                Eye CHAR(1) NOT NULL,
                TreatmentTypeId VARCHAR(20) NOT NULL,
                TreatmentTypeDescription VARCHAR(255) NOT NULL,
                PRIMARY KEY (TreatmentId),
                UNIQUE KEY oe_event_id (oe_event_id,TreatmentId,Eye)
            );
EOL;
        return $query;
    }
    
    
    private function populateTmpRcoNodEpisodeTreatment()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeTreatment_{$this->extract_identifier} (
                oe_event_id	
                ,TreatmentId
                ,OperationId
                ,Eye
                ,TreatmentTypeId
                ,TreatmentTypeDescription
                )
                /* Procedures for LEFT eye */
            SELECT 
                c.oe_event_id ,
                /* Note pa.id unique for each operation<->procedure intersection record */
                /* However the procedure may be for BOTH EYES and the RCO needs this splitting out to two Treatment records LEFT + RIGHT */
                /* We are creating "high range" Treatment IDs for LEFT eye only by adding 1,000,000,000,000) to the number-space */
                (SELECT MAX(pa.id)+10000) + pa.id AS TreatmentId /* LEFT Eye so add 10,000 */
                , c.oe_event_id AS OperationId
                , 'L' AS Eye
                , p.snomed_code AS TreatmentTypeId
                , p.snomed_term AS TreatmentTypeDescription
                /* Restriction: Start with control events */

                FROM tmp_rco_nod_main_event_episodes_ c
                /* Join: Look up PROCEDURE_LIST (containers) - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
                /* Cardinality: On investigation et_ophtroperationnote_procedurelist is a logical bucket for procedures on the */
                /* on the LEFT Eye or the RIGHT Eye. Therefore if procedures were carried our on both eyes then */
                /* two et_ophtroperationnote_procedurelist records would exist each with intersection records */
                /* (ophtroperationnote_procedurelist_procedure_assignment) to the lookup procedure (proc) */
                LEFT OUTER JOIN et_ophtroperationnote_procedurelist pl
                  ON pl.event_id = c.oe_event_id
                /* Join: Look up Procedure List ITEMS (intersection table to proc) - (LOJ used to return nulls if data problems) */
                LEFT OUTER JOIN ophtroperationnote_procedurelist_procedure_assignment pa
                  ON pa.procedurelist_id = pl.id
                /* Join: Look up PROCEDURE DETAIL (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
                LEFT OUTER JOIN proc p 
                  ON p.id = pa.proc_id
                /* Restrict: Only OPERATION NOTE type events */
                WHERE c.oe_event_type = 4 #'Operation Note'
                /* Restrict: LEFT or BOTH eyes only */
                AND pl.eye_id IN (1, 3) /* 1 = LEFT EYE, 3 = BOTH EYES */
            
            UNION ALL
            
                /* Procedures for RIGHT eye */
            SELECT 
                c.oe_event_id
                /* Note pa.id unique for each operation<->procedure intersection record */
                /* However the procedure may be for BOTH EYES and the RCO needs this splitting out to two Treatment records LEFT + RIGHT */
                /* We are creating "high range" Treatment IDs for LEFT eye only by adding 1,000,000,000,000) to the number-space */
                , 0 + pa.id AS TreatmentId /* RIGHT Eye so add zero */
                , c.oe_event_id AS OperationId
                , 'R' AS Eye
                , p.snomed_code AS TreatmentTypeId
                , p.snomed_term AS TreatmentTypeDescription
                /* Restriction: Start with control events */
            
                FROM tmp_rco_nod_main_event_episodes_ c 
                /* Join: Look up PROCEDURE_LIST (containers) - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
                /* Cardinality: On investigation et_ophtroperationnote_procedurelist is a logical bucket for procedures on the */
                /* on the LEFT Eye or the RIGHT Eye. Therefore if procedures were carried our on both eyes then */
                /* two et_ophtroperationnote_procedurelist records would exist each with intersection records */
                /* (ophtroperationnote_procedurelist_procedure_assignment) to the lookup procedure (proc) */
                LEFT OUTER JOIN et_ophtroperationnote_procedurelist pl
                  ON pl.event_id = c.oe_event_id
                /* Join: Look up Procedure List ITEMS (intersection table to proc) - (LOJ used to return nulls if data problems) */
                LEFT OUTER JOIN ophtroperationnote_procedurelist_procedure_assignment pa
                  ON pa.procedurelist_id = pl.id
                /* Join: Look up PROCEDURE DETAIL (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
                LEFT OUTER JOIN proc p 
                  ON p.id = pa.proc_id
                /* Restrict: Only OPERATION NOTE type events */
                WHERE c.oe_event_type = 4 #'Operation Note'
                /* Restrict: RIGHT or BOTH eyes only */
                AND pl.eye_id IN (2, 3); /* 2 = RIGHT EYE, 3 = BOTH EYES */
            
EOL;
        return $query;
    }
    
    
    
    private function getEpisodeTreatment()
    {
        $query = <<<EOL
                SELECT t.TreatmentId, t.OperationId, t.Eye, t.TreatmentTypeId
                FROM tmp_rco_nod_EpisodeTreatment_{$this->extract_identifier} t
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('TreatmentId', 'OperationId', 'Eye', 'TreatmentTypeId'),
        );

        return $this->saveCSVfile($dataQuery, 'EpisodeTreatment');
    }
    
    /********** EpisodeOperationIndication **********/
    
    private function createTmpRcoNodEpisodeOperationIndication()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationIndication_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeOperationIndication_{$this->extract_identifier} (
                oe_event_id INT(10) NOT NULL,
                OperationId INT(10) NOT NULL,
                Eye CHAR(1) NOT NULL,
                IndicationId INT(10) NOT NULL,
                IndicationDescription VARCHAR(255) NOT NULL,
            UNIQUE KEY OperationId (OperationId,Eye,IndicationId) 
            );
EOL;
        return $query;
    }
    
    private function populateTmpRcoNodEpisodeOperationIndication()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeOperationIndication_{$this->extract_identifier} (
                oe_event_id,
                OperationId,
                Eye,
                IndicationId,
                IndicationDescription
              )
            SELECT 
                o.oe_event_id
              , o.OperationId
              , 'L' AS Eye
              , d.id AS IndicationId
              , d.term AS IndicationDescription
              /* Restriction: Start with operations (processed previously) */
            FROM tmp_rco_nod_EpisodeOperation_{$this->extract_identifier} o
            /* Join: Look up PROCEDURE_LIST (containers) - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            /* Cardinality: On investigation et_ophtroperationnote_procedurelist is a logical bucket for procedures on the */
            /* on the LEFT Eye or the RIGHT Eye. Therefore if procedures were carried our on both eyes then */
            /* two et_ophtroperationnote_procedurelist records would exist each with intersection records */
            /* (ophtroperationnote_procedurelist_procedure_assignment) to the lookup procedure (proc) */
            LEFT OUTER JOIN et_ophtroperationnote_procedurelist pl
                ON pl.event_id = o.oe_event_id
            /* Join: Get associated Booking Event DISORDERS - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            LEFT OUTER JOIN et_ophtroperationbooking_diagnosis be
                ON be.event_id = pl.booking_event_id
            /* Join: Lookup DISORDER DETAIL - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            LEFT OUTER JOIN disorder d
                ON d.id = be.disorder_id
              /* Restrict: LEFT or BOTH eyes only */
              AND pl.eye_id IN (1, 3) /* 1 = LEFT EYE, 3 = BOTH EYES */
              UNION ALL
            SELECT 
                o.oe_event_id
              , o.OperationId
              , 'R' AS Eye
              , d.id AS IndicationId
              , d.term AS IndicationDescription
              /* Restriction: Start with operations (processed previously) */
            FROM tmp_rco_nod_EpisodeOperation_{$this->extract_identifier} o
            /* Join: Look up PROCEDURE_LIST (containers) - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            /* Cardinality: On investigation et_ophtroperationnote_procedurelist is a logical bucket for procedures on the */
            /* on the LEFT Eye or the RIGHT Eye. Therefore if procedures were carried our on both eyes then */
            /* two et_ophtroperationnote_procedurelist records would exist each with intersection records */
            /* (ophtroperationnote_procedurelist_procedure_assignment) to the lookup procedure (proc) */
            LEFT OUTER JOIN et_ophtroperationnote_procedurelist pl
              ON pl.event_id = o.oe_event_id
            /* Join: Get associated Booking Event DISORDERS - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            LEFT OUTER JOIN et_ophtroperationbooking_diagnosis be
              ON be.event_id = pl.booking_event_id
            /* Join: Lookup DISORDER DETAIL - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
            LEFT OUTER JOIN disorder d
              ON d.id = be.disorder_id
            /* Restrict: RIGHT or BOTH eyes only */
            AND pl.eye_id IN (2, 3) /* 2 = RIGHT EYE, 3 = BOTH EYES */ ;
                
EOL;
        return $query;
    }
        
    private function getEpisodeOperationIndication()
    {
        $query = <<<EOL
                SELECT i.OperationId, i.Eye, i.IndicationId, i.IndicationDescription
                FROM tmp_rco_nod_EpisodeOperationIndication_{$this->extract_identifier} i
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('OperationId', 'Eye', 'IndicationId'),
        );

        return $this->saveCSVfile($dataQuery, 'EpisodeOperationIndication', null, 'OperationId');

    }
    
    /********** end of EpisodeOperationIndication **********/

    
    
    
    /********** EpisodeOperationComplication **********/
    
    private function createTmpRcoNodEpisodeOperationComplication()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationComplication_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeOperationComplication_{$this->extract_identifier} (
                oe_event_id int(10) NOT NULL,
                OperationId int(10) NOT NULL,
                Eye char(1) NOT NULL,
                ComplicationTypeId int(10) NOT NULL,
                ComplicationTypeDescription varchar(255) DEFAULT NULL,
                UNIQUE KEY OperationId (OperationId,Eye,ComplicationTypeId)
            ) ;
EOL;
        return $query;
    }
    
    
    private function populateTmpRcoNodEpisodeOperationComplication()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeOperationComplication_{$this->extract_identifier} (
                oe_event_id,
                OperationId,
                Eye,
                ComplicationTypeId,
                ComplicationTypeDescription
              ) 
            SELECT
                co.oe_event_id
                , co.OperationId
                , 'L' AS Eye
                , IFNULL(rcoct.code, oncc.id) AS ComplicationTypeId
                , onccs.name AS ComplicationTypeDescription
                
                /* Restriction: Start with OPERATIONS (processed previously), seeded from control events */
                FROM tmp_rco_nod_EpisodeOperation_{$this->extract_identifier} co
                
                /* Hard Join: Operation Note Cataract Detail */
                JOIN et_ophtroperationnote_cataract onc
                    ON onc.event_id = co.oe_event_id
                /* Hard Join: Operation Note Complications */
                JOIN ophtroperationnote_cataract_complication oncc
                    ON oncc.cataract_id = onc.id
                JOIN ophtroperationnote_cataract_complications onccs
                    ON oncc.complication_id = onccs.id
                /* Outer Join: Lookup RCO specific complication codes (LOJ to allow for unmapped codes) */
                LEFT OUTER JOIN tmp_complication_type rcoct
                    ON rcoct.name = onccs.name
                
                /* Hard Join (Implicit Cartesian Product): PROCEDURE_LIST(containers), Cartesian = Complications X Procedure_list
                /* Cardinality: On investigation et_ophtroperationnote_procedurelist is a logical bucket for procedures on the */
                /* on the LEFT Eye or the RIGHT Eye. Therefore if procedures were carried our on both eyes then */
                /* two et_ophtroperationnote_procedurelist records would exist each with intersection records */
                /* (ophtroperationnote_procedurelist_procedure_assignment) to the lookup procedure (proc) */
                JOIN et_ophtroperationnote_procedurelist pl
                    ON pl.event_id = co.oe_event_id  
                
                /* Restrict: LEFT or BOTH eyes only */
                WHERE pl.eye_id IN (1, 3) /* 1 = LEFT EYE, 3 = BOTH EYES */
            UNION ALL
                
                /* Complications for RIGHT eye */
                
            SELECT
                co.oe_event_id
                , co.OperationId
                , 'R' AS Eye
                , IFNULL(rcoct.code, oncc.id) AS ComplicationTypeId
                , onccs.name AS ComplicationTypeDescription
                
                /* Restriction: Start with OPERATIONS (processed previously), seeded from control events */
                FROM tmp_rco_nod_EpisodeOperation_{$this->extract_identifier} co
                
                /* Hard Join: Operation Note Cataract Detail */
                JOIN et_ophtroperationnote_cataract onc
                    ON onc.event_id = co.oe_event_id
                
                /* Hard Join: Operation Note Complications */
                JOIN ophtroperationnote_cataract_complication oncc
                    ON oncc.cataract_id = onc.id
                
                JOIN ophtroperationnote_cataract_complications onccs
                    ON oncc.complication_id = onccs.id  
                
                /* Outer Join: Lookup RCO specific complication codes (LOJ to allow for unmapped codes) */
                LEFT OUTER JOIN tmp_complication_type rcoct
                    ON rcoct.name = onccs.name
                
                /* Hard Join (Implicit Cartesian Product): PROCEDURE_LIST(containers), Cartesian = Complications X Procedure_list
                /* Cardinality: On investigation et_ophtroperationnote_procedurelist is a logical bucket for procedures on the */
                /* on the LEFT Eye or the RIGHT Eye. Therefore if procedures were carried our on both eyes then */
                /* two et_ophtroperationnote_procedurelist records would exist each with intersection records */
                /* (ophtroperationnote_procedurelist_procedure_assignment) to the lookup procedure (proc) */
                JOIN et_ophtroperationnote_procedurelist pl
                    ON pl.event_id = co.oe_event_id  
                
                /* Restrict: RIGHT or BOTH eyes only */
                WHERE pl.eye_id IN (2, 3); /* 2 = RIGHT EYE, 3 = BOTH EYES */
                
EOL;
        return $query;
    }
    
    
    
    private function getEpisodeOperationComplication()
    {
        $query = <<<EOL
                SELECT oc.OperationId, oc.Eye, oc.ComplicationTypeId
                FROM tmp_rco_nod_EpisodeOperationComplication_{$this->extract_identifier} oc
EOL;
                
        $dataQuery = array(
            'query' => $query,
            'header' => array('OperationId', 'Eye', 'ComplicationTypeId'),
        );

        return $this->saveCSVfile($dataQuery, 'EpisodeOperationComplication', null, 'OperationId');
    }
    
    /********** end of EpisodeOperationComplication **********/

    
    
    
    
    /********** EpisodeOperation **********/
    
    private function createTmpRcoNodEpisodeOperation()
    {
        $query = <<<EOL
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperation_{$this->extract_identifier};
                CREATE TABLE tmp_rco_nod_EpisodeOperation_{$this->extract_identifier} (
                    oe_event_id int(10) NOT NULL,
                    OperationId int(10) NOT NULL,
                    Description text,
                    IsHypertensive VARCHAR(1) DEFAULT NULL,
                    ListedDate date NOT NULL,
                    SurgeonId int(10) NOT NULL,
                    SurgeonGradeId int(11) DEFAULT NULL,
                    AssistantId varchar(10) DEFAULT NULL,
                    AssistantGradeId varchar(10) DEFAULT NULL,
                    ConsultantId varchar(10) DEFAULT NULL,
                    PRIMARY KEY (oe_event_id),
                    UNIQUE KEY OperationId (OperationId)
                );
EOL;
        return $query;
    }
    
    private function populateTmpRcoNodEpisodeOperation()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeOperation_{$this->extract_identifier} (
                oe_event_id,
                OperationId,
                Description,
                IsHypertensive,
                ListedDate,
                SurgeonId,
                SurgeonGradeId,
                AssistantId,
                AssistantGradeId,
                ConsultantId
            )
            SELECT
                c.oe_event_id, c.oe_event_id AS OperationId,
               '' AS Description, /* TODO (not required for minimal data set) mapping: et_ophtroperationnote_procedurelist.id-> ophtroperationnote_procedurelist_procedure_assignment.proc_id->proc.snomed_term (semi-colon separated) */
               '' AS IsHypertensive, /* TODO (not required for minimal data set) Toby Bisco said not currently in OE */
               DATE(c.nod_date) AS ListedDate, /* TODO (not required for minimal data set) the specified mapping may not be correct */
               s.surgeon_id AS SurgeonId,
               su.doctor_grade_id AS SurgeonGradeId,
               s.assistant_id AS AssistantId,
               au.doctor_grade_id AS AssistantGradeId,
               s.supervising_surgeon_id AS ConsultantId /* TODO (not required for minimal data set) but mapping not fully implemented */
              /* Restriction: Start with control events */

              FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c

              /* Join: Look up Operation Note SURGEON information */
              /* LOOOOOOOOOOOOOOK TODO CHECK ASSUMPTION: only one et_ophtroperationnote_surgeon per operation note */
              LEFT OUTER JOIN et_ophtroperationnote_surgeon s ON s.event_id = c.oe_event_id

              /* Join: Look up SURGEON user information (LOJ used to return nulls if data problems (as opposed to loosing parent rows) */
              LEFT OUTER JOIN user su ON s.surgeon_id = su.id

              /* Join: Look up ASSISTANT user information (LOJ used to return nulls if data problems (as opposed to loosing parent rows) */
              LEFT OUTER JOIN user au ON s.assistant_id = au.id

              /* Restrict: Only OPERATION NOTE type events */
              WHERE c.oe_event_type = 4; #'Operation Note';
              
EOL;
        return $query;
    }
    
    
    private function getEpisodeOperation()
    {
        $query = <<<EOL
            SELECT  op.OperationId, c.nod_episode_id as EpisodeId, op.Description, op.IsHypertensive, op.ListedDate, op.SurgeonId, IFNULL(op.SurgeonGradeId, "") as SurgeonGradeId, 
                    IFNULL(op.AssistantId, "") as AssistantId,
                    IFNULL(op.AssistantGradeId, "") as AssistantGradeId, IFNULL(op.ConsultantId, "") as ConsultantId
            FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c
            JOIN tmp_rco_nod_EpisodeOperation_{$this->extract_identifier} op ON c.oe_event_id = op.oe_event_id
            
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('OperationId', 'EpisodeId', 'Description', 'IsHypertensive', 'ListedDate', 'SurgeonId', 'SurgeonGradeId', 'AssistantId', 'AssistantGradeId','ConsultantId'),
        );
        return $this->saveCSVfile($dataQuery, 'EpisodeOperation');

    }
    
    /********** end of EpisodeOperation **********/
    
    
    
    
    
    /********** EpisodeVisualAcuity **********/
    
    private function createTmpRcoNodEpisodeVisualAcuity()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeVisualAcuity_{$this->extract_identifier};
            CREATE TABLE tmp_rco_nod_EpisodeVisualAcuity_{$this->extract_identifier} (
                oe_event_id INT(10) NOT NULL,
                Eye CHAR(1) NOT NULL,
                NotationRecordedId INT(10) NOT NULL,
                BestMeasure VARCHAR(255) NOT NULL,
                Unaided INT(10) DEFAULT NULL,
                Pinhole INT(10) DEFAULT NULL,
                BestCorrected INT(10) DEFAULT NULL
            );
EOL;
        return $query;
    }
    
    private function populateTmpRcoNodEpisodeVisualAcuity()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeVisualAcuity_{$this->extract_identifier} (
                oe_event_id,
                Eye,
                NotationRecordedId,
                BestMeasure,
                Unaided,
                Pinhole,
                BestCorrected
              ) 
              /* Get best Visual Acuity for examination for each Method: Unaided Aided Pinhole */
              SELECT
                  bv.oe_event_id 
                , bv.eye
                , bv.orginal_unit_id AS NotationRecordedId /* TODO this needs a mapping from OE to NOD */
                , IFNULL(CASE u_max_all.value WHEN 'CF' THEN 2.10 WHEN 'HM' THEN 2.40 WHEN 'PL' THEN 2.70 WHEN 'NPL' THEN 3.00 ELSE u_max_all.value END, '') AS BestMeasure
                , IFNULL(CASE u_max_unaided.value WHEN 'CF' THEN 2.10 WHEN 'HM' THEN 2.40 WHEN 'PL' THEN 2.70 WHEN 'NPL' THEN 3.00 ELSE u_max_unaided.value END, '') AS Unaided
                , IFNULL(CASE u_max_pinhole.value WHEN 'CF' THEN 2.10 WHEN 'HM' THEN 2.40 WHEN 'PL' THEN 2.70 WHEN 'NPL' THEN 3.00 ELSE u_max_pinhole.value END, '') AS Pinhole
                , IFNULL(CASE u_max_aided.value WHEN 'CF' THEN 2.10 WHEN 'HM' THEN 2.40 WHEN 'PL' THEN 2.70 WHEN 'NPL' THEN 3.00 ELSE u_max_aided.value END, '') AS BestCorrected
            FROM
            (
                    SELECT 
                      v.oe_event_id
                    , v.orginal_unit_id
              , v.eye
                    , MAX(IF(v.method IN ('Unaided','Aided','Pinhole'), v.reading_base_value, NULL)) AS max_all_base_value /* Higher base_value is better */
                    , MAX(IF(v.method = 'Unaided', v.reading_base_value, NULL)) AS max_unaided_base_value /* Higher base_value is better */
                    , MAX(IF(v.method = 'Aided', v.reading_base_value, NULL)) AS max_aided_base_value /* Higher base_value is better */
                    , MAX(IF(v.method = 'Pinhole', v.reading_base_value, NULL)) AS max_pinhole_base_value /* Higher base_value is better */
                    , v.logmar_single_letter_unit_id
              FROM (
                SELECT 
                  c.oe_event_id
                , CASE evar.side
                  WHEN 0 THEN 'R'
                  WHEN 1 THEN 'L'
                  ELSE NULL
                  END AS eye
                , CASE vam.name
                  WHEN 'Glasses' THEN 'Aided'
                  WHEN 'Contact lens' THEN 'Aided'
                  ELSE vam.name
                  END AS method
                , evar.value AS reading_base_value
                , eva.unit_id orginal_unit_id
                , u.id AS logmar_single_letter_unit_id
                /* Restriction: Start with control events */
                FROM tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c 
                /* Hard Join: Only examination events that have a Visual Acuity */
                JOIN et_ophciexamination_visualacuity eva
                  ON eva.event_id = c.oe_event_id
                /* Hard Join: Visual Acuity individual readings (across both eyes and all methods ) */
                JOIN ophciexamination_visualacuity_reading evar
                  ON evar.element_id = eva.id
                /* Join: Look up Visual Acuity individual readings for both eyes and all methods (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
                LEFT OUTER JOIN ophciexamination_visualacuity_method vam 
                  ON vam.id = evar.method_id
                /* Cartesian: Convenience to get logMAR single-letter unit_id only once, used in outer queries */
                CROSS JOIN ophciexamination_visual_acuity_unit u
                WHERE u.name = 'logMAR single-letter'
                    ) v
                    /* Group by important to aggrigate multiple reading to get best reading each eye and method */
              GROUP BY 
                      v.oe_event_id
                    , v.orginal_unit_id
              , v.eye
            ) bv
            /* Join: Decode the overall base_value to logmar_single_letter (LOJ used to return nulls if data problems) */
            LEFT OUTER JOIN ophciexamination_visual_acuity_unit_value u_max_all
              ON  u_max_all.base_value = bv.max_all_base_value
              AND u_max_all.unit_id = bv.logmar_single_letter_unit_id
            /* Join: Decode the unaided base_value to logmar_single_letter (LOJ used to return nulls if data problems) */
            LEFT OUTER JOIN ophciexamination_visual_acuity_unit_value u_max_unaided
              ON  u_max_unaided.base_value = bv.max_unaided_base_value
              AND u_max_unaided.unit_id = bv.logmar_single_letter_unit_id
            /* Join: Decode the aided base_value to logmar_single_letter (LOJ used to return nulls if data problems) */
            LEFT OUTER JOIN ophciexamination_visual_acuity_unit_value u_max_aided
              ON  u_max_aided.base_value = bv.max_aided_base_value
              AND u_max_aided.unit_id = bv.logmar_single_letter_unit_id
            /* Join: Decode the pinhole base_value to logmar_single_letter (LOJ used to return nulls if data problems) */
            LEFT OUTER JOIN ophciexamination_visual_acuity_unit_value u_max_pinhole
              ON  u_max_pinhole.base_value = bv.max_pinhole_base_value
              AND u_max_pinhole.unit_id = bv.logmar_single_letter_unit_id ;

EOL;
        return $query;
    }
    
    private function getEpisodeVisualAcuity()
    {        
        $query = <<<EOL
                SELECT c.nod_episode_id as EpisodeId, va.Eye, va.NotationRecordedId, va.BestMeasure, va.Unaided, va.Pinhole, va.BestCorrected
                FROM tmp_rco_nod_EpisodeVisualAcuity_{$this->extract_identifier} va
                JOIN tmp_rco_nod_main_event_episodes_{$this->extract_identifier} c ON va.oe_event_id = c.oe_event_id
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'Eye', 'NotationRecordedId', 'BestMeasure', 'Unaided', 'Pinhole', 'BestCorrected'),
        );

        return $this->saveCSVfile($dataQuery, 'EpisodeVisualAcuity');
    }
    
    /********** end of EpisodeVisualAcuity **********/
        
    
    
    
    
    /**
     * Creates zip files from the CSV files
     */
    private function createZipFile()
    {

        $zip = new ZipArchive();

        if ($zip->open($this->exportPath . '/' . $this->zipName, ZIPARCHIVE::CREATE) !== true) {
            exit("cannot open <$name>\n");
        }

        foreach (glob($this->exportPath . "/*.csv") as $filename) {
            $zip->addFile($filename, basename($filename));
        }
        $zip->close();
    }

    /**
     * @param array $row
     * @return array
     */
    protected function prescriptionItemFormat(array $row)
    {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $row['StopDate']) === 0) {
            if (!in_array($row['StopDate'], array('Other', 'Until review'))) {
                $startDate = new DateTime($row['StartDate']);
                $endDate = $startDate->add(DateInterval::createFromDateString($row['StopDate']));
                $row['StopDate'] = $endDate->format('Y-m-d');
            } else {
                $row['StopDate'] = '';
            }
        }

        return $row;
    }
}
