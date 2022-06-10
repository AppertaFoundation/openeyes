<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
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
    private $extractIdentifier;


    public function accessRules()
    {
        return array(
            array('allow',
                'roles' => array('NOD Export'),
            ),
        );
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

        if ($startDate) {
            $startDateTime = new DateTime($startDate);
        }

        if ($endDate) {
            $endDateTime = new DateTime($endDate);
        }

        // if start date is greater than end date we exchange the two dates
        if (($startDateTime instanceof DateTime && $endDateTime instanceof DateTime) && $endDateTime < $startDateTime) {
            $tempDate = $endDateTime;
            $endDateTime = $startDateTime;
            $startDateTime = $tempDate;
            $tempDate = null;
        }

        if ($startDate) {
            $this->startDate = $startDateTime->format('Y-m-d');
        }

        if ($endDate) {
            $this->endDate = $endDateTime->format('Y-m-d');
        }

        // Refactoring : generate number from hour-minute-sec
        // this number will be appended to the name of tmp tables
        // tmp tables will be normal DB tables instead of real TEMPORARY tables because in some queries
        // we need to refer the tmp table (like sub-select) two or more times - and in MySQL a tmp table can be referred only once in a query
        // (this prevents error if someone starts 2 extract)
        $this->extractIdentifier = date('His');

        parent::init();
    }

    public function actionIndex()
    {
        $this->pageTitle = 'NOD Export';
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
        }
    }

    public function setExportPath($path)
    {
        $this->exportPath = $path;
    }

    public function setZipName($name)
    {
        $this->zipName = $name;
    }

    public function setStartDate($date)
    {
        $this->startDate = $date;
    }

    public function setEndDate($date)
    {
        $this->endDate = $date;
    }

    /**
     * Generates the CSV files
     */
    public function generateExport()
    {

        // Concatinate sequence of statements to create and load working tables
        $query = $this->createAllTempTables();

        $this->populateAllTempTables();



        // Extract results from tables into csv files
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
     * @param string $dataFormatter
     * @return null|array
     */
    private function saveCSVfile($dataQuery, $filename, $dataFormatter = null)
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

            if ($offset == 0) {
                file_put_contents($this->exportPath . '/' . $filename . '.csv', ((implode(',', $dataQuery['header'])) . "\n"), FILE_APPEND);
            }

            if (count($data) > 0) {
                $csv = $this->array2Csv($data, null, $dataFormatter);

                file_put_contents($this->exportPath . '/' . $filename . '.csv', $csv, FILE_APPEND);

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
        // DROP all tables if exist before creating them
        $this->clearAllTempTables();

        $query = '';

        $query = $this->createTmpRcoNodMainEventEpisodes();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodPatients();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodPatientCVIStatus();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodePreOpAssessment();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeRefraction();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeDrug();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeIOP();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeBiometry();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodSurgeon();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeDiabeticDiagnosis();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodPostOpComplication();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeOperationCoPathology();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeOperation();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeTreatment();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeTreatmentCataract();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeOperationAnaesthesia();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeOperationIndication();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeOperationComplication();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeVisualAcuity();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->createTmpRcoNodEpisodeDiagnosis();
        Yii::app()->db->createCommand($query)->execute();

        $query = <<<EOL

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
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL

DROP TABLE IF EXISTS tmp_rco_nod_pathology_type;
CREATE TABLE tmp_rco_nod_pathology_type
AS
SELECT
  d.id snomed_disorder_id
, d.term
, LOWER(d.term) lowcase_snomed_term
, CASE LOWER(d.term)
  WHEN 'age related macular degeneration' THEN 1
  WHEN 'amblyopia' THEN 2
  /* no mapping 3 Corneal pathology */
  WHEN 'diabetic retinopathy' THEN 4
  WHEN 'glaucoma' THEN 5
  WHEN 'glaucoma suspect' THEN 6
  /* no mapping 7 High myopia */
  WHEN 'ocular hypertension' THEN 8
  /* no mapping 9 Inherited eye diseases */
  /* no mapping 10 Optic nerve / CNS disease */
  WHEN 'stickler syndrome' THEN 11
  WHEN 'uveitis' THEN 12
  WHEN 'pseudoexfoliation' THEN 13 /* multiple mappings */
  WHEN 'phacodonesis' THEN 13 /* multiple mappings */
  WHEN 'cataracta brunescens' THEN 14 /* cross check Brunescent / white cataract */
  WHEN 'vitreous opacities' THEN 15
  /* no mapping 16 Other macular pathology */
  WHEN 'retinal vascular disorder' THEN 17 /* cross check Other retinal vascular pathology */
  WHEN 'macular hole' THEN 18 /* multiple cross check macular hole */
  WHEN 'full thickness macular hole stage ii' THEN 18 /* multiple cross check macular hole */
  WHEN 'full thickness macular hole stage iii' THEN 18 /* multiple cross check macular hole */
  WHEN 'full thickness macular hole stage iv' THEN 18 /* multiple cross check macular hole */
  WHEN 'epiretinal membrane' THEN 19
  WHEN 'retinal detachment' THEN 20 /* cross check - potential others */
  /* no mapping 21 Previous retinal detachment surgery */
  /* no mapping 22 Vitrectomy */
  /* no mapping 23 previous vitrectomy for FTMH / ERM / other reason */
  /* no mapping 24 Previous laser refractive surgery */
  /* no mapping 25 Previous trabeculectomy */
  ELSE 26 /* Other */
  END nod_id
FROM disorder d;
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL

			DROP TEMPORARY TABLE IF EXISTS tmp_iol_positions;
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL

			CREATE TEMPORARY TABLE tmp_iol_positions (
				`nodcode` INT(10) UNSIGNED NOT NULL,
				`term` VARCHAR(100)
			);
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL


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
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL


		DROP TABLE IF EXISTS tmp_complication_type;
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL


		CREATE TABLE tmp_complication_type (
			`code` INT(10) UNSIGNED NOT NULL,
			`name` VARCHAR(100)
		);
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL


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
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL
                        DROP TEMPORARY TABLE IF EXISTS tmp_complication;
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL


                        CREATE TEMPORARY TABLE tmp_complication (
                                `oe_id` INT(10) UNSIGNED NOT NULL,
                                `oe_desc` VARCHAR(100),
                                `nod_id` INT(10) UNSIGNED NOT NULL,
                                `nod_desc` VARCHAR(100)
                        );
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL


                        INSERT INTO tmp_complication (`oe_id`, `oe_desc`, `nod_id`, `nod_desc` )
                        VALUES
                        (1, 'Eyelid haemorrage/bruising', 2, 'Eyelid haemorrhage / bruising'),
                        (2, 'Conjunctivital chemosis', 1, 'Conjunctival chemosis'),
                        (3, 'Retro bulbar / peribulbar haemorrage', 8, 'Retrobulbar / peribulbar haemorrhage'),
                        (4, 'Globe/optic nerve penetration', 4, 'Globe / optic nerve perforation'),
                        (5, 'Inadequate akinesia', 3, 'Excessive eye movement'),
                        (6, 'Patient pain - Mild', 5, 'Patient discomfort / pain mild;'),
                        (7, 'Patient pain - Moderate', 6, 'Patient discomfort / pain moderate;'),
                        (8, 'Patient pain - Severe', 7, 'Patient discomfort / pain severe;'),
                        (9, 'Systemic problems', 10, 'Systemic problems (bradycardia / hypotension / apnoea etc.)'),
                        (10, 'Operation abandoned due to complication', 11, 'Operation cancelled due to complication'),
                        (11, 'None', 0, 'None'),
                        (12, 'Sub-conjunctival haemorrhage', 9, 'Sub-conjunctival haemorrhage'),
                        (13, 'Other', 12, 'Other');
                        -- (0, '', 99, 'Not recorded');
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL


                        DROP TABLE IF EXISTS tmp_biometry_formula;
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL

                        CREATE TABLE tmp_biometry_formula (
                                `code` INT(10) UNSIGNED NOT NULL,
                                `desc` VARCHAR(100)
                        );
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL


                        INSERT INTO tmp_biometry_formula (`code`, `desc`)
                        VALUES
                        (1, 'SRK/T'),
                        (2, 'Holladay 1'),
                        (3, 'SRK II'),
                        (4, 'HofferQ'),
                        (5, 'Haigis-L (myopic)'),
                        (6, 'Holladay 2'),
                        (7, 'Haigis');

                        DROP TABLE IF EXISTS tmp_episode_diagnosis;
                        CREATE TABLE tmp_episode_diagnosis (
                           `oe_subspecialty_name` VARCHAR(50),
                           `rco_condition_name` VARCHAR(50),
                           `oe_subspecialty_id` INT(10) UNSIGNED NOT NULL,
                           `rco_condition_id` INT(10) UNSIGNED NOT NULL
                        );
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL
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
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL



                    DROP TABLE IF EXISTS tmp_episode_medication_route;
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL


                    CREATE TABLE tmp_episode_medication_route (
                        `oe_route_id` INT(10) UNSIGNED,
                        `oe_route_name` VARCHAR(50),
                        `oe_option_id` INT(10) UNSIGNED DEFAULT NULL,
                        `oe_option_name` VARCHAR(50),
                        `nod_id` INT(10) UNSIGNED DEFAULT NULL,
                        `nod_name` VARCHAR(50),

                        KEY `tmp_episode_mediation_route_oe_route_id` (`oe_route_id`)
                    );
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL


                    INSERT INTO `tmp_episode_medication_route` ( `oe_route_id`, `oe_route_name`, `oe_option_id`, `oe_option_name`, `nod_id`, `nod_name` )
                        VALUES
                        (54, 'Ocular', 1, 'Left', 1, 'Left eye'),
                        (54, 'Ocular', 2, 'Right', 2 , 'Right eye'),
                        (54, 'Ocular', 3, 'Both', 4 , 'Both eyes'),
                        (44, 'Intramuscular', NULL, "", 7, 'Intramuscular injection'),
                        (62, 'Inhalation', NULL, "", 6, 'Inhaled'),
                        (81, 'Intracameral', NULL, "", 5, 'Intracameral'),
                        (40, 'Intradermal', NULL, "", 99, 'Other'),
                        (77, 'Intravitreal', NULL, "", 99, 'Other'),
                        (18, 'Topical', NULL, "", 99, 'Other'),
                        (19, 'n/a', NULL, "", 99, 'Other'),
                        (20, 'Other', NULL, "", 99, 'Other'),
                        (21, 'Auricular', NULL, "", 99, 'Other'),
                        (22, 'Cutaneous', NULL, "", 99, 'Other'),
                        (23, 'Dental', NULL, "", 99, 'Other'),
                        (24, 'Endocervical', NULL, "", 99, 'Other'),
                        (25, 'Endosinusial', NULL, "", 99, 'Other'),
                        (26, 'Endotracheopulmonary', NULL, "", 99, 'Other'),
                        (27, 'Epidural', NULL, "", 99, 'Other'),
                        (28, 'Extraamniotic', NULL, "", 99, 'Other'),
                        (29, 'Gastroenteral', NULL, "", 99, 'Other'),
                        (30, 'Gingival', NULL, "", 99, 'Other'),
                        (31, 'Haemodialysis', NULL, "", 99, 'Other'),
                        (32, 'Intraamniotic', NULL, "", 99, 'Other'),
                        (33, 'Intraarterial', NULL, "", 99, 'Other'),
                        (34, 'Intraarticular', NULL, "", 99, 'Other'),
                        (35, 'Intrabursal', NULL, "", 99, 'Other'),
                        (36, 'Intracardiac', NULL, "", 99, 'Other'),
                        (37, 'Intracavernous', NULL, "", 99, 'Other'),
                        (38, 'Intracervical', NULL, "", 99, 'Other'),
                        (39, 'Intracoronary', NULL, "", 99, 'Other'),
                        (41, 'Intradiscal', NULL, "", 99, 'Other'),
                        (42, 'Intralesional', NULL, "", 99, 'Other'),
                        (43, 'Intralymphatic', NULL, "", 99, 'Other'),
                        (45, 'Intraocular', NULL, "", 99, 'Other'),
                        (46, 'Intraperitoneal', NULL, "", 99, 'Other'),
                        (47, 'Intrapleural', NULL, "", 99, 'Other'),
                        (48, 'Intrasternal', NULL, "", 99, 'Other'),
                        (49, 'Intrathecal', NULL, "", 99, 'Other'),
                        (50, 'Intrauterine', NULL, "", 99, 'Other'),
                        (52, 'Intravesical', NULL, "", 99, 'Other'),
                        (56, 'Buccal', NULL, "", 99, 'Other'),
                        (58, 'Obsolete-Oromucosal other', NULL, "", 99, 'Other'),
                        (59, 'Periarticular', NULL, "", 99, 'Other'),
                        (60, 'Perineural', NULL, "", 99, 'Other'),
                        (67, 'Urethral', NULL, "", 99, 'Other'),
                        (63, 'Route of administration not applicable', NULL, "", 99, 'Other'),
                        (69, 'Obsolete-Intraventricular', NULL, "", 99, 'Other'),
                        (70, 'Body cavity use', NULL, "", 99, 'Other'),
                        (71, 'Haemofiltration', NULL, "", 99, 'Other'),
                        (72, 'Intraosseous', NULL, "", 99, 'Other'),
                        (73, 'Intraventricular cardiac', NULL, "", 99, 'Other'),
                        (74, 'Intracerebroventricular', NULL, "", 99, 'Other'),
                        (75, 'Submucosal rectal', NULL, "", 99, 'Other'),
                        (76, 'Regional perfusion', NULL, "", 99, 'Other'),
                        (78, 'Oromucosal', NULL, "", 99, 'Other'),
                        (79, 'Intraepidermal', NULL, "", 99, 'Other'),
                        (80, 'Epilesional', NULL, "", 99, 'Other'),
                        (82, 'Iontophoresis', NULL, "", 99, 'Other'),
                        (83, 'Intratumoral', NULL, "", 99, 'Other'),
                        (84, 'Subretinal', NULL, "", 99, 'Other'),
                        (85, 'Intestinal use', NULL, "", 99, 'Other'),
                        (51, 'Intravenous', NULL, "", 9, 'Intravenously'),
                        (53, 'Nasal', NULL, "", 8, 'Intranasally'),
                        (44, 'Intramuscular', NULL, "", 7, 'Intramuscular injection'),
                        (55, 'Oral', NULL, "", 12, 'Orally'),
                        (61, 'Rectal', NULL, "", 15, 'Per rectum'),
                        (68, 'Vaginal', NULL, "", 99, 'Other'),
                        (64, 'Subconjunctival', NULL, "", 18, 'Subconjunctival'),
                        (57, 'Sublingual', NULL, "", 19, 'Sub-lingual'),
                        (65, 'Subcutaneous', NULL, "", 17, 'Subcutaneously'),
                        (66, 'Transdermal', NULL, "", 24, 'Trans-cutaneous');

EOL;
        Yii::app()->db->createCommand($query)->execute();

        return '';
    }

    // Refactoring :
    /**
     * This function will call the functions one by one to populate each tmp tables belongs to a csv file
     */
    private function populateAllTempTables()
    {

        $query = $this->populateTmpRcoNodMainEventEpisodes();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeOperation();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodPatients();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodePreOpAssessment();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodPatientCVIStatus();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeRefraction();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeDrug();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeIOP();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeBiometry();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeDiabeticDiagnosis();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodPostOpComplication();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeOperationCoPathology();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeTreatment();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeTreatmentCataract();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeOperationAnaesthesia();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeOperationIndication();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeOperationComplication();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeDiagnosis();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodEpisodeVisualAcuity();
        Yii::app()->db->createCommand($query)->execute();
        $query = $this->populateTmpRcoNodSurgeon();  // Depends on earlier tables being populated.
        Yii::app()->db->createCommand($query)->execute();

        return $query;
    }

    private function clearAllTempTables()
    {
        $cleanQuery = <<<EOL

                DROP TABLE IF EXISTS tmp_rco_nod_main_event_episodes_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_patients_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodePreOpAssessment_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_PatientCVIStatus_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeRefraction_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeMedication_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeIOP_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeBiometry_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_Surgeon_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeDiabeticDiagnosis_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationCoPathology_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodePostOpComplication_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeTreatment_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeTreatmentCataract_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationAnaesthesia_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationComplication_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationIndication_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeTreatment_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeVisualAcuity_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeDiagnoses_{$this->extractIdentifier};

                DROP TEMPORARY TABLE IF EXISTS tmp_complication;
                DROP TEMPORARY TABLE IF EXISTS tmp_iol_positions;
                DROP TABLE IF EXISTS tmp_rco_nod_pathology_type;
                DROP TEMPORARY TABLE IF EXISTS tmp_operation_ids;
                DROP TABLE IF EXISTS tmp_complication_type;
                DROP TABLE IF EXISTS tmp_biometry_formula;
                DROP TABLE IF EXISTS tmp_episode_diagnosis;
                DROP TABLE IF EXISTS tmp_episode_medication_route;
                DROP TABLE IF EXISTS tmp_episode_ids;
                DROP TABLE IF EXISTS tmp_treatment_ids;

EOL;

        Yii::app()->db->createCommand($cleanQuery)->execute();
    }





    /********** Surgeon **********/

    private function createTmpRcoNodSurgeon()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_Surgeon_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_Surgeon_{$this->extractIdentifier} (
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
            INSERT INTO tmp_rco_nod_Surgeon_{$this->extractIdentifier} (
                Surgeonid,
                GMCnumber,
                Title,
                FirstName,
                CurrentGradeId
            )
            SELECT id AS Surgeonid
                 , IFNULL(registration_code, '') AS GMCnumber
                 , IFNULL(title, '') AS Title
                 , IFNULL(first_name, '') AS FirstName
                 , IFNULL(user.doctor_grade_id, '') AS CurrentGradeId
            FROM   user
            WHERE  id IN ( SELECT SurgeonId FROM tmp_rco_nod_EpisodeDiagnoses_{$this->extractIdentifier} WHERE SurgeonId IS NOT NULL
                           UNION
                           SELECT SurgeonId FROM tmp_rco_nod_EpisodeOperationAnaesthesia_{$this->extractIdentifier} WHERE SurgeonId IS NOT NULL
                           UNION
                           SELECT SurgeonId FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier}
                           UNION
                           SELECT AssistantId FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} WHERE AssistantId IS NOT NULL
                           UNION
                           SELECT ConsultantId FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} WHERE ConsultantId IS NOT NULL
                         );
