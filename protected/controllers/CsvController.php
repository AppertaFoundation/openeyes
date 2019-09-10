<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue;

\Yii::import('application.modules.OETrial.models.*');

class CsvController extends BaseController
{
	public static	$file_path = "tempfiles/";

	static $contexts = array(
        'trials' => array(
            'successAction' => 'OETrial/trial',
            'createAction' => 'createNewTrial',
            'errorMsg' => 'Errors with creating trial on line ',
        ),
        'patients' => array(
            'successAction' => 'site/index',
            'createAction' => 'createNewPatient',
            'errorMsg' => 'Errors with creating patient on line ',
        ),
        'trialPatients' => array(
            'successAction' => 'OETrial/trial',
            'createAction' => 'createNewTrialPatient',
            'errorMsg' => 'Errors with trial patient upload on line ',
        ),
    );

    public static function uploadAccess()
    {
        return Yii::app()->user->checkAccess('admin')
						&& Yii::app()->user->checkAccess('TaskAddPatient')
						&& Yii::app()->user->checkAccess('TaskEditEpisode')
						&& Yii::app()->user->checkAccess('TaskEditEvent')
            && Yii::app()->user->checkAccess('TaskCreateTrial');
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('upload', 'preview', 'import'),
                'expression' => 'CsvController::uploadAccess()',
                'users' => array('@'),
            )
        );
    }

    public function actionUpload($context)
    {
        $this->render('upload', array('context' => $context));
    }

    public function actionPreview($context)
    {
				if(file_exists(self::$file_path)) {
					$file_list = glob(self::$file_path . "*");
					foreach ($file_list as $file) {
						unlink($file);
					}
					rmdir(self::$file_path);
				}

				$csv_id = null;

        $table = array();
        $headers = array();
        $errors = array();
        if (isset($_FILES['Csv']['tmp_name']['csvFile']) && $_FILES['Csv']['tmp_name']['csvFile'] !== "") {

//            check the file name extension and type
            $allowed = array('csv','application/vnd.ms-excel');
            $fileName = $_FILES['Csv']['name']['csvFile'];
            $fileType = $_FILES['Csv']['type']['csvFile'];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);

            if(!in_array($extension,$allowed) || !in_array($fileType,$allowed)){
                $errors[] = 'Only the csv file can be uploaded.';
                $this->render('upload', array('context' => $context, 'errors' => $errors));
            }
            else {
                if (($handle = fopen($_FILES['Csv']['tmp_name']['csvFile'], "r")) !== false) {
                    if (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
                        foreach ($line as $header) {
                            // basic sanitization, remove non printable chars - This is required if the CSV file is
                            // exported from the excel (as UTF8 CSV) as excel appends \ufeff to the beginning of CSV file.
                            $header = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header);
                            $headers[] = $header;
                        }
                    }

                    while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
                        $row = array();
                        $header_count = 0;
                        foreach ($line as $cel) {
                            if($header_count+1<=count($headers)){
                                $row[$headers[$header_count++]] = $cel;
                            }
                        }
                        $table[] = $row;
                    }
                    fclose($handle);

                }
                $_SESSION['table_data'] = $table;
                $this->render('preview', array('table' => $table, 'context' => $context));
            }
          
					$csv_id = md5_file($_FILES['Csv']['tmp_name']['csvFile']);

					if(!file_exists(self::$file_path)) {
						mkdir(self::$file_path);
					}

					copy($_FILES['Csv']['tmp_name']['csvFile'], self::$file_path . $csv_id . ".csv");
        }
      
        } else { // no file uploaded
            $errors[] = 'No csv file selected. Please choose a csv file to upload.';
            $this->render('upload', array('context' => $context, 'errors' => $errors));
        }
    }

    public function actionImport($context, $csv)
    {
			$errors = null;

			$import_log = new ImportLog();
			$import_log->import_user_id = Yii::app()->user->id;
			$import_log->startdatetime = date('Y-m-d H:i:s');
			$import_log->status = "Failure";
			if(!$import_log->save()) {
				\OELog::log("Failed to save import log: " . var_export($import_log->getErrors(), true));
			}

    	$csv_file_path = self::$file_path . $csv . ".csv";

			if(file_exists($csv_file_path)) {
				$table = array();
				$headers = array();
				if (($handle = fopen($csv_file_path, "r")) !== false) {
					if (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
						foreach ($line as $header) {
							// basic sanitization, remove non printable chars - This is required if the CSV file is
							// exported from the excel (as UTF8 CSV) as excel appends \ufeff to the beginning of CSV file.
							$header = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header);
							$headers[] = $header;
						}
					}

					while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
						$row = array();
						$header_count = 0;
						foreach ($line as $cel) {
							$row[$headers[$header_count++]] = $cel;
						}
						$table[] = $row;
					}
					fclose($handle);
				}

				$row_num = 0;
				$createAction = self::$contexts[$context]['createAction'];

				foreach ($table as $row) {
					$transaction = Yii::app()->db->beginTransaction();
					$import = new Import();
					$import->parent_log_id = $import_log->id;
					$row_num++;
					$errors = $this->$createAction($row, $import);
					if (!empty($errors)) {
						$transaction->rollback();
						$import->import_status_id = 2;
						$import->message = "Import failed on line " . $row_num . ": ";

						$flattened_errors = array();
						array_walk_recursive($errors, function ($item) use (&$flattened_errors) {
							$flattened_errors[] = $item;
						});

						foreach ($flattened_errors as $error) {
							$import->message .= "<br>" . $error;
						}
					} else {
						$transaction->commit();
						$import->import_status_id = 8;
					}

					if (!$import->save()) {
						\OELog::log("Failed to save import status: " . var_export( $import->getErrors(), true));
					}
				}

				$summary_table = array();

				$import_log->status = "Success";

				foreach (Import::model()->findAllByAttributes(['parent_log_id' => $import_log->id]) as $summary_import) {
					$summary = array();

					$status = $summary_import->import_status->status_value;

					//A status of 8 indicates a successful patient import
					if ($status != 8)
						$import_log->status = "Failure";

					$summary['Status'] = $status;
					$summary['Details'] = $summary_import->message;

					$summary_table[] = $summary;
				}
			}else {
				$summary_table[] = ['Status' => "Failure", 'Details' => "File has expired"];
			}

			if(!$import_log->save()) {
				\OELog::log("Failed to save import log: " . var_export($import_log->getErrors(), true));
			}

			//Remove uploaded files
			if(file_exists(self::$file_path)) {
				$file_list = glob(self::$file_path . "*");
				foreach ($file_list as $file) {
					unlink($file);
				}
				rmdir(self::$file_path);
			}

			$this->render(
				'summary',
				array(
					'errors' => $errors,
					'context' => $context,
					'table' => $summary_table,
					)
			);
    }

    private function createNewTrial($trial, $import)
    {
        $errors = array();
        if (empty($trial['name'])) {
            $errors[] = 'Trial has no name';
            return $errors;
        }
        //check that trial does not exist
        $new_trial = Trial::model()->findByAttributes(array('name' => $trial['name']));
        if ($new_trial !== null) {
            return $errors;
        }

        //check that principal investigator's user name exists in the system
        if(!empty($trial['principal_investigator'])) {
//            CERA-523 CERA-524 only active users can be made principal investigators
            $principal_investigator = User::model()->find('username = ? AND active = 1', array($trial['principal_investigator']));
            if(!isset($principal_investigator)) {
                $errors[] = 'The entered Principal Investigator does not exist in the system or is inactive.';
                return $errors;
            } else {
                $_SESSION['principal_investigator'] = $principal_investigator->id;
            }
        }

        //create new trial
        $new_trial = new Trial();
        $new_trial->name = $trial['name'];
        if (empty($trial['trial_type'])) {
            $trial['trial_type'] = TrialType::INTERVENTION_CODE;
        }
        $new_trial->trial_type_id = TrialType::model()->find('code = ?', array($trial['trial_type']))->id;
        $new_trial->description = !empty($trial['description']) ? $trial['description'] : null;
        $new_trial->owner_user_id =  Yii::app()->user->id;
        $new_trial->is_open = isset($trial['is_open']) && $trial['is_open'] !== '' ? $trial['is_open'] : false;
        $new_trial->started_date = !empty($trial['started_date']) ? $trial['started_date'] : null;
        $new_trial->closed_date = !empty($trial['closed_date']) ? $trial['closed_date'] : null;
        $new_trial->external_data_link = !empty($trial['external_data_link']) ? $trial['external_data_link'] : null;
        $new_trial->ethics_number = !empty($trial['ethics_number']) ? $trial['ethics_number'] : null;

        if (!$new_trial->save()) {
            $errors = $new_trial->getErrors();
        }

        return $errors;
    }

    private function createNewPatient($patient_raw_data, $import)
		{
			$errors = array();

			$expected_fields = array(
				'first_name',
				'last_name',
				'dob',
				'patient_source',
				'country',
				'CERA_ID',
				'gender',
				'maiden_name',
				'address_type',
				'address1',
				'address2',
				'city',
				'postcode',
				'county',
				'primary_phone',
				'email',
				'medicare_id',
				'RVEEH_UR',
				'date_of_death',
				'referred_to',
				'created_date',
				'diagnosis',
				'diagnosis_side_l',
				'diagnosis_side_r',
				'diagnosis_date',
				'vision_reading_l',
				'vision_reading_r',
				'vision_date',
				'vision_reading_scale',
				'unable_to_assess_l',
				'unable_to_assess_r',
				'eye_missing_l',
				'eye_missing_r',
				'visual_method_l',
				'visual_method_r'
			);

			//Check for unexpected fields
			foreach (array_keys($patient_raw_data) as $field) {
				if (!in_array($field, $expected_fields)) {
					$errors[] = "Unexpected field: " . $field;
				}
			}

			$mandatory_fields = array(
				'first_name',
				'last_name',
				'dob',
				'patient_source',
				'country',
				'CERA_ID');

			$mandatory_diagnosis_fields = array(
				'diagnosis_side_l',
				'diagnosis_side_r',
				'diagnosis_date');

			//Check if any mandatory field is blank
			foreach ($mandatory_fields as $field) {
				if (!array_key_exists($field, $patient_raw_data) || $patient_raw_data[$field] == '') {
					$errors[] = "Mandatory field missing: " . $field;
				}
			}

			//If a diagnosis exists and is not empty, the rest of the diagnosis fields are mandatory.
			//If a diagnosis does not exist, do not accept any diagnosis related fields
			if (array_key_exists('diagnosis', $patient_raw_data) && !empty($patient_raw_data['diagnosis'])) {
				foreach ($mandatory_diagnosis_fields as $diagnosis_field) {
					if (!array_key_exists($diagnosis_field, $patient_raw_data) || $patient_raw_data[$diagnosis_field] == '') {
						$errors[] = "Mandatory diagnosis field missing: " . $diagnosis_field;
					}
				}
			} else {
				foreach ($mandatory_diagnosis_fields as $diagnosis_field) {
					if ($patient_raw_data[$diagnosis_field] != '') {
						$errors[] = "Cannot add diagnosis fields for diagnosis that does not exist: " . $diagnosis_field;
					}
				}
			}

			if ((!empty($patient_raw_data['dob']) && strtotime($patient_raw_data['dob']) == false)
				|| (!empty($patient_raw_data['date_of_death']) && strtotime($patient_raw_data['date_of_death']) == false)
				|| (!empty($patient_raw_data['diagnosis_date']) && strtotime($patient_raw_data['diagnosis_date']) == false)) {
				$errors[] = "Dates must be in a valid format (dd-mm-yyyy)";
				return $errors;
			}

			//Throw errors if any of the following are true
			//-A mandatory field is missing
			//-A diagnosis is included but other fields are blank
			//-A diagnosis is not included but other fields are not blank
			//-An unexpected field is included
			if (!empty($errors)) {
				return $errors;
			}

			if (!empty($patient_raw_data['CERA_ID'])) {
				$duplicate_patient = Patient::model()->findByAttributes(array('hos_num' => $patient_raw_data['CERA_ID']));
				if ($duplicate_patient !== null) {
					$errors[] = "Duplicate CERA ID (" . $patient_raw_data['CERA_ID'] . ") found for patient: " . $patient_raw_data['first_name'] . " " . $patient_raw_data['last_name'];
					return $errors;
				}
			}

			if (!empty($patient_raw_data['medicare_id'])) {
				$duplicate_patient = Patient::model()->findByAttributes(array('nhs_num' => $patient_raw_data['medicare_id']));
				if ($duplicate_patient !== null) {
					$errors[] = "Duplicate Medicare ID (" . $patient_raw_data['medicare_id'] . ") found for patient: " . $patient_raw_data['first_name'] . " " . $patient_raw_data['last_name'];
					return $errors;
				}
			}

			$dupecheck_first_name = $patient_raw_data['first_name'];
			$dupecheck_last_name = $patient_raw_data['last_name'];

			//Change date to correct format
			$dupecheck_dob = Helper::convertNHS2MySQL(date("Y-m-d", strtotime(str_replace('/', '-', $patient_raw_data['dob']))));

			$dupecheck_sql = '
					SELECT p.*
					FROM patient p
					JOIN contact c
						ON c.id = p.contact_id
					WHERE p.dob = :dob
						AND (LOWER(c.first_name) = LOWER(:first_name))
						AND (LOWER(c.last_name) = LOWER(:last_name))
						AND p.deleted = 0';

			$patient_duplicates = Patient::model()->findAllBySql(
				$dupecheck_sql,
				array(
					':dob' => $dupecheck_dob,
					':first_name' => $dupecheck_first_name,
					':last_name' => $dupecheck_last_name));

			if (count($patient_duplicates) > 0) {
				$errors[] = "Validation error(s) for patient: " . $dupecheck_first_name . " " . $dupecheck_last_name;
				foreach ($patient_duplicates as $duplicate) {
					$errors[] = "Duplicate found: " . $duplicate->contact->first_name . " " . $duplicate->contact->last_name . " with DOB " . $duplicate->dob;
				}
				return $errors;
			}

			$contact = new Contact();
			$contact_cols = array(
				array('var_name' => 'nick_name', 'default' => null,),
				array('var_name' => 'primary_phone', 'default' => null,),
				array('var_name' => 'title', 'default' => null,),
				array('var_name' => 'first_name', 'default' => null,),
				array('var_name' => 'last_name', 'default' => null,),
				array('var_name' => 'maiden_name', 'default' => null,),
				array('var_name' => 'qualifications', 'default' => null,),
				array('var_name' => 'contact_label_id', 'default' => null,),
			);

			foreach ($contact_cols as $col) {
				$contact->$col['var_name'] = !empty($patient_raw_data[$col['var_name']]) ? $patient_raw_data[$col['var_name']] : $col['default'];
			}

			if (!$contact->save()) {
				return $contact->getErrors();
			}

			$address = new Address();
			$address_cols = array(
				array('var_name' => 'address1', 'default' => null,),
				array('var_name' => 'address2', 'default' => null,),
				array('var_name' => 'city', 'default' => null,),
				array('var_name' => 'postcode', 'default' => null,),
				array('var_name' => 'county', 'default' => null,),
				array('var_name' => 'country_id', 'default' => 15,),
				array('var_name' => 'email', 'default' => null,),
				array('var_name' => 'date_start', 'default' => null,),
				array('var_name' => 'date_end', 'default' => null,),
				array('var_name' => 'address_type_id', 'default' => null,),
			);

			foreach ($address_cols as $col) {
				$address->$col['var_name'] = !empty($patient_raw_data[$col['var_name']]) ? $patient_raw_data[$col['var_name']] : $col['default'];
			}

			//Added separately because these fields are parsed from text instead of ids
			if (array_key_exists('address_type', $patient_raw_data) && !empty($patient_raw_data['address_type'])) {
				$address->address_type_id = AddressType::model()->findByAttributes(['name' => $patient_raw_data['address_type']])->id;
			}
			if (array_key_exists('country', $patient_raw_data) && $patient_raw_data['country']) {
				$address->country = $patient_raw_data['country'];
			}

			$address->contact_id = $contact->id;

			if (!$address->save()) {
				$errors[] = $address->getErrors();
				return $errors;
			}

			$new_patient = new Patient();
			$patient_cols = array(
				array('var_name' => 'pas_key', 'default' => null,),
				array('var_name' => 'dob', 'default' => null,),
				array('var_name' => 'gender', 'default' => 'U',),
				array('var_name' => 'nhs_num', 'default' => null,),
				array('var_name' => 'practice_id', 'default' => null,),
				array('var_name' => 'ethnic_group_id', 'default' => null,),
				array('var_name' => 'archive_no_allergies_date', 'default' => null,),
				array('var_name' => 'archive_no_family_history_date', 'default' => null,),
				array('var_name' => 'archive_no_risks_date', 'default' => null,),
				array('var_name' => 'deleted', 'default' => null,),
				array('var_name' => 'nhs_num_status_id', 'default' => null,),
				array('var_name' => 'is_local', 'default' => 1,),
				array('var_name' => 'patient_source', 'default' => 0,),
			);

			foreach ($patient_cols as $col) {
				$new_patient->$col['var_name'] = isset($patient_raw_data[$col['var_name']]) && $patient_raw_data[$col['var_name']] !== ''
					? $patient_raw_data[$col['var_name']] : $col['default'];
			}

			//Set values that cannot be directly translated from csv
			if (array_key_exists('medicare_id', $patient_raw_data) && $patient_raw_data['medicare_id']) {
				$new_patient->nhs_num = $patient_raw_data['medicare_id'];
			}

			if (array_key_exists('date_of_death', $patient_raw_data) && !empty($patient_raw_data['date_of_death'])) {
				$new_patient->date_of_death = date("Y-m-d", strtotime(str_replace('/', '-', $patient_raw_data['date_of_death'])));
			}

			$new_patient->hos_num = !empty($patient_raw_data['CERA_ID']) ? $patient_raw_data['CERA_ID'] : Patient::autoCompleteHosNum();
			$new_patient->contact_id = $contact->id;

			$new_patient->setScenario('other_register');
			if(!$new_patient->save()){
					$errors[] = $new_patient->getErrors();
					return $errors;
			}

			//Add a RVEEH_UR value for patient
			if(array_key_exists('RVEEH_UR', $patient_raw_data) && $patient_raw_data['RVEEH_UR']) {
				$patient_RVEEH_UR = new PatientIdentifier();

				$patient_RVEEH_UR->patient_id = $new_patient->id;
				$patient_RVEEH_UR->code = 'RVEEH_UR';
				$patient_RVEEH_UR->value = $patient_raw_data['RVEEH_UR'];

				if(!$patient_RVEEH_UR->save()) {
					$errors[] = "Failed to validate RHEEV_UR:";
					$errors[] = $patient_RVEEH_UR->getErrors();
					return $errors;
				}
			}
			//patient contact assignments

			//referred to
			if(!empty($patient_raw_data['referred_to'])){
				//Find if exists
				$referred_to = User::model()->findByAttributes(array(
						'username' => $patient_raw_data['referred_to']
				));
				if ($referred_to === null) {
						$errors[] = 'Cannot find referred to user: ' . $patient_raw_data['referred_to'];
						return $errors;
				}
				$pat_ref = new PatientUserReferral();
				$pat_ref->user_id = $referred_to->id;
				$pat_ref->patient_id = $new_patient->id;

				if (!$pat_ref->save()) {
						$errors[] = 'Could not save referred to user';
						array_unshift($errors, $pat_ref->getErrors());
						return $errors;
				}
			}

			//Create events and elements for diagnosis
			if(!empty($patient_raw_data['diagnosis']) || !empty($patient_raw_data['vision_l']) || !empty($patient_raw_data['vision_r'])) {

				//We need an episode to store patient exam data
				$episode = new Episode();

				//Set the proper firm and subspecialty for this
				$config_default_firm = Yii::app()->params['default_patient_import_context'];
				$config_default_subspecialty = Yii::app()->params['default_patient_import_subspecialty'];

				if(isset($config_default_firm) && isset($config_default_subspecialty)) {
					$subspecialty = Subspecialty::model()->findByAttributes(['ref_spec' => $config_default_subspecialty]);

					if(!isset($subspecialty)) {
						$errors[] = "Subspecialty not found: " . $config_default_subspecialty;
					}

					$subspecialty_assignment = ServiceSubspecialtyAssignment::model()->findByAttributes(['subspecialty_id' => $subspecialty->id]);

					if(!isset($subspecialty_assignment)) {
						$errors[] = "Could not find subspecialty assignment for " . $subspecialty->name;
					}

					$firm = Firm::model()->findByAttributes(
						['name' => $config_default_firm,
							'service_subspecialty_assignment_id' => $subspecialty_assignment->id]);

					if(isset($firm)) {
						$episode->firm_id = $firm->id;
					}else {
						$errors[] = "Context " . $config_default_firm . " is not applicable to subspecialty " . $config_default_subspecialty;
					}
				}else {
					$errors[] = "default_patient_import_context or default_patient_import_subspecialty missing from configuration file";
				}

				$episode->patient_id = $new_patient->id;
				if(!$episode->save()){
						$errors[] = 'Could not save new episode';
						array_unshift($errors, $episode->getErrors());
						return $errors;
				}

				//If the patient has a diagnosis, create an examination event to store it
				if(!empty($patient_raw_data['diagnosis'])) {
					$diagnosis_event = new Event();
					$diagnosis_event->event_type_id = EventType::model()->findByAttributes(['name' => 'Examination'])->id;
					$diagnosis_event->episode_id = $episode->id;
					$diagnosis_event->firm_id = $episode->firm_id;

					if (!$diagnosis_event->save()) {
						$errors[] = 'Could not save new diagnosis event';
						array_unshift($errors, $diagnosis_event->getErrors());
						return $errors;
					}

					//create diagnoses element
					$diagnoses_element = new \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses();
					$diagnoses_element->event_id = $diagnosis_event->id;

					if (!$diagnoses_element->save()) {
						$errors[] = 'Could not save diagnoses element';
						array_unshift($errors, $diagnoses_element->getErrors());
					}

					if($patient_raw_data['diagnosis_side_l'] != 'Y' && $patient_raw_data['diagnosis_side_l'] != 'N') {
						$errors[] = 'Field diagnosis_side_l must be Y or N';
					}
					if($patient_raw_data['diagnosis_side_r'] != 'Y' && $patient_raw_data['diagnosis_side_r'] != 'N') {
						$errors[] = 'Field diagnosis_side_r must be Y or N';
					}

					$diagnosis_left = $patient_raw_data['diagnosis_side_l'] == 'Y';
					$diagnosis_right = $patient_raw_data['diagnosis_side_r'] == 'Y';

					//Derive affected eye by performing binary style operation on left and right booleans
					//This formula maps two booleans (One for each eye) to a number from 0-3 inclusive
					//A value of 0 indicates no eye, 1, 2 and 3 indicate right, left, and both eyes respectively
					//This is the fastest way to map two boolean values to an eye in the database
					$diagnosis_eye_id = ($diagnosis_left * 1 + $diagnosis_right * 2);

					//Check if no eye is affected by diagnosis
					if ($diagnosis_eye_id == 0) {
						$errors[] = "Cannot save diagnosis that does not affect an eye";
						return $errors;
					}

					$found_disorder_ids =
						Yii::app()->db->createCommand(
							'SELECT id 
								FROM  disorder 
								WHERE REGEXP_REPLACE(term, \'[^A-Za-z0-9]\', \'\') = 
								REGEXP_REPLACE(\''. $patient_raw_data['diagnosis'] . '\', \'[^A-Za-z0-9]\', \'\')')->queryAll();

					if (count($found_disorder_ids) == 0) {
						$errors[] = "Could not find disorder matching name: " . $patient_raw_data['diagnosis'];
						return $errors;
					} else {
						$disorder = Disorder::model()->findByPk($found_disorder_ids[0]);
					}

					$diagnosis = new \OEModule\OphCiExamination\models\OphCiExamination_Diagnosis();
					$diagnosis->element_diagnoses_id = $diagnoses_element->id;
					$diagnosis->disorder_id = $disorder->id;
					$diagnosis->eye_id = $diagnosis_eye_id;
					$diagnosis->date = date("Y-m-d", strtotime(str_replace('/', '-', $patient_raw_data['diagnosis_date'])));
					$diagnosis->principal = true;

					if($diagnosis->date < $new_patient->dob) {
						$errors[] = "Diagnosis date cannot predate patient date of birth";
					}
					if($diagnosis->date > date("Y-m-d")) {
						$errors[] = "Diagnosis date cannot postdate current date";
					}

					if (!$diagnosis->save()) {
						$errors[] = 'Could not save diagnosis';
						$errors[] = $diagnosis->getErrors();
						return $errors;
					}

					$episode->disorder_id = $disorder->id;
				}

				//Check whether we have readings for left and right
				$has_reading_left = !empty($patient_raw_data['vision_reading_l']);
				$has_reading_right = !empty($patient_raw_data['vision_reading_r']);

				//If the patient has visual acuity reading(s), create an examination event to store them
				if($has_reading_left || $has_reading_right) {
					$errors[] = "Visual Acuity functionality not yet implemented";
					return $errors;
				}
			}

			if(empty($errors)) {
				$import->message = "Import successful for patient: " . $contact->first_name . " " . $contact->last_name;
			}

			return $errors;
    }

    private function createNewTrialPatient($trial_patient, $import)
    {
        $errors = array();
        //trial
        $trial = null;
        if(!empty($trial_patient['trial_name'])){
            $trial = Trial::model()->findByAttributes(array('name' => $trial_patient['trial_name']));
        }
        if ($trial === null){
            $errors[] = 'trial not found, please check the trial name';
            return $errors;
        }

        //patient
        $patient = null;
        if(!empty($trial_patient['CERA_number'])){
            $patient = Patient::model()->findByAttributes(array('hos_num' => $trial_patient['CERA_number']));
        }
        if ($patient === null){
            $errors[] = 'patient not found, please check the CERA number';
            return $errors;
        }

        $new_trial_pat = new TrialPatient();
        $trial_pat_cols = array(
            array('var_name' => 'external_trial_identifier', 'default' => null,),
            array('var_name' => 'status_id'                , 'default' => TrialPatientStatus::model()->find('code = "SHORTLISTED"')->id),
            array('var_name' => 'treatment_type_id'        , 'default' => TreatmentType::model()->find('code = "UNKNOWN"')->id),
            array('var_name' => 'created_date'             , 'default' => null,),
        );

        foreach ($trial_pat_cols as $col){
            $new_trial_pat->$col['var_name'] =
                !empty($new_trial_pat[$col['var_name']]) ? $new_trial_pat[$col['var_name']] : $col['default'];
        }

        $new_trial_pat->patient_id = $patient->id;
        $new_trial_pat->trial_id = $trial->id;

        if(!$new_trial_pat->save()){
            return $new_trial_pat->getErrors();
        }
    }

}