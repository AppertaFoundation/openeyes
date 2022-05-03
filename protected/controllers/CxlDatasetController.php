<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

// this extract's execution time is more than the default 500sec
// for 5yrs time period it can last more than 30min
ini_set('max_execution_time', 3600);

class CxlDatasetController extends BaseController
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
    private $patient_identifier_prompt;

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
                'roles' => array('CXL Dataset'),
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
        $this->exportPath = realpath(dirname(__FILE__) . '/..') . '/runtime/cxl-dataset/' . $this->institutionCode . '/' . $date;
        $this->zipName = $this->institutionCode . '_' . $date . '_CXL_Dataset.zip';

        if (!file_exists($this->exportPath)) {
            mkdir($this->exportPath, 0777, true);
        }

        $startDate = Yii::app()->request->getParam("date_from", '');
        $endDate = Yii::app()->request->getParam("date_to", '');

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
        $this->render('//cxldataset/index');
    }

    /**
     * Generates CSV and zip files then sends to the browser
     */
    public function actionGenerate()
    {
        $this->patient_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), Institution::model()->getCurrent()->id, $this->selectedSiteId);

        $this->generateExport();

        $this->createZipFile();

        if (file_exists($this->exportPath . '/' . $this->zipName)) {
            Yii::app()->getRequest()->sendFile($this->zipName, file_get_contents($this->exportPath . '/' . $this->zipName));
        }
    }

    /**
     * Generates the CSV files
     */
    public function generateExport()
    {
        // Concatinate sequence of statements to create and load working tables
        $query = $this->createAllTempTables();
        $query .= $this->populateAllTempTables();

        // Execute all statements to create and populate working tables
        Yii::app()->db->createCommand($query)->execute();

        // Extract results from tables into csv files
        $this->getPatients();
        $this->getHistory();
        $this->getAssessments();
        $this->getCxlSurgery();

        $this->clearAllTempTables();
    }

    /**
     * Save CSV file and returns episodeIDs if $episodeIdField isset
     *
     * @param string[] $dataQuery SQL query
     * @param string $filename
     * @param string $dataFormatter
     * @return null|array
     *
     * @throws Exception
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
            $runQuery = $dataQuery['query'] . " LIMIT " . $chunk . " OFFSET " . $offset . ";";
            $dataCmd = Yii::app()->db->createCommand($runQuery);

            $data = $dataCmd->queryAll();
            $data = $this->setPatientIdentifiers($data);

            if ($offset == 0) {
                file_put_contents($this->exportPath . '/' . $filename . '.csv', ((implode(',', $dataQuery['header'])) . "\n"), FILE_APPEND);
            }

            if (count($data) > 0) {
                $csv = $this->array2Csv($data, null, $dataFormatter);

                file_put_contents($this->exportPath . '/' . $filename . '.csv', $csv, FILE_APPEND);

                $offset += $chunk;
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

        $query .= $this->createTmpCxlMainEventEpisodes();
        $query .= $this->createTmpCxlPatients();
        $query .= $this->createTmpCxlHistory();
        $query .= $this->createTmpCxlAssessments();
        $query .= $this->createTmpCxlSurgery();

        return $query;
    }

    /**
     * This function will call the functions one by one to populate each tmp tables belongs to a csv file
     */
    private function populateAllTempTables()
    {
        $query = '';

        $query .= $this->populateTmpCxlMainEventEpisodes();
        $query .= $this->populateTmpCxlPatients();
        $query .= $this->populateTmpCxlHistory();
        $query .= $this->populateTmpCxlAssessments();
        $query .= $this->populateTmpCxlSurgery();

        return $query;
    }

    private function clearAllTempTables()
    {
        $cleanQuery = <<<EOL
                DROP TABLE IF EXISTS tmp_cxl_main_event_episodes_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_cxl_patients_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_cxl_history_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_cxl_assessments_{$this->extractIdentifier};
                DROP TABLE IF EXISTS tmp_cxl_surgery_{$this->extractIdentifier};
EOL;

        Yii::app()->db->createCommand($cleanQuery)->execute();
    }

    /********** Surgeon **********/