EOL;
        #Yii::app()->db->createCommand($query)->execute();
        return $query;
    }

    private function getSurgeons()
    {

        $query = <<<EOL
                SELECT *
                FROM tmp_rco_nod_Surgeon_{$this->extractIdentifier}
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
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeDiabeticDiagnosis_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeDiabeticDiagnosis_{$this->extractIdentifier} (
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
                    INSERT INTO tmp_rco_nod_EpisodeDiabeticDiagnosis_{$this->extractIdentifier} (
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
                                    THEN IFNULL((DATE_FORMAT(`converted_date`, '%Y') - DATE_FORMAT(p.dob, '%Y') - (DATE_FORMAT(`converted_date`, '00-%m-%d') < DATE_FORMAT(p.dob, '00-%m-%d'))), "")
                                    ELSE ""
				END
                    )
		    AS AgeAtDiagnosis
            FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
            JOIN patient p ON c.patient_id = p.id
            JOIN (select *,
                case
                    when trim(date) regexp '^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$' = 1 then date
                    when trim(date) regexp '^[0-9]{4}-[0-9]{1,2}$' = 1 then concat(date,'-01')
                    when trim(date) regexp '^[0-9]{4}$' = 1 then concat(date,'-01-01')
                    else null
                end converted_date
                from secondary_diagnosis
                ) s
            ON s.patient_id = p.id
            JOIN disorder d ON d.id = s.disorder_id;
EOL;
        return $query;
    }

    private function getEpisodeDiabeticDiagnosis()
    {
        $query = <<<EOL
                SELECT c.oe_event_id as EpisodeId, d.IsDiabetic, d.DiabetesTypeId, d.DiabetesRegimeId, d.AgeAtDiagnosis
                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
                JOIN tmp_rco_nod_EpisodeDiabeticDiagnosis_{$this->extractIdentifier} d ON c.oe_event_id = d.oe_event_id
EOL;


        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'IsDiabetic', 'DiabetesTypeId', 'DiabetesRegimeId', 'AgeAtDiagnosis'),
        );

        $output = $this->saveCSVfile($dataQuery, 'EpisodeDiabeticDiagnosis');

        return $output;
    }

    /********** end of EpisodeDiabeticDiagnosis **********/





    /********** Patient **********/

    /**
     * Create tmp_rco_nod_patients_{$this->extractIdentifier} table
     */
    private function createTmpRcoNodPatients()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_patients_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_patients_{$this->extractIdentifier} (
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
                INSERT INTO tmp_rco_nod_patients_{$this->extractIdentifier} (
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
                        FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
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
                FROM tmp_rco_nod_patients_{$this->extractIdentifier}
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
            DROP TABLE IF EXISTS tmp_rco_nod_PatientCVIStatus_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_PatientCVIStatus_{$this->extractIdentifier} (
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
                INSERT INTO tmp_rco_nod_PatientCVIStatus_{$this->extractIdentifier} (
                        PatientId,
                        date,
                        IsDateApprox,
                        IsCVIBlind,
                        IsCVIPartial )
                SELECT
                poi.patient_id AS PatientId,
                STR_TO_DATE(REPLACE(poi.cvi_status_date, '-00', '-01'), '%Y-%m-%d') AS `Date`,
                (CASE WHEN poi.cvi_status_date LIKE '%-00%' THEN 1 ELSE 0 END) AS IsDateApprox,
                (CASE WHEN poi.cvi_status_id=4 THEN 1 ELSE 0 END) AS IsCVIBlind,
                (CASE WHEN poi.cvi_status_id=3 THEN 1 ELSE 0 END) AS IsCVIPartial
                FROM patient_oph_info poi
                /* Restriction: patients in control events */
                WHERE poi.patient_id IN ( SELECT c.patient_id FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier}  c );
EOL;

        if (Yii::app()->hasModule('OphCoCvi')) {
            $query = <<<EOL
                INSERT INTO tmp_rco_nod_PatientCVIStatus_{$this->extractIdentifier} (
                        PatientId,
                        date,
                        IsDateApprox,
                        IsCVIBlind,
                        IsCVIPartial )
                SELECT ep.patient_id AS PatientId
                     , DATE(e.event_date) AS `Date`
                     , 0 AS IsDateApprox
                     , CASE WHEN cci.is_considered_blind = 1 THEN 1 ELSE 0 END AS IsCVIBlind
                     , CASE WHEN cci.is_considered_blind = 1 THEN 0 ELSE 1 END AS IsCVIPartial
                FROM   episode ep
                JOIN   event e ON e.episode_id = ep.id AND e.deleted = 0
                JOIN   et_ophcocvi_eventinfo cei ON cei.event_id = e.id AND cei.is_draft = 0
                JOIN   et_ophcocvi_clinicinfo cci ON cci.event_id = e.id
                WHERE  ep.patient_id IN ( SELECT c.patient_id FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c );
EOL;
        }

        return $query;
    }

    private function getPatientCviStatus()
    {
        $query = <<<EOL
                SELECT *
                FROM tmp_rco_nod_PatientCVIStatus_{$this->extractIdentifier}
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

DROP TABLE IF EXISTS tmp_rco_nod_main_event_episodes_{$this->extractIdentifier};
CREATE TABLE tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} (
    oe_event_id int(10) NOT NULL,
    patient_id int(10) NOT NULL,
    nod_episode_id int(10) NOT NULL,
    nod_date date NOT NULL,
    oe_event_type_name VARCHAR(40) NOT NULL,
    nod_episode_seq int(10),
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

# Load main control table with ALL operation events within the specified period
INSERT INTO tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} (
  oe_event_id
, patient_id
, nod_episode_id
, nod_date
, oe_event_type_name
, nod_episode_seq
)
SELECT
  ev.id AS oe_event_id
, ep.patient_id AS patient_id
, ep.id AS nod_episode_id
, DATE(ev.event_date) AS nod_date
, et.class_name AS oe_event_type_name
, 1 AS nod_episode_seq
FROM event ev
JOIN episode ep ON ev.episode_id = ep.id
JOIN event_type et ON ev.event_type_id = et.id
WHERE et.class_name = 'OphTrOperationnote'

EOL;

        if ( $this->startDate ) {
            $query .= " AND DATE(ev.event_date) >= STR_TO_DATE('{$this->startDate}', '%Y-%m-%d') ";
        }

        if ( $this->endDate ) {
            $query .= " AND DATE(ev.event_date) <= STR_TO_DATE('{$this->endDate}', '%Y-%m-%d') ";
        }

        $query .= <<<EOL

AND ev.deleted = 0
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL

#Load main control table with ALL OTHER operation note events (using previously identified patients in control table)
INSERT INTO  tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} (
  oe_event_id
, patient_id
, nod_episode_id
, nod_date
, oe_event_type_name
, nod_episode_seq
)
SELECT
  ev.id AS oe_event_id
