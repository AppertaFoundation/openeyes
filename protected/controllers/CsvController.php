<?php
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

        $table = array();
        $headers = array();
        if (isset($_FILES['Csv']['tmp_name']['csvFile']) && $_FILES['Csv']['tmp_name']['csvFile'] !== "") {
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
                        $row[$headers[$header_count++]] = $cel;
                    }
                    $table[] = $row;
                }
                fclose($handle);
            }
        }

        $csv_id = md5_file($_FILES['Csv']['tmp_name']['csvFile']);

        if(!file_exists(self::$file_path)) {
					mkdir(self::$file_path);
				}

        copy($_FILES['Csv']['tmp_name']['csvFile'], self::$file_path . $csv_id . ".csv");

        $this->render('preview', array('table' => $table, 'csv_id' => $csv_id, 'context' => $context));
    }

    public function actionImport($context, $csv)
    {
    	$import_log = new ImportLog();
			$import_log->import_user_id = Yii::app()->user->id;
			$import_log->startdatetime = date('Y-m-d H:i:s');
			$import_log->status = "Failure";
			if(!$import_log->save()) {
				\OELog::log("WARNING! FAILED TO SAVE IMPORT LOG!");
			}

    	$csv_file_path = self::$file_path . $csv . ".csv";

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

			$errors = null;
			$row_num = 0;
			$createAction = self::$contexts[$context]['createAction'];

			foreach ($table as $row) {
					$transaction = Yii::app()->db->beginTransaction();
					$import = new Import();
					$import->parent_log_id = $import_log->id;
					$row_num++;
					$errors = $this->$createAction($row, $import);
					if(!empty($errors)) {
						$transaction->rollback();
						$import->import_status_id = 2;
						$import->message = "Import failed on line " . $row_num . ": ";

						$flattened_errors = array();
						array_walk_recursive($errors, function($item) use (&$flattened_errors)  { $flattened_errors[] = $item; });

						foreach ($flattened_errors as $error) {
							$import->message .= "<br>" . $error;
						}
					}else {
						$transaction->commit();
						$import->import_status_id = 8;
					}

					if(!$import->save()) {
						\OELog::log("WARNING! FAILED TO SAVE IMPORT STATUS!");
					}
			}

			$summary_table = array();

			$import_log->status = "Success";

			foreach (Import::model()->findAllByAttributes(['parent_log_id' => $import_log->id]) as $summary_import) {
				$summary = array();

				$status = $summary_import->import_status->status_value;

				if($status != 8)
					$import_log->status = "Failure";

				$summary['Status'] = $status;
				$summary['Details'] = $summary_import->message;

				$summary_table[] = $summary;
			}

			if(!$import_log->save()) {
				\OELog::log("WARNING! FAILED TO SAVE IMPORT LOG!");
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
            $principal_investigator = User::model()->find('username = ?', array($trial['principal_investigator']));
            if(!isset($principal_investigator)) {
                $errors[] = 'The entered Principal Investigator does not exist in the system.';
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
					'diagnosis_principal',
					'diagnosis_date');

				//If a mandatory field is missing or empty, throw error
				foreach ($mandatory_fields as $field) {
					if(!array_key_exists($field, $patient_raw_data) || $patient_raw_data[$field] == ''){
						$errors[] = "Mandatory field missing: " . $field;
					}
				}

				//If a diagnosis exists and is not empty, the rest of the diagnosis fields are mandatory.
				//If a diagnosis does not exist, do not accept any diagnosis related fields
				if(array_key_exists('diagnosis', $patient_raw_data) && $patient_raw_data['diagnosis'] != '') {
					foreach($mandatory_diagnosis_fields as $diagnosis_field) {
						if(!array_key_exists($diagnosis_field, $patient_raw_data) || $patient_raw_data[$diagnosis_field] == '')
						{
							$errors[] = "Mandatory diagnosis field missing: " . $diagnosis_field;
						}
					}
				}else {
					foreach($mandatory_diagnosis_fields as $diagnosis_field) {
						if(array_key_exists($diagnosis_field, $patient_raw_data) || $patient_raw_data[$diagnosis_field] != '')
						{
							$errors[] = "Cannot add diagnosis fields for diagnosis that does not exist: " . $diagnosis_field;
						}
					}
				}

				//Throw errors if mandatory fields are missing or diagnosis fields are included that are not attached to a diagnosis
				if(count($errors) > 0)
				{
					return $errors;
				}

				if(!empty($patient_raw_data['CERA_ID'])){
            $duplicate_patient = Patient::model()->findByAttributes(array('hos_num' => $patient_raw_data['CERA_ID']));
            if ($duplicate_patient !== null){
							$errors[] = "Duplicate CERA ID (" . $patient_raw_data['CERA_ID'] . ") found for patient: " . $patient_raw_data['first_name'] . " " . $patient_raw_data['last_name'];
							return $errors;
            }
        }

				$dupecheck_first_name = $patient_raw_data['first_name'];
				$dupecheck_last_name = $patient_raw_data['last_name'];

				//Change date to correct format
				$dupecheck_dob = date("Y-m-d", strtotime(str_replace('/', '-', $patient_raw_data['dob'])));

				//To find duplicates, the dob must be in the form yyyy-mm-dd
				$patient_duplicates = Patient::findDuplicates($dupecheck_last_name, $dupecheck_first_name, $dupecheck_dob, null);

				if(count($patient_duplicates) > 0) {
					$errors[] = "Patient duplicate found for patient: " . $dupecheck_first_name . " " . $dupecheck_last_name . " with DOB " . $dupecheck_dob;
					foreach ($patient_duplicates as $duplicate) {
						$errors[] = "Duplicate: " . $duplicate->contact->first_name . " " . $duplicate->contact->last_name . " with DOB " . $duplicate->dob;
					}
					return $errors;
				}

        $contact = new Contact();
        $contact_cols = array(
            array('var_name' => 'nick_name'       , 'default' => null,),
            array('var_name' => 'primary_phone'   , 'default' => null,),
            array('var_name' => 'title'           , 'default' => null,),
            array('var_name' => 'first_name'      , 'default' => null,),
            array('var_name' => 'last_name'       , 'default' => null,),
            array('var_name' => 'maiden_name'     , 'default' => null,),
            array('var_name' => 'qualifications'  , 'default' => null,),
            array('var_name' => 'contact_label_id', 'default' => null,),
        );

        foreach ($contact_cols as $col){
            $contact->$col['var_name'] = !empty($patient_raw_data[$col['var_name']]) ? $patient_raw_data[$col['var_name']] : $col['default'];
        }

        if(!$contact->save()){
            return $contact->getErrors();
        }

        $address = new Address();
        $address_cols = array(
            array('var_name' => 'address1'       , 'default' => null,),
            array('var_name' => 'address2'       , 'default' => null,),
            array('var_name' => 'city'           , 'default' => null,),
            array('var_name' => 'postcode'       , 'default' => null,),
            array('var_name' => 'county'         , 'default' => null,),
            array('var_name' => 'country_id'     , 'default' => 15,),
            array('var_name' => 'email'          , 'default' => null,),
            array('var_name' => 'date_start'     , 'default' => null,),
            array('var_name' => 'date_end'       , 'default' => null,),
            array('var_name' => 'address_type_id', 'default' => null,),
        );

        foreach ($address_cols as $col){
            $address->$col['var_name'] = !empty($patient_raw_data[$col['var_name']]) ? $patient_raw_data[$col['var_name']] : $col['default'];
        }

        //Added separately because these fields are parsed from text instead of ids
        if(array_key_exists('address_type', $patient_raw_data) && $patient_raw_data['address_type']) {
					$address->type = $patient_raw_data['address_type'];
				}
				if(array_key_exists('country', $patient_raw_data) && $patient_raw_data['country']) {
					$address->country = $patient_raw_data['country'];
				}

        $address->contact_id = $contact->id;

        if(!$address->save()){
            return $address->getErrors();
        }

        $new_patient = new Patient();
        $patient_cols = array(
            array('var_name' => 'pas_key'                       , 'default' => null,),
            array('var_name' => 'dob'                           , 'default' => null,),
            array('var_name' => 'gender'                        , 'default' => 'U',),
            array('var_name' => 'nhs_num'                       , 'default' => null,),
            array('var_name' => 'date_of_death'                 , 'default' => null,),
            array('var_name' => 'practice_id'                   , 'default' => null,),
            array('var_name' => 'ethnic_group_id'               , 'default' => null,),
            array('var_name' => 'archive_no_allergies_date'     , 'default' => null,),
            array('var_name' => 'archive_no_family_history_date', 'default' => null,),
            array('var_name' => 'archive_no_risks_date'         , 'default' => null,),
            array('var_name' => 'deleted'                       , 'default' => null,),
            array('var_name' => 'nhs_num_status_id'             , 'default' => null,),
            array('var_name' => 'is_deceased'                   , 'default' => null,),
            array('var_name' => 'is_local'                      , 'default' => 1,),
            array('var_name' => 'patient_source'                , 'default' => 0,),
        );

        foreach ($patient_cols as $col){
            $new_patient->$col['var_name'] = isset($patient_raw_data[$col['var_name']]) && $patient_raw_data[$col['var_name']] !== ''
                ? $patient_raw_data[$col['var_name']] : $col['default'];
        }

        //Set values that cannot be directly translated from csv
				if(array_key_exists('medicare_ID', $patient_raw_data) && $patient_raw_data['medicare_ID']) {
					$new_patient->nhs_num = $patient_raw_data['medicare_ID'];
				}

        $new_patient->hos_num = !empty($patient_raw_data['CERA_ID']) ? $patient_raw_data['CERA_ID'] : Patient::autoCompleteHosNum();
				$new_patient->contact_id = $contact->id;

        $new_patient->setScenario('other_register');
        if(!$new_patient->save()){
            return $new_patient->getErrors();
        }

        //Add a RVEEH_UR value for patient
				if(array_key_exists('RVEEH_UR', $patient_raw_data) && $patient_raw_data['RVEEH_UR']) {
					$patient_RVEEH_UR = new PatientIdentifier();

					$patient_RVEEH_UR->patient_id = $new_patient->id;
					$patient_RVEEH_UR->code = 'RVEEH_UR';
					$patient_RVEEH_UR->value = $patient_raw_data['RVEEH_UR'];

					if(!$patient_RVEEH_UR->save()) {
						$errors[] = $patient_RVEEH_UR->getErrors();
						return $errors;
					}
				}
        //patient contact assignments

        //referred to
        if(!empty($patient['referred_to_first_name']) || !empty($patient['referred_to_last_name'])){
            if(!empty($patient['referred_to_first_name']) && !empty($patient['referred_to_last_name'])) {
                //Find if exists
                $referred_to = User::model()->findByAttributes(array(
                    'first_name' => $patient['referred_to_first_name'],
                    'last_name' => $patient['referred_to_last_name'],
                ));
                if ($referred_to === null) {
                    $errors[] = 'Cannot find referred to user';
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
            } else {
                $errors[] = 'Both names must be present to import referred_to for this patient';
                return $errors;
            }
        }

        //optom
        if(!empty($patient['optom_first_name']) || !empty($patient['optom_last_name'])){
            if(!empty($patient['optom_first_name']) && !empty($patient['optom_last_name'])) {
                $optom_label = ContactLabel::model()->findByAttributes(array('name' => 'Optometrist'));
                $optom_contact = Contact::model()->findByAttributes(array(
                    'id' => $optom_label->id,
                    'first_name' => $patient['optom_first_name'],
                    'last_name' => $patient['optom_last_name'],
                ));
                if ($optom_contact === null) {
                    $optom_contact = new Contact();
                    $optom_contact->first_name = $patient['optom_first_name'];
                    $optom_contact->last_name = $patient['optom_last_name'];
                    $optom_contact->contact_label_id = $optom_label->id;
                    if (!$optom_contact->save()) {
                        $errors[] = 'Could not save new optometrist contact';
                        array_unshift($errors, $optom_contact->getErrors());
                        return $errors;
                    }
                }
                $pat_con = new PatientContactAssignment();
                $pat_con->contact_id = $optom_contact->id;
                $pat_con->patient_id = $new_patient->id;
                if (!$pat_con->save()) {
                    $errors[] = 'Could not save optometrist contact assignment';
                    array_unshift($errors, $pat_con->getErrors());
                    return $errors;
                }
                $new_gp = new Gp();
                $new_gp->obj_prof = 0;
                $new_gp->nat_id = 0;
                $new_gp->contact = $pat_con;
                if (!$new_gp->save()) {
                    $errors[] = 'Could not save new practitioner contact';
                    array_unshift($errors, $new_gp->getErrors());
                    return $errors;
                }
            } else {
                $errors[] = 'Both names must be present to import optom';
                return $errors;
            }
        }

        //opthal
        if(!empty($patient['opthal_first_name']) || !empty($patient['opthal_last_name'])){
            if(!empty($patient['opthal_first_name']) && !empty($patient['opthal_last_name'])) {
                $opthal_label = ContactLabel::model()->findByAttributes(array('name' => 'Consultant Ophthalmologist'));
                $opthal_contact = Contact::model()->findByAttributes(array(
                    'id' => $opthal_label->id,
                    'first_name' => $patient['opthal_first_name'],
                    'last_name' => $patient['opthal_last_name'],
                ));
                if ($opthal_contact === null) {
                    $opthal_contact = new Contact();
                    $opthal_contact->first_name = $patient['opthal_first_name'];
                    $opthal_contact->last_name = $patient['opthal_last_name'];
                    $opthal_contact->contact_label_id = $opthal_label->id;
                    if (!$opthal_contact->save()) {
                        $errors[] = 'Could not save new ophthalmologist contact';
                        array_unshift($errors, $opthal_contact->getErrors());
                        return $errors;
                    }
                }
                $pat_con = new PatientContactAssignment();
                $pat_con->contact_id = $opthal_contact->id;
                $pat_con->patient_id = $new_patient->id;
                if (!$pat_con->save()) {
                    $errors[] = 'Could not save ophthalmologist contact assignment';
                    array_unshift($errors, $pat_con->getErrors());
                    return $errors;
                }
                $new_gp = new Gp();
                $new_gp->obj_prof = 0;
                $new_gp->nat_id = 0;
                $new_gp->contact_id = $opthal_contact->id;
                if (!$new_gp->save()) {
                    $errors[] = 'Could not save new opthal contact';
                    array_unshift($errors, $new_gp->getErrors());
                    return $errors;
                }
            } else {
                $errors[] = 'Both names must be present to import ophthalmologist';
                return $errors;
            }
        }

        //Gp
        if(!empty($patient['gp_first_name']) || !empty($patient['gp_last_name'])){
            if(!empty($patient['gp_first_name']) && !empty($patient['gp_last_name'])) {
                $gp_label = ContactLabel::model()->findByAttributes(array('name' => 'General Practitioner'));
                $gp_contact = Contact::model()->findByAttributes(array(
                    'id' => $gp_label->id,
                    'first_name' => $patient['gp_first_name'],
                    'last_name' => $patient['gp_last_name'],
                ));
                if ($gp_contact === null) {
                    $gp_contact = new Contact();
                    $gp_contact->first_name = $patient['gp_first_name'];
                    $gp_contact->last_name = $patient['gp_last_name'];
                    $gp_contact->contact_label_id = $gp_label->id;
                    if (!$gp_contact->save()) {
                        $errors[] = 'Could not save new '.Yii::app()->params['gp_label'].' contact';
                        array_unshift($errors, $gp_contact->getErrors());
                        return $errors;
                    }
                    $new_gp = new Gp();
                    $new_gp->obj_prof = 0;
                    $new_gp->nat_id = 0;
                    $new_gp->contact_id = $gp_contact->id;
                    if (!$new_gp->save()) {
                        $errors[] = 'Could not save new practitioner contact';
                        array_unshift($errors, $new_gp->getErrors());
                        return $errors;
                    }
                }
                $pat_con = new PatientContactAssignment();
                $pat_con->contact_id = $gp_contact->id;
                $pat_con->patient_id = $new_patient->id;
                if (!$pat_con->save()) {
                    $errors[] = 'Could not save general practitioner contact';
                    array_unshift($errors, $pat_con->getErrors());
                    return $errors;
                }
            } else {
                $errors[] = 'Both names are required to import '.Yii::app()->params['gp_label'];
                return $errors;
            }
        }

				//Create events and elements for diagnosis and visual acuity
				if(!empty($patient_raw_data['diagnosis'])) {
					$context = Firm::model()->findByAttributes(array(
							'name' => !empty($patient_raw_data['context']) ? $patient_raw_data['context'] :  'Medical Retinal firm'
					));
					$episode = new Episode();
					$episode->firm = $context;
					$episode->patient_id = $new_patient->id;
					if(!$episode->save()){
							$errors[] = 'Could not save new episode';
							array_unshift($errors, $episode->getErrors());
							return $errors;
					}

					$event = new Event();
					$event->event_type_id = EventType::model()->findByAttributes(['name' => 'Examination'])->id;
					$event->episode_id = $episode->id;

					if(!$event->save()){
							$errors[] = 'Could not save new event';
							array_unshift($errors, $event->getErrors());
							return $errors;
					}

					//create diagnoses element
					$diagnoses_element = new \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses();
					$diagnoses_element->event_id = $event->id;

					if(!$diagnoses_element->save()){
							$errors[] = 'could not save diagnoses element';
							array_unshift($errors, $diagnoses_element->getErrors());
					}

					//Warning: This will accept any value that is not 'Y' as false
					$left = $patient_raw_data['diagnosis_side_l'] == 'Y';
					$right = $patient_raw_data['diagnosis_side_r'] == 'Y';

					$eye_id = ($left * 2 + $right * 1);//This is terrible. Fix it.

					//Abusing soundex to strip commas and quotes and to more fuzzily match a potentially misspelled diagnosis
					$disorder_info = Yii::app()->db->createCommand('select id from disorder WHERE SOUNDEX(term) = SOUNDEX(\'' . $patient_raw_data['diagnosis'] . '\')')->queryAll();

					if(count($disorder_info) == 0) {
						$errors[] = "Could not find disorder matching name: " . $patient_raw_data['diagnosis'];
						return $errors;
					}else {
						$disorder = Disorder::model()->findByPk($disorder_info[0]['id']);
					}
//					if($disorder == null) {
//						$errors[] = "Could not find disorder matching name: " . $patient['diagnosis'];
//						return $errors;
//					}

					$diagnosis = new \OEModule\OphCiExamination\models\OphCiExamination_Diagnosis();
					$diagnosis->element_diagnoses_id = $diagnoses_element->id;
					$diagnosis->disorder_id = $disorder->id;
					$diagnosis->eye_id = $eye_id;
					$diagnosis->date = date("Y-m-d", strtotime(str_replace('/', '-', $patient_raw_data['diagnosis_date'])));
					$diagnosis->principal = $patient_raw_data['diagnosis_principal'];

					if($diagnosis->eye_id == 0) {
						$errors[] = "Cannot save diagnosis that does not affect an eye";
						return $errors;
					}

					if(!$diagnosis->save()){
							$errors[] = 'Could not save diagnosis';
							$errors[] = $diagnosis->getErrors();
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