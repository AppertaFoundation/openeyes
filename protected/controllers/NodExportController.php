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

            parent::init();
        }

	public function actionIndex()
	{
		echo "Hello";
		die;
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
        
                $sql = <<<EOL
                    SELECT id as Surgeonid, IFNULL(registration_code, 'NULL') as GMCnumber, IFNULL(title, 'NULL') as Title, IFNULL(first_name, 'NULL') as FirstName,
                    (
			SELECT `code` 
			FROM tmp_doctor_grade, doctor_grade
			WHERE user.`doctor_grade_id` = doctor_grade.id AND doctor_grade.`grade` = tmp_doctor_grade.desc
                    ) AS CurrentGradeId
                FROM user 
                WHERE is_surgeon = 1 AND active = 1
EOL;

            $surgeons = Yii::app()->db->createCommand($sql)->queryAll();
            
            // cleanup
            Yii::app()->db->createCommand("DROP TABLE tmp_doctor_grade;")->execute();
            
            $csv = $this->array2Csv($surgeons);
            
            file_put_contents($this->export_path . '/surgeons.csv' , $csv);
            
            echo "<pre>" . print_r($csv, true) . "</pre>";
            die;
        }
        
        /**
         * The extraction of patient data is psuedoanonymised. All tables prefixed with “Patient” link back to the 
         * “Patient” table via the ‘PatientId’ variable. Each patient on the RCOphth NOD will have one row in the “Patient” table.
         */
        public function actionGetPatients()
        {
            
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
        
}