, ep.patient_id AS patient_id
, ep.id AS nod_episode_id
, DATE(ev.event_date) AS nod_date
, et.class_name AS oe_event_type_name
, 1 AS nod_episode_seq
FROM event ev
JOIN episode ep ON ev.episode_id = ep.id
JOIN event_type et ON ev.event_type_id = et.id
WHERE ep.patient_id IN (SELECT c.patient_id FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c)
AND ev.id NOT IN (SELECT c.oe_event_id FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c)
AND et.class_name = 'OphTrOperationnote'
AND ev.deleted = 0
EOL;
        Yii::app()->db->createCommand($query)->execute();
        $query = <<<EOL

#Load main control table with ALL examination events (using previously identified patients in control table)
INSERT INTO  tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} (
  oe_event_id
, patient_id
, nod_episode_id
, nod_date
, oe_event_type_name
, nod_episode_seq
)
SELECT
  ev.id AS oe_event_id
, ep.patient_id AS patient_id
, ep.id AS nod_episode_id
, DATE(ev.event_date) AS nod_date
, et.class_name AS oe_event_type_name
, 1 AS nod_episode_seq
FROM event ev
JOIN episode ep ON ev.episode_id = ep.id
JOIN event_type et ON ev.event_type_id = et.id
WHERE ep.patient_id IN (SELECT c.patient_id FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c)
AND et.class_name IN ('OphCiExamination', 'OphInBiometry', 'OphDrPrescription')
AND ev.deleted = 0
EOL;
        Yii::app()->db->createCommand($query)->execute();

        return "describe event";
    }

    private function getEpisode()
    {
        $query = <<<EOL
                SELECT c.patient_id as PatientId, c.oe_event_id as EpisodeId, c.nod_date as Date, c.nod_episode_seq as Seq
                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('PatientId', 'EpisodeId', 'Date', 'Seq'),
        );

        $this->saveCSVfile($dataQuery, 'Episode');
    }

    /********** end of Episode **********/





    /********** EpisodePreOpAssessment **********/

    private function createTmpRcoNodEpisodePreOpAssessment()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodePreOpAssessment_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodePreOpAssessment_{$this->extractIdentifier} (
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

INSERT INTO tmp_rco_nod_EpisodePreOpAssessment_{$this->extractIdentifier} (
  oe_event_id,
  Eye,
  IsAbleToLieFlat,
  IsInabilityToCooperate
)
SELECT DISTINCT
c.oe_event_id,
CASE WHEN pl.eye_id IN (1, 3) THEN 'L' ELSE NULL END AS Eye, /* Belt+Brace with WHERE clause or NULL */
CASE WHEN (SELECT COUNT(*) FROM patient_risk_assignment pr WHERE pr.patient_id = c.patient_id AND pr.risk_id = 1) > 0 THEN 0 ELSE 1 END AS IsAbleToLieFlat,
CASE WHEN (SELECT COUNT(*) FROM patient_risk_assignment pr WHERE pr.patient_id = c.patient_id AND pr.risk_id = 4) > 0 THEN 1 ELSE 0 END AS IsInabilityToCooperate
/* Restriction: Start with control events */
FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
/* Join: Associated procedures, Implicit Restriction: Operations with procedures */
JOIN et_ophtroperationnote_procedurelist pl ON pl.event_id = c.oe_event_id
/* Restrict: LEFT/BOTH eyes */
WHERE pl.eye_id IN (1, 3)
/* Group by required as may have multiple procedures on eye */
GROUP BY oe_event_id, Eye, IsAbleToLieFlat, IsInabilityToCooperate;

INSERT INTO tmp_rco_nod_EpisodePreOpAssessment_{$this->extractIdentifier} (
  oe_event_id,
  Eye,
  IsAbleToLieFlat,
  IsInabilityToCooperate
)
SELECT DISTINCT
c.oe_event_id,
CASE WHEN pl.eye_id IN (2, 3) THEN 'R' ELSE NULL END AS Eye, /* Belt+Brace with WHERE clause or NULL */
CASE WHEN (SELECT COUNT(*) FROM patient_risk_assignment pr WHERE pr.patient_id = c.patient_id AND pr.risk_id = 1) > 0 THEN 0 ELSE 1 END AS IsAbleToLieFlat,
CASE WHEN (SELECT COUNT(*) FROM patient_risk_assignment pr WHERE pr.patient_id = c.patient_id AND pr.risk_id = 4) > 0 THEN 1 ELSE 0 END AS IsInabilityToCooperate
/* Restriction: Start with control events */
FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
/* Join: Associated procedures, Implicit Restriction: Operations with procedures */
JOIN et_ophtroperationnote_procedurelist pl ON pl.event_id = c.oe_event_id
/* Restrict: RIGHT/BOTH eyes */
WHERE pl.eye_id IN (2, 3)
/* Group by required as may have multiple procedures on eye */
GROUP BY oe_event_id, Eye, IsAbleToLieFlat, IsInabilityToCooperate;
EOL;
        return $query;
    }

    private function getEpisodePreOpAssessment()
    {

        $query = <<<EOL
                SELECT c.oe_event_id as EpisodeId, p.Eye, p.isAbleToLieFlat, p.IsInabilityToCooperate
                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
                JOIN tmp_rco_nod_EpisodePreOpAssessment_{$this->extractIdentifier} p ON c.oe_event_id = p.oe_event_id
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
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeRefraction_{$this->extractIdentifier};
                CREATE TABLE tmp_rco_nod_EpisodeRefraction_{$this->extractIdentifier} (
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
                INSERT INTO tmp_rco_nod_EpisodeRefraction_{$this->extractIdentifier} (
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
                      rr.sphere AS Sphere,
                      rr.cylinder AS Cylinder,
                      rr.axis AS Axis,
                      '' AS ReadingAdd

                /* Restriction: Start with control events */
                FROM  tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
                JOIN et_ophciexamination_refraction r ON r.event_id = c.oe_event_id
                JOIN ophciexamination_refraction_reading rr ON rr.id = (
                    /* Need to ensure we only get one reading result, ordered by the priority of the type */
                    SELECT single_reading.id
                    FROM ophciexamination_refraction_reading single_reading
                    LEFT JOIN ophciexamination_refraction_type rt
                    ON single_reading.type_id = rt.id
                    WHERE element_id = r.id
                    AND single_reading.eye_id = 1
                    ORDER BY -rt.priority DESC /* Null indicates an "other" type, which negative desc ordering will make last */
                    LIMIT 1
                )

                /* Restrict: LEFT/BOTH eyes */
                WHERE r.eye_id IN (1,3);


                INSERT INTO tmp_rco_nod_EpisodeRefraction_{$this->extractIdentifier} (
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
                      rr.sphere AS Sphere,
                      rr.cylinder AS Cylinder,
                      rr.axis AS Axis,
                      '' AS ReadingAdd

                /* Restriction: Start with control events */
                FROM  tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
                JOIN et_ophciexamination_refraction r ON r.event_id = c.oe_event_id
                JOIN ophciexamination_refraction_reading rr ON rr.id = (
                    /* Need to ensure we only get one reading result, ordered by the priority of the type */
                    SELECT single_reading.id
                    FROM ophciexamination_refraction_reading single_reading
                    LEFT JOIN ophciexamination_refraction_type rt
                    ON single_reading.type_id = rt.id
                    WHERE element_id = r.id
                    AND single_reading.eye_id = 2
                    ORDER BY -rt.priority DESC /* Null indicates an "other" type, which negative desc ordering will make last */
                    LIMIT 1
                )
                /* Restrict: RIGHT/BOTH eyes */
                WHERE r.eye_id IN (2,3);
EOL;

        return $query;
    }

    private function getEpisodeRefraction()
    {
        $query = <<<EOL
                SELECT c.oe_event_id as EpisodeId, r.Eye, r.RefractionTypeId, r.Sphere, r.Cylinder, r.Axis, r.ReadingAdd
                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
                JOIN tmp_rco_nod_EpisodeRefraction_{$this->extractIdentifier} r ON c.oe_event_id = r.oe_event_id
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'Eye', 'RefractionTypeId', 'Sphere', 'Cylinder', 'Axis', 'ReadingAdd'),
        );

        return $this->saveCSVfile($dataQuery, 'EpisodeRefraction');
    }

    /********** end of EpisodeRefraction **********/




    /********** EpisodeDiagnosis **********/

    private function createTmpRcoNodEpisodeDiagnosis()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeDiagnoses_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeDiagnoses_{$this->extractIdentifier} (
                oe_event_id INT(10) NOT NULL,
                Eye CHAR(1) NOT NULL,
                Date DATE DEFAULT NULL,
                SurgeonId INT(10) DEFAULT NULL,
                ConditionId INT(11) DEFAULT NULL,
                DiagnosisTermId BIGINT(20) DEFAULT NULL,
                DiagnosisTermDescription VARCHAR(255)
            );
EOL;

        return $query;
    }


    private function populateTmpRcoNodEpisodeDiagnosis()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeDiagnoses_{$this->extractIdentifier} (
                oe_event_id,
                Eye,
                Date,
                SurgeonId,
                ConditionId,
                DiagnosisTermId,
                DiagnosisTermDescription
            )
            SELECT
                c.oe_event_id
              , CASE eye_id WHEN 1 THEN 'L' WHEN 2 THEN 'R' WHEN 3 THEN 'B' ELSE 'N' END AS eye
              , DATE(
                  IFNULL (
                    (
                      SELECT epv.last_modified_date
                      FROM episode_version epv
                      WHERE epv.id = ep.id
                      AND epv.disorder_id = ep.disorder_id
                      ORDER BY epv.last_modified_date ASC
                      LIMIT 1
                    )
                    , ep.last_modified_date
                  )
                ) AS `Date`
              , IFNULL (
                  (
                    SELECT epv.last_modified_user_id
                    FROM episode_version epv
                    WHERE epv.id = ep.id
                    AND epv.disorder_id = ep.disorder_id
                    ORDER BY epv.last_modified_date ASC
                    LIMIT 1
                  )
                , ep.last_modified_user_id
              ) AS SurgeonId
              , (
                  SELECT rco_condition_id
                  FROM tmp_episode_diagnosis
                  WHERE oe_subspecialty_id = (
                    SELECT ssa.subspecialty_id
                    FROM firm f
                    JOIN service_subspecialty_assignment ssa
                    ON f.service_subspecialty_assignment_id = ssa.id
                    WHERE f.id = ep.firm_id
                  )
                ) AS ConditionId
              , IFNULL(disorder_id,0) AS DiagnosisTermId
              , IFNULL(d.term, '') AS DiagnosisTermDescription
              FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
              JOIN event e
                ON e.id = c.oe_event_id
              LEFT OUTER JOIN episode ep
                ON ep.id = e.episode_id
              LEFT OUTER JOIN disorder d
                ON d.id = ep.disorder_id
              WHERE ep.firm_id IS NOT NULL;