// LEAVING THIS IN, IN CASE IT IS REQUIRED FOR CXL LATER
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
                FROM tmp_rco_nod_Surgeon_{$this->extractIdentifier}
EOL;

        $dataQuery = array(
            'query' => $query,
            'header' => array('Surgeonid', 'GMCnumber', 'Title', 'FirstName', 'CurrentGradeId'),
        );

        $this->saveCSVfile($dataQuery, 'Surgeon');
    }

    /********** end of Surgeon **********/


    /********** Patients **********/

    private function createTmpCxlPatients()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_cxl_patients_{$this->extractIdentifier};
            CREATE TABLE tmp_cxl_patients_{$this->extractIdentifier} (
                PatientId VARCHAR(40) NOT NULL,
                Age INTEGER(3) NOT NULL,
                Sex VARCHAR(7) NOT NULL,
                Postcode VARCHAR(10) NOT NULL,
                Consultant INTEGER(10),
                EthnicCategory VARCHAR(64),
                PRIMARY KEY (PatientId)
            );
EOL;
        return $query;
    }

    private function populateTmpCxlPatients()
    {
        $query = <<<EOL
                INSERT INTO tmp_cxl_patients_{$this->extractIdentifier} (
                    PatientId,
                    Age,
                    Sex,
                    Postcode,
                    Consultant,
                    EthnicCategory
                  )
                  SELECT
                          p.id,
                          TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, CURDATE())),
                          (SELECT CASE WHEN gender='F' THEN 'Female' WHEN gender='M' THEN 'Male' ELSE 'Unknown' END),
                          IFNULL(LEFT(a.postcode,LOCATE(' ',a.postcode) - 1), 'Unknown'),
                          (SELECT MAX(consultant_id) FROM tmp_cxl_main_event_episodes_{$this->extractIdentifier} WHERE patient_id = p.id),
                          IFNULL(eg.name, 'Unknown')
                  FROM patient p
                  LEFT JOIN ethnic_group eg ON eg.id = p.ethnic_group_id
                  LEFT JOIN address a ON a.contact_id = p.contact_id
                  WHERE p.id IN
                    (
                        SELECT DISTINCT(c.patient_id)
                        FROM tmp_cxl_main_event_episodes_{$this->extractIdentifier} c
                    );
EOL;
        return $query;
    }

    private function getPatients()
    {
        $query = <<<EOL
                SELECT *
                FROM tmp_cxl_patients_{$this->extractIdentifier}
EOL;

        $dataQuery = array(
            'query' => $query,
            'header' => array($this->patient_identifier_prompt, 'Age', 'Sex', 'Postcode', 'Consultant', 'EthnicCategory', 'Patient IDs'),
        );

        $this->saveCSVfile($dataQuery, 'Patient');
    }

    private function setPatientIdentifiers($data)
    {
        $data_with_ids = [];

        foreach ($data as $patient) {
            $patient_identifier = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $patient['PatientId'], Institution::model()->getCurrent()->id, $this->selectedSiteId));
            $patient_identifiers = PatientIdentifierHelper::getAllPatientIdentifiersForReports($patient['PatientId']);
            $patient['PatientId'] = $patient_identifier;
            $patient['all_ids'] = $patient_identifiers;
            $data_with_ids[] = $patient;
        }

        return $data_with_ids;
    }

    /********** History **********/
    private function createTmpCxlHistory()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_cxl_history_{$this->extractIdentifier};
            CREATE TABLE tmp_cxl_history_{$this->extractIdentifier} (
                PatientId VARCHAR(40) NOT NULL,
                EventDate DATE NOT NULL,
                Eye VARCHAR(5) NOT NULL,
                CXL VARCHAR(3) NOT NULL,
                Atopy VARCHAR(100) NOT NULL
            );