EOL;

        return $query;
    }


    private function getEpisodeDiagnosis()
    {
        $query = <<<EOL
                SELECT c.oe_event_id as EpisodeId, d.Eye, d.Date, d.SurgeonId, d.ConditionId, d.DiagnosisTermId, d.DiagnosisTermDescription
                FROM tmp_rco_nod_EpisodeDiagnoses_{$this->extractIdentifier} d
                JOIN tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c ON d.oe_event_id = c.oe_event_id

EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'Eye', 'Date', 'SurgeonId', 'ConditionId', 'DiagnosisTermId', 'DiagnosisTermDescription'),
        );

        $output =  $this->saveCSVfile($dataQuery, 'EpisodeDiagnosis');

        return $output;
    }

    /********** end of EpisodeDiagnosis **********/





    /********** EpisodeDrug **********/

    private function createTmpRcoNodEpisodeDrug()
    {

        $query = <<<EOL
                DROP TABLE IF EXISTS tmp_rco_nod_EpisodeMedication_{$this->extractIdentifier};
                CREATE TABLE tmp_rco_nod_EpisodeMedication_{$this->extractIdentifier} (
                        oe_event_id INT(10) NOT NULL,
                        Eye CHAR(1) NOT NULL,
                        MedicationId VARCHAR(150) DEFAULT NULL,
                        MedicationRouteId INT(10) UNSIGNED DEFAULT NULL,
                        StartDate VARCHAR(20) DEFAULT NULL,
                        StopDate  VARCHAR(20) DEFAULT NULL,
                        IsAddedByPrescription  TINYINT(1) DEFAULT NULL,
                        IsContinueIndefinitely  TINYINT(1) DEFAULT NULL,
                        IsStartDateApprox TINYINT(1) DEFAULT NULL,
                        inner_event_id INT(10) NOT NULL
                );
EOL;
        return $query;
    }

    private function populateTmpRcoNodEpisodeDrug()
    {
        $query = <<<EOL
                INSERT INTO tmp_rco_nod_EpisodeMedication_{$this->extractIdentifier} (
                    oe_event_id,
                    Eye,
                    MedicationId,
                    MedicationRouteId,
                    StartDate,
                    StopDate,
                    IsAddedByPrescription,
                    IsContinueIndefinitely,
                    IsStartDateApprox,
                    inner_event_id
                  ) SELECT t.oe_event_id AS EpisodeId,
                        (SELECT CASE WHEN m.route_id = 1 THEN 'L' WHEN m.route_id = 2 THEN 'R' WHEN m.route_id = 3 THEN 'B'  ELSE 'N' END) AS Eye,
                        (SELECT CASE WHEN m.medication_id IS NOT NULL THEN (SELECT preferred_term from medication where id = m.medication_id) END) AS MedicationId,

                      ( SELECT CASE
                                 WHEN m.id IS NOT NULL
                                 THEN ( SELECT nod_id FROM tmp_episode_medication_route
                                        WHERE
                                        (m.route_id = tmp_episode_medication_route.oe_route_id AND tmp_episode_medication_route.oe_option_id = m.laterality) OR
                                        (m.route_id = tmp_episode_medication_route.oe_route_id AND tmp_episode_medication_route.oe_option_id IS NULL)
                                      )
                               END
                      ) AS MedicationRouteId,

                        (SELECT CASE WHEN ev2.event_date IS NULL THEN '' ELSE DATE_FORMAT(ev2.event_date,'%Y-%m-%d') END) AS StartDate,
                        (SELECT CASE
                            WHEN LOCATE('day', dd.name) > 0 THEN
                                DATE_FORMAT(DATE_ADD(DATE_FORMAT(ev2.event_date, '%Y-%m-%d'), INTERVAL SUBSTR(dd.name, 1, LOCATE('day', dd.name)-1) DAY), '%Y-%m-%d')
                            WHEN LOCATE('month', dd.name) > 0 THEN
                                DATE_FORMAT(DATE_ADD(DATE_FORMAT(ev2.event_date, '%Y-%m-%d'), INTERVAL SUBSTR(dd.name, 1, LOCATE('month', dd.name)-1) MONTH), '%Y-%m-%d')
                            WHEN LOCATE('week', dd.name) > 0 THEN
                                DATE_FORMAT(DATE_ADD(DATE_FORMAT(ev2.event_date, '%Y-%m-%d'), INTERVAL SUBSTR(dd.name, 1, LOCATE('week', dd.name)-1) WEEK), '%Y-%m-%d')
                           ELSE ''
                        END) AS StopDate,

                        (SELECT CASE WHEN m.usage_type = "OphDrPrescription" THEN 1 ELSE 0 END ) AS IsAddedByPrescription,
                        (SELECT CASE WHEN lower(dd.name) = 'ongoing' THEN 1 ELSE 0 END) AS IsContinueIndefinitely,
                        (SELECT CASE WHEN DAYNAME(m.start_date) IS NULL THEN 1 ELSE 0 END) AS IsStartDateApprox,
                        ev2.id

                    FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} t
                    JOIN event ev ON t.oe_event_id = ev.id
                    JOIN episode ep ON t.patient_id = ep.patient_id
                    JOIN event ev2 ON ev2.episode_id = ep.id
                    JOIN event_medication_use m ON m.event_id = ev2.id
                    LEFT JOIN medication_duration dd ON dd.id = m.duration_id
                    WHERE ( m.usage_type = "OphDrPrescription" AND ev2.deleted = 0 AND (ev.event_type_id != 27 OR ev2.info != 'Draft'));

                DELETE tmp_rco_nod_EpisodeMedication_{$this->extractIdentifier} FROM tmp_rco_nod_EpisodeMedication_{$this->extractIdentifier} t JOIN event e ON t.inner_event_id = e.id WHERE e.info = 'Draft' AND t.inner_event_id != t.oe_event_id;


EOL;

        return $query;
    }

    private function getEpisodeDrug()
    {
        $query = <<<EOL
                SELECT d.oe_event_id as EpisodeId, (SELECT CASE WHEN d.MedicationRouteId = 1 THEN 'L' WHEN d.MedicationRouteId = 2 THEN 'R' WHEN d.MedicationRouteId = 4 THEN 'B'  ELSE 'N' END), d.MedicationId, d.MedicationRouteId, d.StartDate, d.StopDate, d.IsAddedByPrescription, d.IsContinueIndefinitely, d.IsStartDateApprox
                FROM tmp_rco_nod_EpisodeMedication_{$this->extractIdentifier} d
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
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeBiometry_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeBiometry_{$this->extractIdentifier} (
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
                INSERT INTO tmp_rco_nod_EpisodeBiometry_{$this->extractIdentifier} (
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
                                WHEN ophinbiometry_imported_events.device_model = 'IOLMaster 500'  THEN 1
                                WHEN ophinbiometry_imported_events.device_model = 'Haag-Streit LensStar' THEN 2
                                WHEN ophinbiometry_imported_events.device_model = 'Other' THEN 9
                        END
                    ) AS BiometryKeratometerId,
                    (
                        SELECT code
                        FROM tmp_biometry_formula
                        WHERE BINARY tmp_biometry_formula.desc = ophinbiometry_calculation_formula.name
                    ) AS BiometryFormulaId,
                    k1_left AS K1PreOperative,
                    k2_left AS K2PreOperative,
                    k1_axis_left AS AxisK1,
                    ms.k2_axis_left AS AxisK2,
                    ms.acd_left AS ACDepth,
                    ms.snr_left AS SNR

                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c

                JOIN et_ophinbiometry_measurement ms ON c.oe_event_id = ms.event_id

                LEFT JOIN ophinbiometry_imported_events ON c.oe_event_id  = ophinbiometry_imported_events.event_id
                LEFT JOIN et_ophinbiometry_selection ON c.oe_event_id  = et_ophinbiometry_selection.event_id
                        /* Restrict: LEFT/BOTH eyes */
                        AND (et_ophinbiometry_selection.eye_id = 1 OR et_ophinbiometry_selection.eye_id = 3)

                LEFT JOIN ophinbiometry_calculation_formula
                        ON et_ophinbiometry_selection.formula_id_left = ophinbiometry_calculation_formula.id

                WHERE ms.deleted = 0 ;



                INSERT INTO tmp_rco_nod_EpisodeBiometry_{$this->extractIdentifier} (
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
                    k1_axis_left AS AxisK1,
                    ms.k2_axis_left AS AxisK2,
                    ms.acd_left AS ACDepth,
                    ms.snr_left AS SNR

                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c

                JOIN et_ophinbiometry_measurement ms ON c.oe_event_id = ms.event_id

                LEFT JOIN ophinbiometry_imported_events ON c.oe_event_id = ophinbiometry_imported_events.event_id
                LEFT JOIN et_ophinbiometry_selection ON c.oe_event_id  = et_ophinbiometry_selection.event_id
                        /* Restrict: RIGHT/BOTH eyes */
                        AND (et_ophinbiometry_selection.eye_id = 2 OR et_ophinbiometry_selection.eye_id = 3)

                LEFT JOIN ophinbiometry_calculation_formula
                        ON et_ophinbiometry_selection.formula_id_left = ophinbiometry_calculation_formula.id

                WHERE ms.deleted = 0;

EOL;
        return $query;
    }

    private function getEpisodeBiometry()
    {

        $query = <<<EOL
            SELECT  c.oe_event_id,
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
            FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
            JOIN tmp_rco_nod_EpisodeBiometry_{$this->extractIdentifier} b ON c.oe_event_id = b.oe_event_id
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
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeIOP_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeIOP_{$this->extractIdentifier} (
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
            INSERT INTO tmp_rco_nod_EpisodeIOP_{$this->extractIdentifier} (
                oe_event_id,
                Eye,
                Type,
                GlaucomaMedicationStatusId,
                Value
            )
            SELECT
                c.oe_event_id AS EpisodeId,
                'L' AS Eye,
                '' AS Type,
                9 AS GlaucomaMedicationStatusId,
                (oipvr.value + 0.0) AS VALUE

                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c

                JOIN et_ophciexamination_intraocularpressure etoi ON etoi.event_id = c.oe_event_id
                JOIN ophciexamination_intraocularpressure_value oipv ON oipv.element_id = etoi.id
                JOIN ophciexamination_intraocularpressure_reading oipvr ON oipv.reading_id = oipvr.id

                /* Restrict: LEFT/BOTH eyes */
                WHERE oipv.eye_id IN (1,3);


            INSERT INTO tmp_rco_nod_EpisodeIOP_{$this->extractIdentifier} (
                oe_event_id,
                Eye,
                Type,
                GlaucomaMedicationStatusId,
                Value
            )
            SELECT  c.oe_event_id AS EpisodeId,
                    'R' AS Eye,
                    '' AS Type,
                    9 AS GlaucomaMedicationStatusId,
                    (oipvr.value + 0.0) AS VALUE

                    FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
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
                SELECT  c.oe_event_id as EpisodeId, iop.Eye, iop.Type, iop.GlaucomaMedicationStatusId, iop.Value
                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
                JOIN tmp_rco_nod_EpisodeIOP_{$this->extractIdentifier} iop ON c.oe_event_id = iop.oe_event_id
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
            foreach ($data as $row) {
                if (method_exists($this, $dataFormatter)) {
                    $row = $this->$dataFormatter($row);
                }
                fputcsv($df, $row);
            }
        } elseif ($header) {
            fputcsv($df, $header);
        }

        fclose($df);
        return ob_get_clean();
    }





    /********** EpisodePostOpComplication **********/

    private function createTmpRcoNodPostOpComplication()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodePostOpComplication_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodePostOpComplication_{$this->extractIdentifier} (
                    oe_event_id INT(10) NOT NULL,
                    OperationId INT(10) NOT NULL,
                    Eye CHAR(1) NOT NULL,
                    ComplicationTypeId INT(10) DEFAULT NULL,
                    ComplicationTypeDescription VARCHAR(64) DEFAULT NULL
            );
EOL;
        return $query;
    }

    private function populateTmpRcoNodPostOpComplication()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodePostOpComplication_{$this->extractIdentifier} (
                oe_event_id,
                OperationId,
                Eye,
                ComplicationTypeId,
                ComplicationTypeDescription
            )
            SELECT * FROM (
              SELECT
                  c.oe_event_id
                  , (
                  /* Look up most recent operation outer query post op complication for same oe_episode */
                  SELECT eon.id
                  /* Start with same oe_episode as examination event (correlated from outer query) */
                  FROM event eon
                  JOIN event_type et
                      ON et.id = eon.event_type_id
                  /* Correlated operation notes to outer query for same oe_episode */
                  WHERE eon.episode_id = cev.episode_id
                  AND et.class_name = 'OphTrOperationnote'
                  AND eon.deleted = 0
                  /* Restrict to operations on or before examination post op complication date */
                  AND eon.event_date <= cev.event_date
                  ORDER BY eon.event_date DESC
                  LIMIT 1
                  ) AS OperationId
                  , 'L' AS Eye
                  , poc.code AS ComplicationTypeId
                  , poc.name AS ComplicationTypeDescription
                  FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
                  JOIN et_ophciexamination_postop_complications epoc
                      ON epoc.event_id = c.oe_event_id
                  JOIN ophciexamination_postop_et_complications epoce
                      ON epoc.id = epoce.element_id
                  JOIN ophciexamination_postop_complications poc
                      ON epoce.complication_id = poc.id
                  /* Look up original oe_events to deterine oe_episode */
                  JOIN event cev
                      ON cev.id = c.oe_event_id
                  WHERE epoce.eye_id IN (1, 3) /* 1 = LEFT EYE, 3 = BOTH EYES */

                  UNION ALL

                  SELECT
                  c.oe_event_id
                  , (
                  /* Look up most recent operation outer query post op complication for same oe_episode */
                  SELECT eon.id
                  /* Start with same oe_episode as examination event (correlated from outer query) */
                  FROM event eon
                  JOIN event_type et
                    ON et.id = eon.event_type_id
                  /* Correlated operation notes to outer query for same oe_episode */
                  WHERE eon.episode_id = cev.episode_id
                  AND et.class_name = 'OphTrOperationnote'
                  AND eon.deleted = 0
                  /* Restrict to operations on or before examination post op complication date */
                  AND eon.event_date <= cev.event_date
                  ORDER BY eon.event_date DESC
                  LIMIT 1
                  ) AS OperationId
                  , 'R' AS Eye
                  , poc.code AS ComplicationTypeId
                  , poc.name AS ComplicationTypeDescription
                  FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
                  JOIN et_ophciexamination_postop_complications epoc
                      ON epoc.event_id = c.oe_event_id
                  JOIN ophciexamination_postop_et_complications epoce
                      ON epoc.id = epoce.element_id
                  JOIN ophciexamination_postop_complications poc
                      ON epoce.complication_id = poc.id
                  /* Look up original oe_events to deterine oe_episode */
                  JOIN event cev
                      ON cev.id = c.oe_event_id
                  WHERE epoce.eye_id IN (2, 3) /* 2 = RIGHT EYE, 3 = BOTH EYES */) a
                  WHERE a.OperationId IS NOT NULL;
EOL;
        return $query;
    }

    private function getEpisodePostOpComplication()
    {

        $query = <<<EOL
                SELECT c.oe_event_id as EpisodeId, c.oe_event_id as OperationId, p.Eye, p.ComplicationTypeId
                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
                JOIN tmp_rco_nod_EpisodePostOpComplication_{$this->extractIdentifier} p ON c.oe_event_id = p.oe_event_id

EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('EpisodeId', 'OperationId', 'Eye', 'ComplicationTypeId', 'ComplicationTypeDescription'),
        );

        $output = $this->saveCSVfile($dataQuery, 'EpisodePostOpComplication');

        return $output;
    }

    /********** end of EpisodePostOpComplication **********/





    /********** EpisodeOperationCoPathology **********/

    private function createTmpRcoNodEpisodeOperationCoPathology()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationCoPathology_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeOperationCoPathology_{$this->extractIdentifier} (
                oe_event_id INT(10) NOT NULL,
                Eye CHAR(1) NOT NULL,
                CoPathologyId INT(10) DEFAULT NULL,
                DisorderId BIGINT(20) DEFAULT NULL,
                DisorderDescription VARCHAR(255) DEFAULT NULL
            );