EOL;
        return $query;
    }

    private function populateTmpCxlHistory()
    {
        $query = <<<EOL
                INSERT INTO tmp_cxl_history_{$this->extractIdentifier} (
                        PatientId,
                        EventDate,
                        Eye,
                        CXL,
                        Atopy )
                SELECT p.id,
                       c.event_date,
                       'Right',
                       CASE WHEN IFNULL(h.right_previous_cxl_value, 0) = 0 THEN 'No' ELSE 'Yes' END,
                       TRIM(',None' FROM CONCAT_WS( ','
                                                  , CASE WHEN h.asthma_id = 1 THEN 'Asthma' END
                                                  , CASE WHEN h.eczema_id = 1 THEN 'Eczema' END
                                                  , CASE WHEN h.eye_rubber_id = 1 THEN 'Eye Rubber' END
                                                  , CASE WHEN h.hayfever_id = 1 THEN 'Hayfever' END
                                                  , 'None'
                                                  ))
                FROM tmp_cxl_main_event_episodes_{$this->extractIdentifier} c
                JOIN et_ophciexamination_keratometry k ON k.event_id = c.event_id
                LEFT JOIN et_ophciexamination_cxl_history h ON h.event_id = c.event_id
                JOIN patient p ON p.id = c.patient_id
                UNION ALL
                SELECT p.id,
                       c.event_date,
                       'Left',
                       CASE WHEN IFNULL(h.left_previous_cxl_value, 0) = 0 THEN 'No' ELSE 'Yes' END,
                       TRIM(',None' FROM CONCAT_WS( ','
                                                  , CASE WHEN h.asthma_id = 1 THEN 'Asthma' END
                                                  , CASE WHEN h.eczema_id = 1 THEN 'Eczema' END
                                                  , CASE WHEN h.eye_rubber_id = 1 THEN 'Eye Rubber' END
                                                  , CASE WHEN h.hayfever_id = 1 THEN 'Hayfever' END
                                                  , 'None'
                                                  ))
                FROM tmp_cxl_main_event_episodes_{$this->extractIdentifier} c
                JOIN et_ophciexamination_keratometry k ON k.event_id = c.event_id
                LEFT JOIN et_ophciexamination_cxl_history h ON h.event_id = c.event_id
                JOIN patient p ON p.id = c.patient_id
                ;
EOL;
        return $query;
    }

    private function getHistory()
    {
        $query = <<<EOL
                SELECT *
                FROM tmp_cxl_history_{$this->extractIdentifier}
EOL;

        $dataQuery = array(
            'query' => $query,
            'header' => array($this->patient_identifier_prompt, 'Date', 'Eye', 'CXL', 'Atopy', 'Patient IDs'),
        );

        $this->saveCSVfile($dataQuery, 'History');
    }

    /********** Main Events Control Table **********/

    private function createTmpCxlMainEventEpisodes()
    {
        $query = <<<EOL

DROP TABLE IF EXISTS tmp_cxl_main_event_episodes_{$this->extractIdentifier};
CREATE TABLE tmp_cxl_main_event_episodes_{$this->extractIdentifier} (
    event_id int(10) NOT NULL,
    event_date DATE NOT NULL,
    patient_id int(10) NOT NULL,
    episode_id int(10) NOT NULL,
    consultant_id int(10),
    PRIMARY KEY (event_id)
);

EOL;
        return $query;
    }

    private function populateTmpCxlMainEventEpisodes()
    {
        $query = <<<EOL
INSERT INTO tmp_cxl_main_event_episodes_{$this->extractIdentifier} (
  event_id
, event_date
, patient_id
, episode_id
, consultant_id
)
SELECT
  ev.id
, ev.event_date
, ep.patient_id
, ep.id
, f.consultant_id
FROM et_ophciexamination_keratometry k
JOIN event ev ON ev.id = k.event_id
JOIN episode ep ON ev.episode_id = ep.id
JOIN firm f ON f.id = ep.firm_id
WHERE ev.deleted = 0
EOL;

        if ($this->startDate) {
            $query .= " AND DATE(ev.event_date) >= STR_TO_DATE('{$this->startDate}', '%Y-%m-%d') ";
        }

        if ($this->endDate) {
            $query .= " AND DATE(ev.event_date) <= STR_TO_DATE('{$this->endDate}', '%Y-%m-%d') ";
        }

        $query .= ';';

        $query .= <<<EOL

INSERT INTO tmp_cxl_main_event_episodes_{$this->extractIdentifier} (
  event_id
, event_date
, patient_id
, episode_id
, consultant_id
)
SELECT
  ev.id
, ev.event_date
, ep.patient_id
, ep.id
, f.consultant_id
FROM et_ophtroperationnote_cxl k
JOIN event ev ON ev.id = k.event_id
JOIN episode ep ON ev.episode_id = ep.id
JOIN firm f ON f.id = ep.firm_id
WHERE ev.deleted = 0
EOL;

        if ($this->startDate) {
            $query .= " AND DATE(ev.event_date) >= STR_TO_DATE('{$this->startDate}', '%Y-%m-%d') ";
        }

        if ($this->endDate) {
            $query .= " AND DATE(ev.event_date) <= STR_TO_DATE('{$this->endDate}', '%Y-%m-%d') ";
        }

        $query .= ';';

        return $query;
    }

    /********** Assessments **********/

    private function createTmpCxlAssessments()
    {
        $query = <<<EOL
            DROP TABLE IF EXISTS tmp_cxl_assessments_{$this->extractIdentifier};
            CREATE TABLE tmp_cxl_assessments_{$this->extractIdentifier} (
                PatientId VARCHAR(40) NOT NULL,
                EventDate DATE NOT NULL,
                Eye VARCHAR(5) NOT NULL,
                VisualAcuityChart VARCHAR(100),
                UDVA VARCHAR(255),
                CDVA VARCHAR(255),
                Sphere DECIMAL(5,2),
                Cylinder DECIMAL(5,2),
                Axis INTEGER(3),
                Kmax DECIMAL(5,2),
                FrontK1 DECIMAL(5,2),
                FrontK2 DECIMAL(5,2),
                BackK1 DECIMAL(5,2),
                BackK2 DECIMAL(5,2),
                ThinnestPachymetry INTEGER,
                BelinAmbrosio DECIMAL(5,2),
                QualityScoreFront VARCHAR(128),
                QualityScoreBack VARCHAR(128),
                CLRemoved VARCHAR(128),
                EndothelialCellDensity INTEGER(10),
                CoefficientOfVariation DECIMAL(5,2),
                Cornea VARCHAR(128),
                Diagnosis VARCHAR(255),
                Outcome VARCHAR(128)
            );
EOL;
        return $query;
    }

    private function populateTmpCxlAssessments()
    {
        $query = <<<EOL
                INSERT INTO tmp_cxl_assessments_{$this->extractIdentifier} (
                        PatientId,
                        EventDate,
                        Eye,
                        VisualAcuityChart,
                        UDVA,
                        CDVA,
                        Sphere,
                        Cylinder,
                        Axis,
                        Kmax,
                        FrontK1,
                        FrontK2,
                        BackK1,
                        BackK2,
                        ThinnestPachymetry,
                        BelinAmbrosio,
                        QualityScoreFront,
                        QualityScoreBack,
                        CLRemoved,
                        EndothelialCellDensity,
                        CoefficientOfVariation,
                        Cornea,
                        Diagnosis,
                        Outcome )
                SELECT p.id,
                       c.event_date,
                       'Right',
                       vau.name,
                       ( SELECT MAX(uv.value)
                         FROM   ophciexamination_visualacuity_reading r
                         JOIN   ophciexamination_visualacuity_method m ON m.id = r.method_id
                         JOIN   ophciexamination_visual_acuity_unit_value uv ON uv.base_value = r.value
                         WHERE  r.element_id = va.id
                           AND  uv.unit_id = r.unit_id
                           AND  r.side = 0
                           AND  m.name = 'Unaided'
                       ),
                       ( SELECT MAX(uv.value)
                         FROM   ophciexamination_visualacuity_reading r
                         JOIN   ophciexamination_visualacuity_method m ON m.id = r.method_id
                         JOIN   ophciexamination_visual_acuity_unit_value uv ON uv.base_value = r.value
                         WHERE  r.element_id = va.id
                           AND  uv.unit_id = r.unit_id
                           AND  r.side = 0
                           AND  m.name IN ('Glasses', 'Contact lens')
                       ),
                       rr.sphere,
                       rr.cylinder,
                       rr.axis,
                       k.right_kmax_value,
                       k.right_anterior_k1_value,
                       k.right_anterior_k2_value,
                       k.right_axis_anterior_k1_value,
                       k.right_axis_anterior_k2_value,
                       k.right_thinnest_point_pachymetry_value,
                       k.right_ba_index_value,
                       qf.name,
                       qb.name,
                       clr.name,
                       sm.right_endothelial_cell_density_value,
                       sm.right_coefficient_variation_value,
                       slc.name,
                       disorder.term,
                       os.name
                FROM tmp_cxl_main_event_episodes_{$this->extractIdentifier} c
                JOIN et_ophciexamination_keratometry k ON k.event_id = c.event_id
                LEFT JOIN ophciexamination_cxl_quality_score qf ON qf.id = k.right_quality_front
                LEFT JOIN ophciexamination_cxl_quality_score qb ON qb.id = k.right_quality_back
                LEFT JOIN ophciexamination_cxl_cl_removed clr ON clr.id = k.right_cl_removed
                LEFT JOIN et_ophciexamination_specular_microscopy sm ON sm.event_id = c.event_id
                LEFT JOIN et_ophciexamination_slit_lamp sl ON sl.event_id = c.event_id
                LEFT JOIN ophciexamination_slit_lamp_cornea slc ON slc.id = sl.right_cornea_id
                LEFT JOIN et_ophciexamination_cxl_history h ON h.event_id = c.event_id
                LEFT JOIN et_ophciexamination_visualacuity va ON va.event_id = c.event_id
                LEFT JOIN ophciexamination_visual_acuity_unit vau ON vau.id = va.archive_unit_id
                LEFT JOIN et_ophciexamination_refraction rf ON rf.event_id = c.event_id AND rf.eye_id IN (2, 3)
                LEFT JOIN ophciexamination_refraction_reading rr ON rr.id = (
                    /* Need to ensure we only get one reading result, ordered by the priority of the type */
                    SELECT single_reading.id
                    FROM ophciexamination_refraction_reading single_reading
                    LEFT JOIN ophciexamination_refraction_type rt
                    ON single_reading.type_id = rt.id
                    WHERE element_id = rf.id
                    AND single_reading.eye_id = 2
                    ORDER BY -rt.priority DESC /* Null indicates an "other" type, which negative desc ordering will make last */
                    LIMIT 1
                )
                LEFT JOIN et_ophciexamination_diagnoses d ON d.event_id = c.event_id
                LEFT JOIN ophciexamination_diagnosis dd ON dd.element_diagnoses_id = d.id AND dd.eye_id IN (2, 3) AND dd.principal = 1
                LEFT JOIN disorder ON disorder.id = dd.disorder_id
                LEFT JOIN et_ophciexamination_clinicoutcome o ON o.event_id = c.event_id
                LEFT JOIN ophciexamination_clinicoutcome_entry oe ON oe.element_id = o.id
                LEFT JOIN ophciexamination_clinicoutcome_status os ON os.id = oe.status_id
                JOIN patient p ON p.id = c.patient_id
                UNION ALL
                SELECT p.id,
                       c.event_date,
                       'Left',
                       vau.name,
                       ( SELECT MAX(uv.value)
                         FROM   ophciexamination_visualacuity_reading r
                         JOIN   ophciexamination_visualacuity_method m ON m.id = r.method_id
                         JOIN   ophciexamination_visual_acuity_unit_value uv ON uv.base_value = r.value
                         WHERE  r.element_id = va.id
                           AND  uv.unit_id = r.unit_id
                           AND  r.side = 1
                           AND  m.name = 'Unaided'
                       ),
                       ( SELECT MAX(uv.value)
                         FROM   ophciexamination_visualacuity_reading r
                         JOIN   ophciexamination_visualacuity_method m ON m.id = r.method_id
                         JOIN   ophciexamination_visual_acuity_unit_value uv ON uv.base_value = r.value
                         WHERE  r.element_id = va.id
                           AND  uv.unit_id = r.unit_id
                           AND  r.side = 1
                           AND  m.name IN ('Glasses', 'Contact lens')
                       ),
                       rr.sphere,
                       rr.cylinder,
                       rr.axis,
                       k.left_kmax_value,
                       k.left_anterior_k1_value,
                       k.left_anterior_k2_value,
                       k.left_axis_anterior_k1_value,
                       k.left_axis_anterior_k2_value,
                       k.left_thinnest_point_pachymetry_value,
                       k.left_ba_index_value,
                       qf.name,
                       qb.name,
                       clr.name,
                       sm.left_endothelial_cell_density_value,
                       sm.left_coefficient_variation_value,
                       slc.name,
                       disorder.term,
                       os.name
                FROM tmp_cxl_main_event_episodes_{$this->extractIdentifier} c
                JOIN et_ophciexamination_keratometry k ON k.event_id = c.event_id
                LEFT JOIN ophciexamination_cxl_quality_score qf ON qf.id = k.left_quality_front
                LEFT JOIN ophciexamination_cxl_quality_score qb ON qb.id = k.left_quality_back
                LEFT JOIN ophciexamination_cxl_cl_removed clr ON clr.id = k.left_cl_removed
                LEFT JOIN et_ophciexamination_specular_microscopy sm ON sm.event_id = c.event_id
                LEFT JOIN et_ophciexamination_slit_lamp sl ON sl.event_id = c.event_id
                LEFT JOIN ophciexamination_slit_lamp_cornea slc ON slc.id = sl.left_cornea_id
                LEFT JOIN et_ophciexamination_cxl_history h ON h.event_id = c.event_id
                LEFT JOIN et_ophciexamination_visualacuity va ON va.event_id = c.event_id
                LEFT JOIN ophciexamination_visual_acuity_unit vau ON vau.id = va.archive_unit_id
                LEFT JOIN et_ophciexamination_refraction rf ON rf.event_id = c.event_id AND rf.eye_id IN (1, 3)
                LEFT JOIN ophciexamination_refraction_reading rr ON rr.id = (
                    /* Need to ensure we only get one reading result, ordered by the priority of the type */
                    SELECT single_reading.id
                    FROM ophciexamination_refraction_reading single_reading
                    LEFT JOIN ophciexamination_refraction_type rt
                    ON single_reading.type_id = rt.id
                    WHERE element_id = rf.id
                    AND single_reading.eye_id = 1
                    ORDER BY -rt.priority DESC /* Null indicates an "other" type, which negative desc ordering will make last */
                    LIMIT 1
                )
                LEFT JOIN et_ophciexamination_diagnoses d ON d.event_id = c.event_id
                LEFT JOIN ophciexamination_diagnosis dd ON dd.element_diagnoses_id = d.id AND dd.eye_id IN (1, 3) AND dd.principal = 1
                LEFT JOIN disorder ON disorder.id = dd.disorder_id
                LEFT JOIN et_ophciexamination_clinicoutcome o ON o.event_id = c.event_id
                LEFT JOIN ophciexamination_clinicoutcome_entry oe ON oe.element_id = o.id
                LEFT JOIN ophciexamination_clinicoutcome_status os ON os.id = oe.status_id
                JOIN patient p ON p.id = c.patient_id
                ;
EOL;
        return $query;
    }

    private function getAssessments()
    {

        $query = <<<EOL
                SELECT *
                FROM tmp_cxl_assessments_{$this->extractIdentifier}
EOL;

        $dataQuery = array(
            'query' => $query,
            'header' => array($this->patient_identifier_prompt, 'Date', 'Eye', 'VisualAcuityChart', 'UDVA', 'CDVA', 'Sphere', 'Cylinder',
                'Axis', 'Kmax', 'FrontK1', 'FrontK2', 'BackK1', 'BackK2', 'ThinnestPachymetry', 'BelinAmbrosio',
                'QualityScoreFront', 'QualityScoreBack', 'CLRemoved', 'EndothelialCellDensity', 'CoefficientOfVariation',
                'Cornea', 'Diagnosis', 'Outcome'),
        );

        return $this->saveCSVfile($dataQuery, 'Assessments');
    }

    /********** CxlSurgery **********/

    private function createTmpCxlSurgery()
    {
        $query = <<<EOL
                DROP TABLE IF EXISTS tmp_cxl_surgery_{$this->extractIdentifier};
                CREATE TABLE tmp_cxl_surgery_{$this->extractIdentifier} (
                    PatientId VARCHAR(40) NOT NULL,
                    EventDate DATE NOT NULL,
                    Eye VARCHAR(5) NOT NULL,
                    Operator VARCHAR(250),
                    Device VARCHAR(128),
                    EpithelialStatus VARCHAR(255),
                    EpithelialDebridement VARCHAR(128),
                    DebridementSize INTEGER,
                    IontophoresisCycles VARCHAR(128),
                    IontophoresisCurrent INTEGER(10),
                    IontophoresisDuration INTEGER(10),
                    RiboflavinPreparation VARCHAR(128),
                    RiboflavinDuration INTEGER,
                    UVIrradiance INTEGER,
                    UVDuration DECIMAL(5,1),
                    UVContinuousOrPulsed VARCHAR(128),
                    UVTotalEnergy INTEGER(10),
                    Comments VARCHAR(1024)
                );
EOL;

        return $query;
    }

    private function populateTmpCxlSurgery()
    {
        $query = <<<EOL
                INSERT INTO tmp_cxl_surgery_{$this->extractIdentifier} (
                    PatientId,
                    EventDate,
                    Eye,
                    Operator,
                    Device,
                    EpithelialStatus,
                    EpithelialDebridement,
                    DebridementSize,
                    IontophoresisCycles,
                    IontophoresisCurrent,
                    IontophoresisDuration,
                    RiboflavinPreparation,
                    RiboflavinDuration,
                    UVIrradiance,
                    UVDuration,
                    UVContinuousOrPulsed,
                    UVTotalEnergy,
                    Comments
                  )
                SELECT p.id,
                       c.event_date,
                       'Right',
                       dg.grade,
                       cd.name,
                       es.name,
                       erm.name,
                       REPLACE(erd.name, 'mm', ''),
                       i.name,
                       k.iontophoresis_current_value,
                       k.iontophoresis_duration_value,
                       rp.name,
                       REPLACE(REPLACE(sd.name, 'minutes', ''), 'minute', ''),
                       ui.name,
                       REPLACE(REPLACE(ud.name, 'seconds', ''), 'second', ''),
                       CASE WHEN uid.name = '0 seconds' THEN 'Continuous' ELSE REPLACE(REPLACE(uid.name, ' seconds', ''), ' second', '') END,
                       k.uv_total_energy_value,
                       k.cxl_comments
                FROM tmp_cxl_main_event_episodes_{$this->extractIdentifier} c
                JOIN et_ophtroperationnote_cxl k ON k.event_id = c.event_id
                JOIN et_ophtroperationnote_procedurelist pl ON pl.event_id = c.event_id AND pl.eye_id IN (2, 3)
                LEFT JOIN et_ophtroperationnote_surgeon s ON s.event_id = c.event_id
                LEFT JOIN user u ON u.id = s.surgeon_id
                LEFT JOIN doctor_grade dg ON dg.id = u.doctor_grade_id
                LEFT JOIN ophtroperationnote_cxl_devices cd ON cd.id = k.device_id
                LEFT JOIN ophtroperationnote_cxl_epithelial_status es ON es.id = k.epithelial_status_id
                LEFT JOIN ophtroperationnote_cxl_epithelial_removal_method erm ON erm.id = k.epithelial_removal_method_id
                LEFT JOIN ophtroperationnote_cxl_epithelial_removal_diameter erd ON erd.id = k.epithelial_removal_diameter_id
                LEFT JOIN ophtroperationnote_cxl_iontophoresis i ON i.id = k.iontophoresis_id
                LEFT JOIN ophtroperationnote_cxl_riboflavin_preparation rp ON rp.id = k.riboflavin_preparation_id
                LEFT JOIN ophtroperationnote_cxl_soak_duration sd ON sd.id = k.soak_duration_range_id
                LEFT JOIN ophtroperationnote_cxl_uv_irradiance ui ON ui.id = k.uv_irradiance_range_id
                LEFT JOIN ophtroperationnote_cxl_uv_pulse_duration ud ON ud.id = k.uv_pulse_duration_id
                LEFT JOIN ophtroperationnote_cxl_interpulse_duration uid ON uid.id = k.interpulse_duration_id
                JOIN patient p ON p.id = c.patient_id
                UNION ALL
                SELECT p.id,
                       c.event_date,
                       'Left',
                       dg.grade,
                       cd.name,
                       es.name,
                       erm.name,
                       REPLACE(erd.name, 'mm', ''),
                       i.name,
                       k.iontophoresis_current_value,
                       k.iontophoresis_duration_value,
                       rp.name,
                       REPLACE(REPLACE(sd.name, 'minutes', ''), 'minute', ''),
                       ui.name,
                       REPLACE(REPLACE(ud.name, 'seconds', ''), 'second', ''),
                       CASE WHEN uid.name = '0 seconds' THEN 'Continuous' ELSE REPLACE(REPLACE(uid.name, ' seconds', ''), ' second', '') END,
                       k.uv_total_energy_value,
                       k.cxl_comments
                FROM tmp_cxl_main_event_episodes_{$this->extractIdentifier} c
                JOIN et_ophtroperationnote_cxl k ON k.event_id = c.event_id
                JOIN et_ophtroperationnote_procedurelist pl ON pl.event_id = c.event_id AND pl.eye_id IN (1, 3)
                LEFT JOIN et_ophtroperationnote_surgeon s ON s.event_id = c.event_id
                LEFT JOIN user u ON u.id = s.surgeon_id
                LEFT JOIN doctor_grade dg ON dg.id = u.doctor_grade_id
                LEFT JOIN ophtroperationnote_cxl_devices cd ON cd.id = k.device_id
                LEFT JOIN ophtroperationnote_cxl_epithelial_status es ON es.id = k.epithelial_status_id
                LEFT JOIN ophtroperationnote_cxl_epithelial_removal_method erm ON erm.id = k.epithelial_removal_method_id
                LEFT JOIN ophtroperationnote_cxl_epithelial_removal_diameter erd ON erd.id = k.epithelial_removal_diameter_id
                LEFT JOIN ophtroperationnote_cxl_iontophoresis i ON i.id = k.iontophoresis_id
                LEFT JOIN ophtroperationnote_cxl_riboflavin_preparation rp ON rp.id = k.riboflavin_preparation_id
                LEFT JOIN ophtroperationnote_cxl_soak_duration sd ON sd.id = k.soak_duration_range_id
                LEFT JOIN ophtroperationnote_cxl_uv_irradiance ui ON ui.id = k.uv_irradiance_range_id
                LEFT JOIN ophtroperationnote_cxl_uv_pulse_duration ud ON ud.id = k.uv_pulse_duration_id
                LEFT JOIN ophtroperationnote_cxl_interpulse_duration uid ON uid.id = k.interpulse_duration_id
                JOIN patient p ON p.id = c.patient_id
                ;
EOL;

        return $query;
    }

    private function getCxlSurgery()
    {
        $query = <<<EOL
                SELECT *
                FROM tmp_cxl_surgery_{$this->extractIdentifier}
EOL;
        $dataQuery = array(
            'query' => $query,
            'header' => array($this->patient_identifier_prompt, 'Date', 'Eye', 'Operator', 'Device', 'EpithelialStatus', 'EpithelialDebridement',
                'DebridementSize', 'IontophoresisCycles', 'IontophoresisCurrent', 'IontophoresisDuration', 'RiboflavinPreparation',
                'RiboflavinDuration', 'UVIrradiance', 'UVDuration', 'UVContinuousOrPulsed', 'UVTotalEnergy', 'Comments'),
        );

        return $this->saveCSVfile($dataQuery, 'CxlSurgery');
    }

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
}