EOL;
            return $query;
    }

    private function populateTmpRcoNodEpisodeOperationCoPathology()
    {
        $query = <<<EOL

INSERT INTO tmp_rco_nod_EpisodeOperationCoPathology_{$this->extractIdentifier} (
  oe_event_id
, Eye
, CoPathologyId
, DisorderId
, DisorderDescription
)
/* CoPathology: LEFT Previous Examination Diagnoses XX – on or prior to operation "Listed Date" */
SELECT
  op.oe_event_id
, CASE WHEN edl.eye_id IN (1, 3) THEN 'L' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Join: all OE_epispodes relating to patient */
JOIN episode ep
  ON ep.patient_id = op.patient_id
/* Join: all OE_event relating to patient */
JOIN event ev
  ON ev.episode_id = ep.id
/* Join: Examination Diagnosis (container) */
JOIN et_ophciexamination_diagnoses ed
  ON ed.event_id = ev.id
/* Join: Examination Diagnosis (list) */
JOIN ophciexamination_diagnosis edl
  ON edl.element_diagnoses_id = ed.id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (LOJ used to cause error if values not mapped (but they should be above) */
LEFT OUTER JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = edl.disorder_id
/* Restrict: only OE_event occuring on or prior to operation "Listed Date" */
WHERE ev.event_date <= op.ListedDate
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
AND edl.eye_id IN (1, 3)
UNION
/* CoPathology: RIGHT Previous Examination Diagnoses XX – on or prior to operation "Listed Date" */
SELECT
  op.oe_event_id
, CASE WHEN edl.eye_id IN (2, 3) THEN 'R' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Join: all OE_epispodes relating to patient */
JOIN episode ep
  ON ep.patient_id = op.patient_id
/* Join: all OE_event relating to patient */
JOIN event ev
  ON ev.episode_id = ep.id
/* Join: Examination Diagnosis (container) */
JOIN et_ophciexamination_diagnoses ed
  ON ed.event_id = ev.id
/* Join: Examination Diagnosis (list) */
JOIN ophciexamination_diagnosis edl
  ON edl.element_diagnoses_id = ed.id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (LOJ used to cause error if values not mapped (but they should be above) */
LEFT OUTER JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = edl.disorder_id
/* Restrict: only OE_event occuring on or prior to operation "Listed Date" */
WHERE ev.event_date <= op.ListedDate
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
AND edl.eye_id IN (2, 3)
UNION
/* CoPathology: LEFT This Operation Booking Diagnosis*/
SELECT
  op.oe_event_id
, CASE WHEN pl.eye_id IN (1, 3) THEN 'L' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Join: Look up PROCEDURE_LIST (containers)  */
/* Cardinality: On investigation et_ophtroperationnote_procedurelist is a logical bucket for procedures on the */
/* on the LEFT Eye or the RIGHT Eye. Therefore if procedures were carried our on both eyes then */
/* two et_ophtroperationnote_procedurelist records would exist each with intersection records */
JOIN et_ophtroperationnote_procedurelist pl
    ON pl.event_id = op.oe_event_id
/* Join: Get associated Booking Event DISORDERS - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
JOIN et_ophtroperationbooking_diagnosis opd
    ON opd.event_id = pl.booking_event_id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (LOJ used to cause error if values not mapped (but they should be above) */
LEFT OUTER JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = opd.disorder_id
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
WHERE pl.eye_id IN (1, 3)
UNION
/* CoPathology: RIGHT This Operation Booking Diagnosis*/
SELECT
  op.oe_event_id
, CASE WHEN pl.eye_id IN (2, 3) THEN 'R' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Join: Look up PROCEDURE_LIST (containers)  */
/* Cardinality: On investigation et_ophtroperationnote_procedurelist is a logical bucket for procedures on the */
/* on the LEFT Eye or the RIGHT Eye. Therefore if procedures were carried our on both eyes then */
/* two et_ophtroperationnote_procedurelist records would exist each with intersection records */
JOIN et_ophtroperationnote_procedurelist pl
    ON pl.event_id = op.oe_event_id
/* Join: Get associated Booking Event DISORDERS - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
JOIN et_ophtroperationbooking_diagnosis opd
    ON opd.event_id = pl.booking_event_id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (LOJ used to cause error if values not mapped (but they should be above) */
LEFT OUTER JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = opd.disorder_id
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
WHERE pl.eye_id IN (2, 3)
UNION
/* CoPathology: LEFT This OE_Episode Diagnosis*/
SELECT
  op.oe_event_id
, CASE WHEN ep.eye_id IN (1, 3) THEN 'L' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Lookup: Event for this operation */
JOIN event ev
  ON ev.id = op.oe_event_id
/* Lookup: THIS Episode for this operation */
JOIN episode ep
  ON ep.id = ev.episode_id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (LOJ used to cause error if values not mapped (but they should be above) */
LEFT OUTER JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = ep.disorder_id
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
WHERE ep.eye_id IN (1, 3)
/* Restrict: Diagnosis made */
AND ep.disorder_id IS NOT NULL
UNION
/* CoPathology: RIGHT This OE_Episode Diagnosis*/
SELECT
  op.oe_event_id
, CASE WHEN ep.eye_id IN (2, 3) THEN 'R' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Lookup: Event for this operation */
JOIN event ev
  ON ev.id = op.oe_event_id
/* Lookup: THIS Episode for this operation */
JOIN episode ep
  ON ep.id = ev.episode_id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (LOJ used to cause error if values not mapped (but they should be above) */
LEFT OUTER JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = ep.disorder_id
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
WHERE ep.eye_id IN (2, 3)
/* Restrict: Diagnosis made */
AND ep.disorder_id IS NOT NULL
UNION
/* CoPathology: LEFT Other Opthalmic + Systemic Diagnoses */
SELECT
  op.oe_event_id
, CASE WHEN sd.eye_id IN (1, 3) OR sd.eye_id IS NULL THEN 'L' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Lookup: Other Opthalmic + Systemic Diagnoses (eye_id=null) */
JOIN secondary_diagnosis sd
  ON sd.patient_id = op.patient_id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (LOJ used to cause error if values not mapped (but they should be above) */
LEFT OUTER JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = sd.disorder_id
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
WHERE (sd.eye_id IN (1, 3) OR sd.eye_id IS NULL /* other Systemic Diagnoses*/)
UNION
/* CoPathology: LEFT Other Opthalmic + Systemic Diagnoses */
SELECT
  op.oe_event_id
, CASE WHEN sd.eye_id IN (2, 3) OR sd.eye_id IS NULL THEN 'R' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Lookup: Other Opthalmic + Systemic Diagnoses (eye_id=null) */
JOIN secondary_diagnosis sd
  ON sd.patient_id = op.patient_id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (LOJ used to cause error if values not mapped (but they should be above) */
LEFT OUTER JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = sd.disorder_id
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
WHERE (sd.eye_id IN (2, 3) OR sd.eye_id IS NULL /* other Systemic Diagnoses*/)
UNION
/* CoPathology: LEFT-DIAGNOSIS-1 Previous Injection management – on or prior to operation "Listed Date" */
SELECT
  op.oe_event_id
, 'L' AS Eye
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Join: all OE_epispodes relating to patient */
JOIN episode ep
  ON ep.patient_id = op.patient_id
/* Join: all OE_event relating to patient */
JOIN event ev
  ON ev.episode_id = ep.id
/* Join: all Injection events relating to patient */
JOIN et_ophciexamination_injectionmanagementcomplex inj
  ON inj.event_id = ev.id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (hard join if diagnosis not made/NULL) */
JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = inj.left_diagnosis1_id
/* Restrict: only OE_event occuring on or prior to operation "Listed Date" */
WHERE ev.event_date <= op.ListedDate
UNION
/* CoPathology: LEFT-DIAGNOSIS-2 Previous Injection management – on or prior to operation "Listed Date" */
SELECT
  op.oe_event_id
, 'L' AS Eye
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Join: all OE_epispodes relating to patient */
JOIN episode ep
  ON ep.patient_id = op.patient_id
/* Join: all OE_event relating to patient */
JOIN event ev
  ON ev.episode_id = ep.id
/* Join: all Injection events relating to patient */
JOIN et_ophciexamination_injectionmanagementcomplex inj
  ON inj.event_id = ev.id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (hard join if diagnosis not made/NULL) */
JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = inj.left_diagnosis2_id
/* Restrict: only OE_event occuring on or prior to operation "Listed Date" */
WHERE ev.event_date <= op.ListedDate
UNION
/* CoPathology: RIGHT-DIAGNOSIS-1 Previous Injection management – on or prior to operation "Listed Date" */
SELECT
  op.oe_event_id
, 'R' AS Eye
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Join: all OE_epispodes relating to patient */
JOIN episode ep
  ON ep.patient_id = op.patient_id
/* Join: all OE_event relating to patient */
JOIN event ev
  ON ev.episode_id = ep.id
/* Join: all Injection events relating to patient */
JOIN et_ophciexamination_injectionmanagementcomplex inj
  ON inj.event_id = ev.id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (hard join if diagnosis not made/NULL) */
JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = inj.right_diagnosis1_id
/* Restrict: only OE_event occuring on or prior to operation "Listed Date" */
WHERE ev.event_date <= op.ListedDate
UNION
/* CoPathology: RIGHT-DIAGNOSIS-2 Previous Injection management – on or prior to operation "Listed Date" */
SELECT
  op.oe_event_id
, 'R' AS Eye
, np.nod_id AS CoPathologyId
, np.snomed_disorder_id
, np.term
/* Start from the operation note being reported */
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
/* Join: all OE_epispodes relating to patient */
JOIN episode ep
  ON ep.patient_id = op.patient_id
/* Join: all OE_event relating to patient */
JOIN event ev
  ON ev.episode_id = ep.id
/* Join: all Injection events relating to patient */
JOIN et_ophciexamination_injectionmanagementcomplex inj
  ON inj.event_id = ev.id
/* Lookup: RCO NOD CoPathology Code from SnomedCT Diagnosis_id (hard join if diagnosis not made/NULL) */
JOIN tmp_rco_nod_pathology_type np
  ON np.snomed_disorder_id = inj.right_diagnosis2_id
/* Restrict: only OE_event occuring on or prior to operation "Listed Date" */
WHERE ev.event_date <= op.ListedDate
UNION
/* Op-Procedures: LEFT 23 (previous vitrectomy for FTMH / ERM / other reason) or 25 (Previous trabeculectomy) */
SELECT
  op.oe_event_id AS OperationId
, CASE WHEN proc_list.eye_id IN (1, 3) THEN 'L' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, if (element_type.name = 'Trabeculectomy', 25, 23)  AS CoPathologyId
, NULL
, NULL
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
JOIN tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c ON c.patient_id = op.patient_id
JOIN et_ophtroperationnote_procedurelist AS proc_list ON proc_list.event_id = c.oe_event_id
JOIN ophtroperationnote_procedurelist_procedure_assignment AS proc_list_asgn ON proc_list_asgn.procedurelist_id = proc_list.id
JOIN proc ON proc_list_asgn.proc_id = proc.id
JOIN ophtroperationnote_procedure_element ON ophtroperationnote_procedure_element.procedure_id = proc.id
JOIN element_type ON ophtroperationnote_procedure_element.element_type_id = element_type.id
WHERE element_type.name IN ('Vitrectomy', 'Trabeculectomy')
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
AND proc_list.eye_id IN (1, 3)
AND c.nod_date < op.ListedDate  /* Do NOT include operations on same day. */
UNION
/* Op-Procedures: RIGHT 23 (previous vitrectomy for FTMH / ERM / other reason) or 25 (Previous trabeculectomy) */
SELECT
  op.oe_event_id AS OperationId
, CASE WHEN proc_list.eye_id IN (2, 3) THEN 'R' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, if (element_type.name = 'Trabeculectomy', 25, 23)  AS CoPathologyId
, NULL
, NULL
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
JOIN tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c ON c.patient_id = op.patient_id
JOIN et_ophtroperationnote_procedurelist AS proc_list ON proc_list.event_id = c.oe_event_id
JOIN ophtroperationnote_procedurelist_procedure_assignment AS proc_list_asgn ON proc_list_asgn.procedurelist_id = proc_list.id
JOIN proc ON proc_list_asgn.proc_id = proc.id
JOIN ophtroperationnote_procedure_element ON ophtroperationnote_procedure_element.procedure_id = proc.id
JOIN element_type ON ophtroperationnote_procedure_element.element_type_id = element_type.id
WHERE element_type.name IN ('Vitrectomy', 'Trabeculectomy')
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
AND proc_list.eye_id IN (2, 3)
AND c.nod_date < op.ListedDate  /* Do NOT include operations on same day. */
UNION
/* Op-Procedures: LEFT 21 (Previous retinal detachment surgery) */
SELECT
  op.oe_event_id AS OperationId
, CASE WHEN proc_list.eye_id IN (1, 3) THEN 'L' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, 21 AS CoPathologyId
, NULL
, NULL
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
JOIN tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c ON c.patient_id = op.patient_id
JOIN et_ophtroperationnote_procedurelist AS proc_list ON proc_list.event_id = c.oe_event_id
JOIN ophtroperationnote_procedurelist_procedure_assignment AS proc_list_asgn ON proc_list_asgn.procedurelist_id = proc_list.id
JOIN proc ON proc_list_asgn.proc_id = proc.id
JOIN procedure_benefit ON procedure_benefit.proc_id = proc.id
JOIN benefit ON procedure_benefit.benefit_id = benefit.id
WHERE benefit.name = 'to prevent retinal detachment'
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
AND proc_list.eye_id IN (1, 3)
AND c.nod_date < op.ListedDate  /* Do NOT include operations on same day. */
UNION
/* Op-Procedures: RIGHT 21 (Previous retinal detachment surgery) */
SELECT
  op.oe_event_id AS OperationId
, CASE WHEN proc_list.eye_id IN (2, 3) THEN 'R' ELSE NULL END AS Eye /* Belt+Brace with WHERE clause or NULL */
, 21 AS CoPathologyId
, NULL
, NULL
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
JOIN tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c ON c.patient_id = op.patient_id
JOIN et_ophtroperationnote_procedurelist AS proc_list ON proc_list.event_id = c.oe_event_id
JOIN ophtroperationnote_procedurelist_procedure_assignment AS proc_list_asgn ON proc_list_asgn.procedurelist_id = proc_list.id
JOIN proc ON proc_list_asgn.proc_id = proc.id
JOIN procedure_benefit ON procedure_benefit.proc_id = proc.id
JOIN benefit ON procedure_benefit.benefit_id = benefit.id
WHERE benefit.name = 'to prevent retinal detachment'
/* Restrict: Relevant Eye or Both as per SELECT CASE Clause */
AND proc_list.eye_id IN (2, 3)
AND c.nod_date < op.ListedDate  /* Do NOT include operations on same day. */
UNION
/* Examination: LEFT 14 Brunescent / white cataract */
SELECT
  op.oe_event_id
, 'L' AS Eye
, 14 AS CoPathologyId
, NULL
, NULL
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
JOIN tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c ON c.patient_id = op.patient_id
JOIN et_ophciexamination_anteriorsegment a
  ON a.event_id = c.oe_event_id
WHERE (left_cortical_id = 4 OR left_nuclear_id = 4)
AND c.nod_date <= op.ListedDate
UNION
/* Examination: RIGHT 14 Brunescent / white cataract */
SELECT
  op.oe_event_id
, 'R' AS Eye
, 14 AS CoPathologyId
, NULL
, NULL
FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op
JOIN tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c ON c.patient_id = op.patient_id
JOIN et_ophciexamination_anteriorsegment a
  ON a.event_id = c.oe_event_id
WHERE (right_cortical_id = 4 OR right_nuclear_id = 4)
AND c.nod_date <= op.ListedDate
;
EOL;
        return $query;
    }


    private function getEpisodeOperationCoPathology()
    {

        $query = <<<EOL
                SELECT p.oe_event_id as OperationId, p.Eye, p.CoPathologyId, p.DisorderId, p.DisorderDescription
                FROM tmp_rco_nod_EpisodeOperationCoPathology_{$this->extractIdentifier} p

EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('OperationId', 'Eye', 'CoPathologyId', 'DisorderId', 'DisorderDescription'),
        );

        $output = $this->saveCSVfile($dataQuery, 'EpisodeOperationCoPathology');

        return $output;
    }

    /********** end of EpisodeOperationCoPathology **********/





    /********** EpisodeTreatmentCataract **********/

    private function createTmpRcoNodEpisodeTreatmentCataract()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeTreatmentCataract_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeTreatmentCataract_{$this->extractIdentifier} (
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
            INSERT INTO tmp_rco_nod_EpisodeTreatmentCataract_{$this->extractIdentifier} (
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
                , if (
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
              , if (
                  oci.name = 'Limbal'
                , 5
                , if (
                    oci.name = 'Scleral'
                  , 8
                  , 4
                  )
                ) AS IncisionSiteId
              , oc.length AS IncisionLengthId
              , 4 AS IncisionPlanesId /* TODO what was #unkown about in original implementation */
              , oc.meridian AS IncisionMeridean
              , if (
                  oc.pupil_size = 'Small'
                , 1
                , if (
                    oc.pupil_size = 'Medium'
                    , 2
                    , if (
                        oc.pupil_size = 'Large'
                      , 3
                      , NULL
                    )
                  )
                ) AS PupilSizeId
                , ocpt.nodcode AS IOLPositionId
                , olt.display_name AS IOLModelId
                , oc.iol_power AS IOLPower
                , oc.predicted_refraction AS PredictedPostOperativeRefraction
                , '' AS WoundClosureId
            /* Restriction: Start with treatment records (processed previously), seeded from control events */
            FROM tmp_rco_nod_EpisodeTreatment_{$this->extractIdentifier} ct
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
            LEFT OUTER JOIN ophinbiometry_lenstype_lens olt
              ON olt.id = oc.iol_type_id

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
FROM tmp_rco_nod_EpisodeTreatmentCataract_{$this->extractIdentifier} tc

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





    /*********** EpisodeOperationAnaesthesia ****************/

    private function createTmpRcoNodEpisodeOperationAnaesthesia()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationAnaesthesia_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeOperationAnaesthesia_{$this->extractIdentifier} (
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

    private function populateTmpRcoNodEpisodeOperationAnaesthesia()
    {
        $query = "INSERT INTO tmp_rco_nod_EpisodeOperationAnaesthesia_{$this->extractIdentifier}(
                      oe_event_id,
                      AnaesthesiaTypeId,
                      AnaesthesiaNeedle,
                      Sedation,
                      SurgeonId,
                      ComplicationId
                  )
SELECT oe_event_id
     , CASE
         WHEN at_list NOT LIKE '%,LA,%' AND at_list NOT LIKE '%,GA,%' AND at_list LIKE '%,NoA,%'
         THEN 0
         WHEN at_list NOT LIKE '%,LA,%' AND at_list LIKE '%,GA,%' AND at_list NOT LIKE '%,NoA,%'
         THEN 1
         WHEN at_list LIKE '%,LA,%' AND at_list NOT LIKE '%,GA,%' AND at_list NOT LIKE '%,NoA,%' AND ad_list NOT LIKE '%,Topical%'
         THEN 2
         WHEN at_list LIKE '%,LA,%' AND at_list LIKE '%,GA,%' AND at_list NOT LIKE '%,NoA,%'
         THEN 3
         WHEN at_list LIKE '%,LA,%' AND at_list NOT LIKE '%,GA,%' AND at_list NOT LIKE '%,NoA,%' AND ad_list LIKE '%,Topical,%' AND ad_list NOT LIKE '%,Peribulbar,%' AND ad_list NOT LIKE '%,Retrobulbar,%' AND ad_list NOT LIKE '%,Subtenons,%' AND ad_list NOT LIKE '%,Subconjunctival,%' AND ad_list NOT LIKE '%,Topical and intracameral,%' AND ad_list NOT LIKE '%,Other,%'
         THEN 4
         WHEN at_list LIKE '%,LA,%' AND at_list NOT LIKE '%,GA,%' AND at_list NOT LIKE '%,NoA,%' AND ad_list LIKE '%,Topical%' AND (ad_list LIKE '%,Peribulbar,%' OR ad_list LIKE '%,Retrobulbar,%' OR ad_list LIKE '%,Subtenons,%' OR ad_list LIKE '%,Subconjunctival,%' OR ad_list LIKE '%,Topical and intracameral,%' OR ad_list LIKE '%,Other,%')
         THEN 5
         ELSE 9
       END AnaesthesiaTypeId
     , CASE
         WHEN ad_list NOT LIKE '%,Peribulbar,%' AND ad_list NOT LIKE '%,Retrobulbar,%' AND ad_list NOT LIKE '%,Subtenons,%'
         THEN 0
         WHEN ad_list LIKE '%,Peribulbar,%' AND ad_list NOT LIKE '%,Retrobulbar,%' AND ad_list NOT LIKE '%,Subtenons,%'
         THEN 1
         WHEN ad_list NOT LIKE '%,Peribulbar,%' AND ad_list LIKE '%,Retrobulbar,%' AND ad_list NOT LIKE '%,Subtenons,%'
         THEN 2
         WHEN ad_list NOT LIKE '%,Peribulbar,%' AND ad_list NOT LIKE '%,Retrobulbar,%' AND ad_list LIKE '%,Subtenons,%'
         THEN 3
         WHEN ad_list LIKE '%,Peribulbar,%' AND ad_list LIKE '%,Retrobulbar,%' AND ad_list NOT LIKE '%,Subtenons,%'
         THEN 4
         WHEN ad_list LIKE '%,Peribulbar,%' AND ad_list NOT LIKE '%,Retrobulbar,%' AND ad_list LIKE '%,Subtenons,%'
         THEN 5
         WHEN ad_list NOT LIKE '%,Peribulbar,%' AND ad_list LIKE '%,Retrobulbar,%' AND ad_list LIKE '%,Subtenons,%'
         THEN 6
         WHEN ad_list LIKE '%,Peribulbar,%' AND ad_list LIKE '%,Retrobulbar,%' AND ad_list LIKE '%,Subtenons,%'
         THEN 7
         ELSE 9
       END AnaesthesiaNeedle
     , CASE
         WHEN at_list NOT LIKE '%,Sed,%'
         THEN 0
         WHEN at_list LIKE '%,Sed,%' AND at_list NOT LIKE '%,LA,%' AND at_list NOT LIKE '%,GA,%' AND at_list NOT LIKE '%,NoA,%'
         THEN 1
         WHEN at_list LIKE '%,Sed,%' AND (at_list LIKE '%,LA,%' OR at_list LIKE '%,GA,%') AND at_list NOT LIKE '%,NoA,%'
         THEN 2
         ELSE 9
       END Sedation
     , SurgeonId
     , ComplicationId
FROM   ( SELECT    a.event_id oe_event_id
                 , CASE
                     WHEN a.anaesthetist_id = 2
                     THEN s.surgeon_id
                     ELSE NULL
                   END SurgeonId
                 , ( SELECT tmp_complication.nod_id
                     FROM   tmp_complication WHERE oe_id = acs.id
                   ) ComplicationId
                 , ( SELECT concat(',', IFNULL(group_concat(at.code separator ','), 'NULL'), ',')
                     FROM   ophtroperationnote_anaesthetic_anaesthetic_type aat
                     JOIN   anaesthetic_type at ON at.id = aat.anaesthetic_type_id
                     WHERE  aat.et_ophtroperationnote_anaesthetic_id = a.id
                   ) at_list
                 , ( SELECT concat(',', IFNULL(group_concat(ad.name separator ','), 'NULL'), ',')
                     FROM   ophtroperationnote_anaesthetic_anaesthetic_delivery aad
                     JOIN   anaesthetic_delivery ad ON ad.id = aad.anaesthetic_delivery_id
                     WHERE  aad.et_ophtroperationnote_anaesthetic_id = a.id
                   ) ad_list
         FROM      et_ophtroperationnote_anaesthetic a
         JOIN      ophtroperationnote_anaesthetic_anaesthetic_complication ac ON a.id = ac.et_ophtroperationnote_anaesthetic_id
         JOIN      ophtroperationnote_anaesthetic_anaesthetic_complications acs ON ac.anaesthetic_complication_id = acs.id
         JOIN      tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c ON c.oe_event_id = a.event_id
         LEFT JOIN et_ophtroperationnote_surgeon s ON s.event_id = a.event_id
       ) x;";

        return $query;
    }

    private function getEpisodeOperationAnaesthesia()
    {
        $query = "SELECT oe_event_id AS OperationId, AnaesthesiaTypeId, AnaesthesiaNeedle, Sedation, SurgeonId, ComplicationId
                    FROM tmp_rco_nod_EpisodeOperationAnaesthesia_{$this->extractIdentifier}";

        $dataQuery = array(
            'query' => $query,
            'header' => array('OperationId', 'AnaesthesiaTypeId', 'AnaesthesiaNeedle', 'Sedation', 'SurgeonId', 'ComplicationId'),
        );

        return $this->saveCSVfile($dataQuery, 'EpisodeOperationAnaesthesia');
    }

    /********* end of EpisodeOperationAnaesthesia***********/





    /********** EpisodeTreatment **********/

    private function createTmpRcoNodEpisodeTreatment()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeTreatment_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeTreatment_{$this->extractIdentifier} (
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
            INSERT INTO tmp_rco_nod_EpisodeTreatment_{$this->extractIdentifier} (
                oe_event_id
                ,TreatmentId
                ,OperationId
                ,Eye
                ,TreatmentTypeId
                ,TreatmentTypeDescription
                )
                /* Procedures for LEFT eye */
            SELECT
                c.oe_event_id
                /* Note pa.id unique for each operation<->procedure intersection record */
                /* However the procedure may be for BOTH EYES and the RCO needs this splitting out to two Treatment records LEFT + RIGHT */
                /* We are creating "high range" Treatment IDs for LEFT eye only by adding 1,000,000,000,000) to the number-space */
                ,  v.l_eye_offset + pa.id AS TreatmentId /* LEFT Eye so add max rox offset plus 10,000 */
                , c.oe_event_id AS OperationId
                , 'L' AS Eye
                , p.snomed_code AS TreatmentTypeId
                , p.snomed_term AS TreatmentTypeDescription
                /* Restriction: Start with control events */

                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
                /* Join: Look up PROCEDURE_LIST (containers) - (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
                /* Cardinality: On investigation et_ophtroperationnote_procedurelist is a logical bucket for procedures on the */
                /* on the LEFT Eye or the RIGHT Eye. Therefore if procedures were carried our on both eyes then */
                /* two et_ophtroperationnote_procedurelist records would exist each with intersection records */
                /* (ophtroperationnote_procedurelist_procedure_assignment) to the lookup procedure (proc) */
                JOIN et_ophtroperationnote_procedurelist pl
                  ON pl.event_id = c.oe_event_id
                /* Join: Look up Procedure List ITEMS (intersection table to proc) - (LOJ used to return nulls if data problems) */
                JOIN ophtroperationnote_procedurelist_procedure_assignment pa
                  ON pa.procedurelist_id = pl.id
                /* Join: Look up PROCEDURE DETAIL (LOJ used to return nulls if data problems (as opposed to loosing parent rows)) */
                LEFT OUTER JOIN proc p
                  ON p.id = pa.proc_id
                CROSS JOIN (
                    SELECT MAX(os.id)+10000 l_eye_offset
                    FROM ophtroperationnote_procedurelist_procedure_assignment os
                ) AS v
                /* Restrict: Only OPERATION NOTE type events */
                WHERE c.oe_event_type_name = 'OphTrOperationnote'
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

                FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
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
                WHERE c.oe_event_type_name = 'OphTrOperationnote'
                /* Restrict: RIGHT or BOTH eyes only */
                AND pl.eye_id IN (2, 3); /* 2 = RIGHT EYE, 3 = BOTH EYES */

EOL;
        return $query;
    }



    private function getEpisodeTreatment()
    {
        $query = <<<EOL

SELECT t.TreatmentId, t.OperationId, t.Eye, t.TreatmentTypeId, t.TreatmentTypeDescription
FROM tmp_rco_nod_EpisodeTreatment_{$this->extractIdentifier} t

EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('TreatmentId', 'OperationId', 'Eye', 'TreatmentTypeId', 'TreatmentTypeDescription'),
        );

        $this->saveCSVfile($dataQuery, 'EpisodeTreatment');

        $query = <<<EOL

SELECT DISTINCT t.TreatmentTypeId, t.TreatmentTypeDescription
FROM tmp_rco_nod_EpisodeTreatment_{$this->extractIdentifier} t

EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('TreatmentTypeId', 'TreatmentTypeDescription'),
        );

        return $this->saveCSVfile($dataQuery, 'TreatmentCodeLookup');
    }

    /********** EpisodeOperationIndication **********/

    private function createTmpRcoNodEpisodeOperationIndication()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationIndication_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeOperationIndication_{$this->extractIdentifier} (
                oe_event_id INT(10) NOT NULL,
                OperationId INT(10) NOT NULL,
                Eye CHAR(1) NOT NULL,
                IndicationId INT(10) NOT NULL,
                IndicationDescription VARCHAR(255),
            UNIQUE KEY OperationId (OperationId,Eye,IndicationId)
            );
EOL;
        return $query;
    }

    private function populateTmpRcoNodEpisodeOperationIndication()
    {
        $query = <<<EOL
            INSERT INTO tmp_rco_nod_EpisodeOperationIndication_{$this->extractIdentifier} (
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
              , IFNULL(d.id,0) AS IndicationId
              , d.term AS IndicationDescription
              /* Restriction: Start with operations (processed previously) */
            FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} o
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
              , IFNULL(d.id,0) AS IndicationId
              , d.term AS IndicationDescription
              /* Restriction: Start with operations (processed previously) */
            FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} o
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
FROM tmp_rco_nod_EpisodeOperationIndication_{$this->extractIdentifier} i

EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('OperationId', 'Eye', 'IndicationId', 'IndicationDescription'),
        );

        $this->saveCSVfile($dataQuery, 'EpisodeOperationIndication');

        $query = <<<EOL

SELECT DISTINCT i.IndicationId, i.IndicationDescription
FROM tmp_rco_nod_EpisodeOperationIndication_{$this->extractIdentifier} i

EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('IndicationId', 'IndicationDescription'),
        );

        return $this->saveCSVfile($dataQuery, 'OperationIndicationCodeLookup');
    }

    /********** end of EpisodeOperationIndication **********/




    /********** EpisodeOperationComplication **********/

    private function createTmpRcoNodEpisodeOperationComplication()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperationComplication_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeOperationComplication_{$this->extractIdentifier} (
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
            INSERT INTO tmp_rco_nod_EpisodeOperationComplication_{$this->extractIdentifier} (
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
                , IFNULL(rcoct.code, onccs.id) AS ComplicationTypeId
                , if (rcoct.code IS NULL, onccs.name, rcoct.name) as ComplicationTypeDescription
                #, onccs.name AS ComplicationTypeDescription

                /* Restriction: Start with OPERATIONS (processed previously), seeded from control events */
                FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} co

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
                , IFNULL(rcoct.code, onccs.id) AS ComplicationTypeId
                , if (rcoct.code IS NULL, onccs.name, rcoct.name) as ComplicationTypeDescription
                #, onccs.name AS ComplicationTypeDescription

                /* Restriction: Start with OPERATIONS (processed previously), seeded from control events */
                FROM tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} co

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

SELECT oc.OperationId, oc.Eye, oc.ComplicationTypeId, oc.ComplicationTypeDescription
FROM tmp_rco_nod_EpisodeOperationComplication_{$this->extractIdentifier} oc

EOL;

        $dataQuery = array(
            'query' => $query,
            'header' => array('OperationId', 'Eye', 'ComplicationTypeId', 'ComplicationTypeDescription'),
        );

        $this->saveCSVfile($dataQuery, 'EpisodeOperationComplication');

        $query = <<<EOL

SELECT DISTINCT oc.ComplicationTypeId, oc.ComplicationTypeDescription
FROM tmp_rco_nod_EpisodeOperationComplication_{$this->extractIdentifier} oc

EOL;

        $dataQuery = array(
            'query' => $query,
            'header' => array('ComplicationTypeId', 'ComplicationTypeDescription'),
        );

        return $this->saveCSVfile($dataQuery, 'OperationComplicationCodeLookup');
    }

    /********** end of EpisodeOperationComplication **********/





    /********** EpisodeOperation **********/

    private function createTmpRcoNodEpisodeOperation()
    {
        $query = <<<EOL

DROP TABLE IF EXISTS tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier};
CREATE TABLE tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} (
  oe_event_id int(10) NOT NULL
, patient_id int(10) NOT NULL
, OperationId int(10) NOT NULL
, Description text
, IsHypertensive VARCHAR(1) DEFAULT NULL
, ListedDate date NOT NULL
, SurgeonId int(10) NOT NULL
, SurgeonGradeId int(11) DEFAULT NULL
, AssistantId varchar(10) DEFAULT NULL
, AssistantGradeId varchar(10) DEFAULT NULL
, ConsultantId varchar(10) DEFAULT NULL
, SiteName varchar(255) DEFAULT NULL
, SiteODS varchar(10) DEFAULT NULL
, PRIMARY KEY (oe_event_id)
, UNIQUE KEY OperationId (OperationId)
);

EOL;
        return $query;
    }

    private function populateTmpRcoNodEpisodeOperation()
    {
        $query = <<<EOL

INSERT INTO tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} (
  oe_event_id
, patient_id
, OperationId
, Description
, IsHypertensive
, ListedDate
, SurgeonId
, SurgeonGradeId
, AssistantId
, AssistantGradeId
, ConsultantId
, SiteName
, SiteODS
)
SELECT
  c.oe_event_id
, c.patient_id
, c.oe_event_id AS OperationId
, '' AS Description /* TODO (not required for minimal data set) mapping: et_ophtroperationnote_procedurelist.id-> ophtroperationnote_procedurelist_procedure_assignment.proc_id->proc.snomed_term (semi-colon separated) */
, '' AS IsHypertensive /* TODO (not required for minimal data set) Toby Bisco said not currently in OE */
, DATE(c.nod_date) AS ListedDate /* TODO (not required for minimal data set) the specified mapping may not be correct */
, s.surgeon_id AS SurgeonId
, su.doctor_grade_id AS SurgeonGradeId
, s.assistant_id AS AssistantId
, au.doctor_grade_id AS AssistantGradeId
, s.supervising_surgeon_id AS ConsultantId /* TODO (not required for minimal data set) but mapping not fully implemented */
, s2.name
, s2.remote_id
/* Restriction: Start with control events */
FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
/* Join: Look up Operation Note SURGEON information */
/* LOOOOOOOOOOOOOOK TODO CHECK ASSUMPTION: only one et_ophtroperationnote_surgeon per operation note */
LEFT OUTER JOIN et_ophtroperationnote_surgeon s ON s.event_id = c.oe_event_id
/* Join: Look up SURGEON user information (LOJ used to return nulls if data problems (as opposed to loosing parent rows) */
LEFT OUTER JOIN user su ON s.surgeon_id = su.id
/* Join: Look up ASSISTANT user information (LOJ used to return nulls if data problems (as opposed to loosing parent rows) */
LEFT OUTER JOIN user au ON s.assistant_id = au.id
LEFT OUTER JOIN et_ophtroperationnote_site_theatre ost ON ost.event_id = c.oe_event_id
LEFT OUTER JOIN site s2 ON s2.id = ost.site_id
/* Restrict: Only OPERATION NOTE type events */
WHERE c.oe_event_type_name = 'OphTrOperationnote';

EOL;
        return $query;
    }


    private function getEpisodeOperation()
    {
        $query = <<<EOL
            SELECT  op.OperationId, c.oe_event_id as EpisodeId, op.Description, op.IsHypertensive, op.ListedDate, op.SurgeonId, IFNULL(op.SurgeonGradeId, "") as SurgeonGradeId,
                    IFNULL(op.AssistantId, "") as AssistantId,
                    IFNULL(op.AssistantGradeId, "") as AssistantGradeId, IFNULL(op.ConsultantId, "") as ConsultantId,
                    IFNULL(op.SiteName, "") as SiteName, IFNULL(op.SiteODS, "") as SiteODS
            FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
            JOIN tmp_rco_nod_EpisodeOperation_{$this->extractIdentifier} op ON c.oe_event_id = op.oe_event_id

EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array('OperationId', 'EpisodeId', 'Description', 'IsHypertensive', 'ListedDate', 'SurgeonId', 'SurgeonGradeId', 'AssistantId', 'AssistantGradeId', 'ConsultantId', 'SiteName', 'SiteODS'),
        );
        return $this->saveCSVfile($dataQuery, 'EpisodeOperation');
    }

    /********** end of EpisodeOperation **********/





    /********** EpisodeVisualAcuity **********/

    private function createTmpRcoNodEpisodeVisualAcuity()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_rco_nod_EpisodeVisualAcuity_{$this->extractIdentifier};
            CREATE TABLE tmp_rco_nod_EpisodeVisualAcuity_{$this->extractIdentifier} (
                oe_event_id INT(10) NOT NULL,
                Eye CHAR(1) NOT NULL,
                NotationRecordedId INT(10) NOT NULL,
                BestMeasure VARCHAR(255) NOT NULL,
                Unaided VARCHAR(255) DEFAULT NULL,
                Pinhole VARCHAR(255) DEFAULT NULL,
                BestCorrected VARCHAR(255) DEFAULT NULL
            );
EOL;
        return $query;
    }

    private function populateTmpRcoNodEpisodeVisualAcuity()
    {
        $query = <<<EOL
INSERT INTO tmp_rco_nod_EpisodeVisualAcuity_{$this->extractIdentifier} (
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
  , MAX(if (v.method IN ('Unaided','Aided','Pinhole'), v.reading_base_value, NULL)) AS max_all_base_value /* Higher base_value is better */
  , MAX(if (v.method = 'Unaided', v.reading_base_value, NULL)) AS max_unaided_base_value /* Higher base_value is better */
  , MAX(if (v.method = 'Aided', v.reading_base_value, NULL)) AS max_aided_base_value /* Higher base_value is better */
  , MAX(if (v.method = 'Pinhole', v.reading_base_value, NULL)) AS max_pinhole_base_value /* Higher base_value is better */
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
      WHEN 'Auto-refraction' THEN 'Aided'
      WHEN 'Formal refraction' THEN 'Aided'
      ELSE vam.name
      END AS method
    , evar.value AS reading_base_value
    , evar.unit_id orginal_unit_id
    , u.id AS logmar_single_letter_unit_id
    /* Restriction: Start with control events */
    FROM tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c
    /* Hard Join: Only examination events that have a Visual Acuity */
    JOIN et_ophciexamination_visualacuity eva
      ON eva.event_id = c.oe_event_id
    /* Hard Join: Visual Acuity individual readings (across both eyes and all methods ) */
    JOIN ophciexamination_visualacuity_reading evar
      ON evar.element_id = eva.id AND side != 2
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
AND u_max_pinhole.unit_id = bv.logmar_single_letter_unit_id;

EOL;
        return $query;
    }

    private function getEpisodeVisualAcuity()
    {
        $query = <<<EOL
                SELECT c.oe_event_id as EpisodeId, va.Eye, va.NotationRecordedId, va.BestMeasure, va.Unaided, va.Pinhole, va.BestCorrected
                FROM tmp_rco_nod_EpisodeVisualAcuity_{$this->extractIdentifier} va
                JOIN tmp_rco_nod_main_event_episodes_{$this->extractIdentifier} c ON va.oe_event_id = c.oe_event_id
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
            if (!in_array($row['StopDate'], array('Other', 'Ongoing'))) {
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
