<?php

class AnalyticsController extends BaseController
{
    const DAYTIME_ONE = 86400;
    const DAYTIME_THREE = 259200;
    const WEEKTIME = 604800;
    const PERIOD_DAY = 1;
    const PERIOD_WEEK = 7;
    const PERIOD_MONTH = 30;
    const PERIOD_YEAR = 365;
    const FOLLOWUP_WEEK_LIMITED = 78;
    private $current_user ;

  public $layout = '//layouts/events_and_episodes';
  protected $patient_list = array();
  protected $filters;
  protected $surgeon;
  public $fixedHotlist = false;
  protected $custom_csv_data = array();

    /**
     * @param $subspecialty_name
     * @return int
     * Get subspecialty ID by name, used in each actionXXX function to filter data by subspecialty.
     */
  protected function getSubspecialtyID($subspecialty_name){
      return Subspecialty::model()->findByAttributes(array('name'=>$subspecialty_name))->id;
  }

  public function accessRules()
  {
    return array(
      array('allow',
        'actions' => array('analyticsReports','cataract', 'medicalRetina', 'glaucoma', 'updateData','allSubspecialties'),
        'users'=> array('@')
      ),
    );
  }
  public function actionAnalyticsReports(){
      $this->current_user = User::model()->findByPk(Yii::app()->user->id);
      $firm_id = $this->current_user->last_firm_id;
      $subspecialty = '';
      if (isset(Firm::model()->findAllByPk($firm_id)->serviceSubspecialtyAssignment)){
          $subspecialty = Firm::model()->findByPk($firm_id)->serviceSubspecialtyAssignment->subspecialty->name;
      }

      switch ($subspecialty){
          case "Glaucoma":
              $this->actionGlaucoma();
              break;
          case "Cataract":
              $this->actionCataract();
              break;
          case "Medical Retina":
              $this->actionMedicalRetina();
              break;
          default:
              $this->actionAllSubspecialties();
              break;
      }
  }

    /**
     * Function actionCataract(), actionMedicalRetina(), actionGlaucoma() are the main function for those three subspecialties
     * The function grab data for all the plots.
     */
    public function actionAllSubspecialties(){
        $this->getPatientList();
        $this->checkAuth();
        $subspecialty_id = null;
        $this->current_user = User::model()->findByPk(Yii::app()->user->id);
        $disorder_data = $this->getDisorders($subspecialty_id);
        $this->filters = array(
            'date_from' => 0,
            'date_to' => Helper::mysqlDate2JsTimestamp(date("Y-m-d h:i:s")),
        );
        $follow_patient_list = $this->getFollowUps($subspecialty_id);
        $common_ophthalmic_disorders = $this->getCommonDisorders($subspecialty_id,true);
        $clinical_data = array(
            'title' => 'Disorders Section',
            'x' => $disorder_data['x'],
            'y' => $disorder_data['y'],
            'text' => $disorder_data['text'],
            'customdata' =>$disorder_data['customdata'],
            'csv_data'=>$disorder_data['csv_data'],
        );

        $current_user = User::model()->findByPk(Yii::app()->user->id);
        if (isset($this->surgeon)){
            $user_list = null;
        }else{
            $user_list = User::model()->findAll();
        }

        $this->render('/analytics/analytics_container',
            array(
                'specialty'=>'All',
                'service_data'=> $follow_patient_list,
                'clinical_data' => $clinical_data,
                'patient_list' => $this->patient_list,
                'user_list' => $user_list,
                'current_user'=> $current_user,
                'common_disorders'=> $common_ophthalmic_disorders,
            )
        );
    }
  public function actionCataract(){
      $this->getPatientList();
      $assetManager = Yii::app()->getAssetManager();
      $assetManager->registerScriptFile('js/dashboard/OpenEyes.Dash.js', null, null, AssetManager::OUTPUT_ALL, false);
      $current_user = User::model()->findByPk(Yii::app()->user->id);
      $event_list = $this->queryCataractEventList();
      if (isset($this->surgeon)){
          $user_list = null;
      }else{
          $user_list = User::model()->findAll();
      }
      $this->render('/analytics/analytics_container',
          array(
              'specialty'=>'Cataract',
              'clinical_data'=> array(),
              'service_data'=> array(),
              'custom_data' => array(),
              'event_list' => $event_list,
              'patient_list' => $this->patient_list,
              'user_list' => $user_list,
              'current_user'=>$current_user,
          )
      );
  }
  public function actionMedicalRetina(){
      $this->getPatientList();
      list($follow_patient_list, $common_ophthalmic_disorders, $current_user, $user_list, $custom_data,$clinical_data) = $this->getClinicalSpecialityData('Medical Retina');
      $this->render('/analytics/analytics_container',
          array(
              'specialty' => 'Medical Retina',
              'service_data' => $follow_patient_list,
              'custom_data' => $custom_data,
              'patient_list' => $this->patient_list,
              'user_list' => $user_list,
              'current_user' => $current_user,
              'common_disorders' => $common_ophthalmic_disorders,
              'clinical_data'=>$clinical_data,
          )
      );
  }
  public function actionGlaucoma(){
      $this->getPatientList();
      list($follow_patient_list, $common_ophthalmic_disorders, $current_user, $user_list, $custom_data,$clinical_data) = $this->getClinicalSpecialityData('Glaucoma');
      $this->render('/analytics/analytics_container',
          array(
              'specialty' => 'Glaucoma',
              'service_data' => $follow_patient_list,
              'custom_data' => $custom_data,
              'patient_list' => $this->patient_list,
              'user_list' => $user_list,
              'current_user' => $current_user,
              'common_disorders' => $common_ophthalmic_disorders,
              'clinical_data'=>$clinical_data,
          )
      );
  }

  private function getPatientList(){
      $paitent_list_command = Yii::app()->db->createCommand()->select('DISTINCT p.hos_num as hos_num,
      p.nhs_num as nhs_num,
      p.id AS id,
      CONCAT(c.first_name, \' \', c.last_name) AS name,
      p.dob as dob,
      IF(p.date_of_death IS NOT NULL,
         YEAR(p.date_of_death) - YEAR(p.dob) - IF( DATE_FORMAT(p.date_of_death,"%m-%d") < DATE_FORMAT(p.dob,\'%m-%d\'), 1, 0),
         YEAR(CURRENT_DATE())-YEAR(p.dob)-IF(DATE_FORMAT(CURRENT_DATE(),"%m-%d") < DATE_FORMAT(p.dob,\'%m-%d\'), 1, 0)) as age,
      p.gender as gender,
      patient_diagnoses.diagnoses AS diagnoses,
      patient_procedures.procedures AS procedures')
          ->from('patient p')->leftJoin('contact c','p.contact_id = c.id')
          ->leftJoin('(SELECT patient_id AS patient_id, GROUP_CONCAT(diagnoses) AS diagnoses
                  FROM (
                         SELECT ep.patient_id AS patient_id, GROUP_CONCAT(d.term) AS diagnoses
                         FROM episode ep
                                LEFT JOIN disorder d ON ep.disorder_id = d.id
                         WHERE d.term IS NOT NULL
                         GROUP BY ep.patient_id
                         UNION
                         SELECT sd.patient_id AS patient_id, GROUP_CONCAT(d.term) AS diagnoses
                         FROM secondary_diagnosis sd
                                LEFT JOIN disorder d ON sd.disorder_id = d.id
                         WHERE d.term IS NOT NULL
                         GROUP BY sd.patient_id
                       ) t2
                  GROUP BY t2.patient_id) patient_diagnoses','patient_diagnoses.patient_id = p.id')->leftJoin('(SELECT e2.patient_id, GROUP_CONCAT(p.short_format) AS procedures
                  FROM et_ophtroperationnote_procedurelist eop
                         LEFT JOIN ophtroperationnote_procedurelist_procedure_assignment oppa
                                   ON eop.id = oppa.procedurelist_id
                         LEFT JOIN proc p ON oppa.proc_id = p.id
                         LEFT JOIN event e ON eop.event_id = e.id
                         LEFT JOIN episode e2 ON e.episode_id = e2.id
                  WHERE p.short_format IS NOT NULL
                  GROUP BY e2.patient_id) patient_procedures','patient_procedures.patient_id = p.id');
      $this->patient_list = $paitent_list_command->queryAll();
  }


    /**
     * @param $data_list
     * @param $sum
     * @param $count
     * @return floatcustom_csv_data
     * This function is used to calculate the standard deviation of an array.
     * Used for Glaucuoma and MR subspecialties VA/IOP/CRT plots.
     * Normally we don't need the sum and number count passed as variables because they are can be get from the original array
     * But in this situation, we get the sum and count from the upper function, pass and use them directly will cause less calculations.
     */
  public function calculateStandardDeviation($data_list, $sum, $count){
      $variance = 0;
      $average = $sum/$count;
      foreach ($data_list as $value){
          $current_deviation = $value - $average;
          $variance += $current_deviation * $current_deviation;
      }
      $variance /= $count;

      $standard_deviation = sqrt($variance);
      return $standard_deviation;
  }

    /**
     * @param $subspecialty_id
     * @return array
     * Functions getCustomVA(), getCustomCRT(), getCustomIOP() is used to get VA, CRT, and IOP values for corresponding subspecialty
     * Return arraies for left and right eye side which can be easily transformed to plotly readable data format.
     * Note: the name getCustomXXX() because we thought they are for custom section, but seems not, names will be changed if needed.
     */
  public function getCustomVA($subspecialty_name,$surgeon = null) {
      $basic_criteria = null;
      $extra_command = null;
      if ($subspecialty_name == 'Medical Retina'){
          $basic_criteria = isset($this->filters['treatment'])? $this->filters['treatment']:null;
          $extra_command = Yii::app()->db->createCommand()
              ->from('et_ophtrintravitinjection_treatment eot')
              ->leftJoin('event e','e.id = eot.event_id')
              ->leftJoin('episode ep','ep.id = e.episode_id')
              ->leftJoin('patient p','p.id = ep.patient_id')
              ->where('ep.deleted = 0 and e.deleted=0 and p.deleted = 0');
          if (isset($surgeon)){
              $extra_command->andWhere('eot.created_user_id = '.$surgeon);
          }
      }elseif ($subspecialty_name == 'Glaucoma'){
          $basic_criteria = isset($this->filters['procedure'])? $this->filters['procedure']:null;
          $op_proc_command = Yii::app()->db->createCommand()
              ->select('p.id as patient_id, MIN(e.event_date) as event_date, IF(eop.eye_id = 1 or eop.eye_id = 3, opa.proc_id, null) as left_value, IF(eop.eye_id =2 or eop.eye_id = 3, opa.proc_id, null) as right_value, \'2event\' as t_name, eop.created_user_id as surgeon_id', 'DISTINCT')
              ->from('et_ophtroperationnote_procedurelist eop')
              ->leftJoin('ophtroperationnote_procedurelist_procedure_assignment opa','eop.id = opa.procedurelist_id')
              ->leftJoin('event e','eop.event_id = e.id')
              ->leftJoin('episode ep','e.episode_id = ep.id')
              ->leftJoin('patient p','p.id = ep.patient_id')
              ->where('ep.deleted = 0 and e.deleted=0 and p.deleted = 0')
              ->group('patient_id, left_value, right_value');
          $laser_proc_command = Yii::app()->db->createCommand()
              ->select('p.id as patient_id, MIN(e.event_date) as event_date, IF(eot.eye_id = 1 or eot.eye_id = 3, ola.procedure_id, null) as left_value, IF(eot.eye_id =2 or eot.eye_id = 3, ola.procedure_id, null) as right_value, \'2event\' as t_name, eot.created_user_id as surgeon_id', 'DISTINCT')
              ->from('et_ophtrlaser_treatment eot')
              ->leftJoin('ophtrlaser_laserprocedure_assignment ola','eot.id = ola.treatment_id')
              ->leftJoin('event e','eot.event_id = e.id')
              ->leftJoin('episode ep','e.episode_id = ep.id')
              ->leftJoin('patient p','p.id = ep.patient_id')
              ->where('ep.deleted = 0 and e.deleted=0 and p.deleted = 0')
              ->group('patient_id, left_value, right_value');
          $extra_command = Yii::app()->db->createCommand()
              ->from('('.$op_proc_command->union('('.$laser_proc_command->getText().')')->getText().') as et');
          if (isset($surgeon)){
              $extra_command->andWhere('et.surgeon_id = '.$surgeon);
          }
      }
      return $this->getCustomDataListQueryNew($subspecialty_name,'VA',$extra_command,$basic_criteria);
  }

  public function getCustomCRT($subspecialty_name,$surgeon=null) {
      $basic_criteria = isset($this->filters['treatment'])? $this->filters['treatment']:null;
      $extra_command = Yii::app()->db->createCommand()
          ->from('et_ophtrintravitinjection_treatment eot')
          ->leftJoin('event e','e.id = eot.event_id')
          ->leftJoin('episode ep','ep.id = e.episode_id')
          ->leftJoin('patient p','p.id = ep.patient_id')
          ->where('ep.deleted = 0 and e.deleted=0 and p.deleted = 0');
      if (isset($surgeon)){
          $extra_command->andWhere('eot.created_user_id = '.$surgeon);
      }
      return $this->getCustomDataListQueryNew($subspecialty_name,'CRT',$extra_command,$basic_criteria);
  }

  public function getCustomIOP($subspecialty_name,$surgeon=null){
      $basic_criteria = isset($this->filters['procedure'])? $this->filters['procedure']:null;
      $op_proc_command = Yii::app()->db->createCommand()
          ->select('p.id as patient_id, MIN(e.event_date) as event_date, IF(eop.eye_id = 1 or eop.eye_id = 3, opa.proc_id, null) as left_value, IF(eop.eye_id =2 or eop.eye_id = 3, opa.proc_id, null) as right_value, \'2event\' as t_name, eop.created_user_id as surgeon_id', 'DISTINCT')
          ->from('et_ophtroperationnote_procedurelist eop')
          ->leftJoin('ophtroperationnote_procedurelist_procedure_assignment opa','eop.id = opa.procedurelist_id')
          ->leftJoin('event e','eop.event_id = e.id')
          ->leftJoin('episode ep','e.episode_id = ep.id')
          ->leftJoin('patient p','p.id = ep.patient_id')
          ->where('ep.deleted = 0 and e.deleted=0 and p.deleted = 0')
          ->group('patient_id, left_value, right_value');
      $laser_proc_command = Yii::app()->db->createCommand()
          ->select('p.id as patient_id, MIN(e.event_date) as event_date, IF(eot.eye_id = 1 or eot.eye_id = 3, ola.procedure_id, null) as left_value, IF(eot.eye_id =2 or eot.eye_id = 3, ola.procedure_id, null) as right_value, \'2event\' as t_name, eot.created_user_id as surgeon_id', 'DISTINCT')
          ->from('et_ophtrlaser_treatment eot')
          ->leftJoin('ophtrlaser_laserprocedure_assignment ola','eot.id = ola.treatment_id')
          ->leftJoin('event e','eot.event_id = e.id')
          ->leftJoin('episode ep','e.episode_id = ep.id')
          ->leftJoin('patient p','p.id = ep.patient_id')
          ->where('ep.deleted = 0 and e.deleted=0 and p.deleted = 0')
          ->group('patient_id, left_value, right_value');
      $extra_command = Yii::app()->db->createCommand()
          ->from('('.$op_proc_command->union('('.$laser_proc_command->getText().')')->getText().') as et');
      if (isset($surgeon)){
          $extra_command->andWhere('et.surgeon_id ='.$surgeon);
      }
      return $this->getCustomDataListQueryNew($subspecialty_name,'IOP',$extra_command,$basic_criteria);
  }

    /**
     * @param $age
     * @param $protocol Todo: seems this parameter is never used, need specify.
     * @param $date
     * @return bool
     *
     */
  public function validateAgeAndDateFilters($age, $date){
      $return_value = true;
      if (isset($this->filters['age_min'])){
          $return_value = ((int)$age >= (int)$this->filters['age_min']);
      }
      if (isset($this->filters['age_max']) && $return_value){
          $return_value = ((int)$age <= (int)$this->filters['age_max']);
      }
      if (isset($this->filters['date_to']) && $return_value){
          $return_value = ($date < $this->filters['date_to']);
      }
      if (isset($this->filters['date_from']) && $return_value){
          $return_value = ($date > $this->filters['date_from']);
      }
      return $return_value;
  }

    /**
     * @param $subspecialty_id
     * @return mixed
     * Function queryVANew() and queryIOPNew() to get all data from visual acuity element and intraocular pressure element
     * Return the record in dataset, used by getCustomVA() and getCustomIOP() functions.
     */

    public function queryVANew($eye_side,$subspecialty,$extra_command,$basic_criteria=null){
        $extra_commands = clone $extra_command;
        $command_va_values = Yii::app()->db->createCommand()
            ->select('ep.patient_id as patient_id, e.event_date as event_date,  IF(eov.eye_id=3 OR eov.eye_id = 1, eov.id, null) AS left_value, IF(eov.eye_id=3 OR eov.eye_id = 2, eov.id, null) AS right_value, \'1value\' as t_name','DISTINCT')
            ->from('et_ophciexamination_visualacuity eov')
            ->leftJoin('event e','e.id = eov.event_id')
            ->leftJoin('episode ep','ep.id = e.episode_id')
            ->leftJoin('patient p','p.id = ep.patient_id')
            ->where('ep.deleted = 0 and e.deleted=0 and p.deleted = 0')
            ->group('eov.id');

        if (isset($basic_criteria)){
            if ($subspecialty == 'Medical Retina'){
                $extra_commands->select('ep.patient_id as patient_id, MIN(e.event_date) as event_date , IF(eot.left_drug_id '.$basic_criteria.',eot.left_drug_id, null)  as left_value, IF(eot.right_drug_id '.$basic_criteria.',eot.right_drug_id, null) as right_value, \'2event\' as t_name','DISTINCT')
                    ->andwhere('eot.'.$eye_side.'_drug_id '.$basic_criteria)
                    ->group('ep.patient_id, eot.left_drug_id, eot.right_drug_id');
            }elseif ($subspecialty == 'Glaucoma'){
                $extra_commands->select('et.patient_id as patient_id, MIN(et.event_date) as event_date , IF(et.left_value '.$basic_criteria.',et.left_value, null)  as left_value, IF(et.right_value '.$basic_criteria.',et.right_value, null) as right_value, \'2event\' as t_name','DISTINCT')
                    ->andwhere('et.'.$eye_side.'_value '.$basic_criteria)
                    ->group('patient_id, left_value,right_value');
            }
        }else{
            if ($subspecialty == 'Medical Retina'){
                $extra_commands->select('ep.patient_id as patient_id, MIN(e.event_date) as event_date , eot.left_drug_id as left_value, eot.right_drug_id as right_value, \'2event\' as t_name','DISTINCT')
                    ->andwhere('eot.left_drug_id IS NOT NULL or eot.right_drug_id IS NOT NULL')
                    ->group('ep.patient_id');;
            }elseif ($subspecialty == 'Glaucoma'){
                $extra_commands->select('et.patient_id as patient_id, MIN(et.event_date) as event_date , et.left_value  as left_value, et.right_value as right_value, \'2event\' as t_name','DISTINCT')
                    ->andwhere('et.left_value IS NOT NULL or et.right_value IS NOT NULL')
                    ->group('et.patient_id');
            }
        }
        $command_va_values_patients = clone $command_va_values;
        $command_va_patients = Yii::app()->db->createCommand()
            ->select('va_vals.patient_id','DISTINCT')
            ->from('('.$command_va_values_patients->getText().') AS va_vals');

        $extra_command_patient = Yii::app()->db->createCommand()
            ->select('tp.patient_id','distinct')
            ->from('('.$extra_commands->getText().') as tp');

        $command_filtered_patients = Yii::app()->db->createCommand()
            ->select('dp.patient_id','distinct')
            ->from('('.$this->queryDiagnosesFilteredPatientListCommand($eye_side)->getText().') AS dp')
            ->where('dp.patient_id in ('.$extra_command_patient->getText().')')
            ->andWhere('dp.patient_id in ('.$command_va_patients->getText().')');

        $command_final_table = Yii::app()->db->createCommand()
            ->select('t.patient_id as patient_id, t.event_date as event_date, t.'.$eye_side.'_value as value, t.t_name as name','DISTINCT')
            ->from('('.$command_va_values->union($extra_commands->getText())->getText().') as t')
            ->where('t.patient_id in ('.$command_filtered_patients->getText().')')
            ->andWhere('t.'.$eye_side.'_value IS NOT NULL')
            ->order('t.patient_id, t.event_date','ASC');

        return $command_final_table->queryAll();
    }

    public function queryIOPNew($eye_side,$extra_command,$basic_criteria=null){
        $extra_commands = clone $extra_command;
        $command_iop_values = Yii::app()->db->createCommand()
            ->select('ep.patient_id as patient_id, e.event_date as event_date,  IF(eov.eye_id=3 OR eov.eye_id = 1, eov.id, null) AS left_value, IF(eov.eye_id=3 OR eov.eye_id = 2, eov.id, null) AS right_value, \'1value\' as t_name','DISTINCT')
            ->from('et_ophciexamination_intraocularpressure eov')
            ->leftJoin('event e','e.id = eov.event_id')
            ->leftJoin('episode ep','ep.id = e.episode_id')
            ->leftJoin('patient p','p.id = ep.patient_id')
            ->where('ep.deleted = 0 and e.deleted=0 and p.deleted = 0')
            ->group('eov.id');
        if (isset($basic_criteria)){
            $extra_commands->select('et.patient_id as patient_id, MIN(et.event_date) as event_date , IF(et.left_value '.$basic_criteria.',et.left_value, null)  as left_value, IF(et.right_value '.$basic_criteria.',et.right_value, null) as right_value, \'2event\' as t_name','DISTINCT')
                ->andwhere('et.'.$eye_side.'_value '.$basic_criteria)
                ->group('patient_id, left_value,right_value');
        }else{
            $extra_commands->select('et.patient_id as patient_id, MIN(et.event_date) as event_date , et.left_value  as left_value, et.right_value as right_value, \'2event\' as t_name','DISTINCT')
                ->andwhere('et.left_value IS NOT NULL or et.right_value IS NOT NULL')
                ->group('et.patient_id');
        }
        $command_iop_values_patients = clone $command_iop_values;
        $command_iop_patients = Yii::app()->db->createCommand()
            ->select('iop_vals.patient_id','DISTINCT')
            ->from('('.$command_iop_values_patients->getText().') AS iop_vals');

        $extra_command_patient = Yii::app()->db->createCommand()
            ->select('tp.patient_id','distinct')
            ->from('('.$extra_commands->getText().') as tp');

        $command_filtered_patients = Yii::app()->db->createCommand()
            ->select('dp.patient_id','distinct')
            ->from('('.$this->queryDiagnosesFilteredPatientListCommand($eye_side)->getText().') AS dp')
            ->where('dp.patient_id in ('.$extra_command_patient->getText().')')
            ->andWhere('dp.patient_id in ('.$command_iop_patients->getText().')');
        $command_final_table = Yii::app()->db->createCommand()
            ->select('t.patient_id as patient_id, t.event_date as event_date, t.'.$eye_side.'_value as value, t.t_name as name','DISTINCT')
            ->from('('.$command_iop_values->union($extra_commands->getText())->getText().') as t')
            ->where('t.patient_id in ('.$command_filtered_patients->getText().')')
            ->andWhere('t.'.$eye_side.'_value IS NOT NULL')
            ->order('t.patient_id, t.event_date','ASC');

        return $command_final_table->queryAll();
    }

    public function queryCRTNew($eye_side,$extra_command,$basic_criteria=null){
        $extra_commands = clone $extra_command;
        $command_crt_values = Yii::app()->db->createCommand()
            ->select('ep.patient_id as patient_id, e.event_date as event_date,  IF(eov.eye_id=3 OR eov.eye_id = 1, eov.id, null) AS left_value, IF(eov.eye_id=3 OR eov.eye_id = 2, eov.id, null) AS right_value, \'1value\' as t_name','DISTINCT')
            ->from('et_ophciexamination_oct eov')
            ->leftJoin('event e','e.id = eov.event_id')
            ->leftJoin('episode ep','ep.id = e.episode_id')
            ->leftJoin('patient p','p.id = ep.patient_id')
            ->where('ep.deleted = 0 and e.deleted=0 and p.deleted = 0')
            ->group('eov.id');

        if (isset($basic_criteria)){
            $extra_commands->select('ep.patient_id as patient_id, MIN(e.event_date) as event_date , IF(eot.left_drug_id '.$basic_criteria.',eot.left_drug_id, null)  as left_value, IF(eot.right_drug_id '.$basic_criteria.',eot.right_drug_id, null) as right_value, \'2event\' as t_name','DISTINCT')
                ->andwhere('eot.'.$eye_side.'_drug_id '.$basic_criteria)
                ->group('ep.patient_id, eot.left_drug_id, eot.right_drug_id');
        }else{
            $extra_commands->select('ep.patient_id as patient_id, MIN(e.event_date) as event_date , eot.left_drug_id as left_value, eot.right_drug_id as right_value, \'2event\' as t_name','DISTINCT')
                ->andwhere('eot.left_drug_id IS NOT NULL or eot.right_drug_id IS NOT NULL')
                ->group('ep.patient_id');;
        }

        $command_crt_values_patients = clone $command_crt_values;
        $command_crt_patients = Yii::app()->db->createCommand()
            ->select('crt_vals.patient_id','DISTINCT')
            ->from('('.$command_crt_values_patients->getText().') AS crt_vals');

        $extra_command_patient = Yii::app()->db->createCommand()
            ->select('tp.patient_id','distinct')
            ->from('('.$extra_commands->getText().') as tp');

        $command_filtered_patients = Yii::app()->db->createCommand()
            ->select('dp.patient_id','distinct')
            ->from('('.$this->queryDiagnosesFilteredPatientListCommand($eye_side)->getText().') AS dp')
            ->where('dp.patient_id in ('.$extra_command_patient->getText().')')
            ->andWhere('dp.patient_id in ('.$command_crt_patients->getText().')');
        $command_final_table = Yii::app()->db->createCommand()
            ->select('t.patient_id as patient_id, t.event_date as event_date, t.'.$eye_side.'_value as value, t.t_name as name','DISTINCT')
            ->from('('.$command_crt_values->union($extra_commands->getText())->getText().') as t')
            ->where('t.patient_id in ('.$command_filtered_patients->getText().')')
            ->andWhere('t.'.$eye_side.'_value IS NOT NULL')
            ->order('t.patient_id, t.event_date','ASC');

        return $command_final_table->queryAll();
    }

    public function queryDiagnosesFilteredPatientListCommand($eye_side,$caller = 'custom'){
        if ($caller == 'followup'){
            $diagnoses = isset($this->filters['service_diagnosis'])? $this->filters['service_diagnosis']: null;
        }else{
            $diagnoses = isset($this->filters['custom_diagnosis'])? $this->filters['custom_diagnosis']: null;
        }
        $command_principal = Yii::app()->db->createCommand()
            ->select('e.patient_id as patient_id','DISTINCT')
            ->from('episode e')
            ->where('e.disorder_id IS NOT NULL')
            ->leftJoin('patient p','p.id = e.patient_id')
            ->andWhere('e.deleted = 0')
            ->andWhere('p.deleted = 0');


        $command_secondary = Yii::app()->db->createCommand()
            ->select('sd.patient_id as patient_id','DISTINCT')
            ->from('secondary_diagnosis sd')
            ->leftJoin('patient p','p.id = sd.patient_id')
            ->where('sd.disorder_id IS NOT NULL')
            ->andWhere('p.deleted = 0');

        if ($eye_side == 'left'){
            $command_principal->andWhere('e.eye_id IN (1,3)');
            $command_secondary->andWhere('sd.eye_id IN (1,3)');
        }elseif ($eye_side == 'right'){
            $command_principal->andWhere('e.eye_id IN (2,3)');
            $command_secondary->andWhere('sd.eye_id IN (2,3)');
        }

        if (isset($diagnoses)){
            if(is_array($diagnoses)){
                $command_principal->andWhere('e.disorder_id IN ('.implode(",",$diagnoses).')');
                $command_secondary->andWhere('sd.disorder_id IN ('.implode(",",$diagnoses).')');
            }else{
                $command_principal->andWhere('e.disorder_id IN ('.$diagnoses.')');
                $command_secondary->andWhere('sd.disorder_id IN ('.$diagnoses.')');
            }
        }
        return $command_secondary->union($command_principal->getText());
    }

    public function getCustomDataListQueryNew($subsepcialty,$type,$extra_command,$basic_criteria){
        $patient_list = array();
        $left_list = array();
        $right_list = array();
        foreach (['right','left'] as $side) {
            $treatment = array();
            $initial_reading = array();
            switch ($type){
                case 'VA':
                    $elements = $this->queryVANew($side,$subsepcialty,$extra_command,$basic_criteria);
                    break;
                case 'IOP':
                    $elements = $this->queryIOPNew($side,$extra_command,$basic_criteria);
                    break;
                case 'CRT':
                    $elements = $this->queryCRTNew($side,$extra_command,$basic_criteria);
                    break;
            }
            foreach ($elements as $element) {
                if (!isset($element['value'])){
                    continue;
                }

                if ($element['name'] == '1value'){
                    switch ($type){
                        case 'VA':
                            $reading = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->findByPk($element['value'])->getBestReading($side);
                            break;
                        case 'IOP':
                            $reading = \OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure::model()->findByPk($element['value'])->getReading($side);
                            break;
                        case 'CRT':
                            $reading = \OEModule\OphCiExamination\models\Element_OphCiExamination_OCT::model()->findByPk($element['value']);
                            break;
                    }

                    if (isset($reading)){
                        if ($type == 'VA'){
                            $reading = $reading->value;
                        }
                        if ($type == 'CRT'){
                            $reading = $reading->{$side.'_sft'};
                        }
                    }else{
                        continue;
                    }
                    $current_time = Helper::mysqlDate2JsTimestamp($element['event_date']);
                }else{
                    $treatment[$element['patient_id']] = $element['value'];
                    $reading = isset($initial_reading[$element['patient_id']]['value'])? $initial_reading[$element['patient_id']]['value']: null;
                    $current_time = isset($initial_reading[$element['patient_id']]['event_date'])? $initial_reading[$element['patient_id']]['event_date']:null;
                }


                if (!isset($treatment[$element['patient_id']])){
                    $initial_reading[$element['patient_id']]['value'] = $reading;
                    $initial_reading[$element['patient_id']]['event_date'] = $current_time;
                    continue;
                }

                $current_patient = Patient::model()->findByPk($element['patient_id']);

                /* Add patient in this->patient_list if not exist, prepare for drill down list,
                Get each patient's left and right eye readings as well as event time */
                if ($this->validateAgeAndDateFilters( $current_patient->getAge(), $current_time) && isset($reading) && isset($current_time)) {
                    if (!isset($initial_reading[$element['patient_id']]['value']) || !isset($initial_reading[$element['patient_id']]['event_date'])){
                        $initial_reading[$element['patient_id']]['value'] = $reading;
                        $initial_reading[$element['patient_id']]['event_date'] = $current_time;
                    }

                    if (!array_key_exists($current_patient->id, $patient_list)) {
                        $patient_list[$current_patient->id] = array();
                    }


                    if  (!array_key_exists($current_patient->id, $this->custom_csv_data) && (isset($reading))){
                        $this->custom_csv_data[$current_patient->id] = array(
                            'patient_id'=>$current_patient->id,
                            'left'=>array(
                                "VA"=>array(),
                                "CRT"=>array(),
                                "IOP"=>array(),
                            ),
                            'right'=>array(
                                "VA"=>array(),
                                "CRT"=>array(),
                                "IOP"=>array(),
                            ),
                        );
                    }
                    if (isset($reading)){
                        array_push($this->custom_csv_data[$current_patient->id][$side][$type],$reading);
                    }

                    $current_week = floor((($current_time/1000) - ($initial_reading[$element['patient_id']]['event_date']/1000)) / self::WEEKTIME);

                    if (array_key_exists((int)$current_week, ${$side.'_list'})){
                        ${$side.'_list'}[$current_week]['count']+=1;
                        ${$side.'_list'}[$current_week]['sum']+=$reading;
                        ${$side.'_list'}[$current_week]['square_sum']+= pow($reading,2);
                        ${$side.'_list'}[$current_week]['average'] = round( ${$side.'_list'}[$current_week]['sum']/ ${$side.'_list'}[$current_week]['count']);
                        ${$side.'_list'}[$current_week]['SD'] = $this->calculateStandardDeviationByDataSet(${$side.'_list'}[$current_week]);
                        array_push( ${$side.'_list'}[$current_week]['patients'],$current_patient->id);
                    } else {
                        ${$side.'_list'}[$current_week] = array(
                            'count'=> 1,
                            'sum' => $reading,
                            'square_sum'=> pow($reading,2),
                            'average'=>$reading,
                            'SD'=>0,
                            'patients' => array($current_patient->id),
                        );
                    }

                }
            }
        }

        ksort($left_list);
        ksort($right_list);

        return [$left_list,$right_list];
    }
    public function calculateStandardDeviationByDataSet($data_set){
        $square_average = pow($data_set['average'],2);
        $square_sum = $data_set['square_sum'];
        $sum = $data_set['sum'];
        $average = $data_set['average'];
        $count = $data_set['count'];

        $SD = sqrt((($square_sum-(2*$average*$sum))/$count) + $square_average);
        return number_format($SD, 2, '.', '');
    }
    /**
     * @return mixed
     * Get all the cataract elements in operation note event
     * Used for the drill down list.
     */
    public function queryCataractEventList(){
        $return_data = array();
        $command = Yii::app()->db->createCommand()
            ->select('eoc.event_id as event_id,
            e.event_date as event_date,
            eye.name as eye_side,
            CONCAT(c.title," ",c.first_name, " ", c.last_name) as patient_name,
            p.id as patient_id,
            proc.term as event_procedure')
            ->from('et_ophtroperationnote_cataract eoc')
            ->join('event e', 'e.id = eoc.event_id')
            ->join('episode ep', 'ep.id = e.episode_id')
            ->join('patient p','p.id = ep.patient_id')
            ->join('contact c', 'c.id = p.contact_id')
            ->join('et_ophtroperationnote_procedurelist eop','eop.event_id = eoc.event_id')
            ->leftJoin('ophtroperationnote_procedurelist_procedure_assignment oppa', 'oppa.procedurelist_id = eop.id')
            ->leftJoin('proc','proc.id = oppa.proc_id')
            ->leftJoin('eye', 'eye.id = eop.eye_id');
        $raw_data = $command->queryAll();
        foreach ($raw_data as $row){
            if (array_key_exists($row['event_id'],$return_data)){
                $return_data[$row['event_id']]['procedures'] .= ', '.$row['event_procedure'];
            }else{
                $return_data[$row['event_id']] = array(
                    'event_id' => $row['event_id'],
                    'event_date' => $row['event_date'],
                    'eye_side' => $row['eye_side'],
                    'patient_id' => $row['patient_id'],
                    'patient_name'=>$row['patient_name'],
                    'procedures'=>$row['event_procedure'],
                );
            }
        }
        return array_values($return_data);
    }

    public function insertIntoCustomCSV($current_patient,$right_reading,$left_reading,$type){
        if  (!array_key_exists($current_patient->id, $this->custom_csv_data) && (isset($right_reading) || isset($left_reading))){
            $this->custom_csv_data[$current_patient->id] = array(
                'first_name'=>$current_patient->getFirst_name(),
                'second_name'=>$current_patient->getLast_name(),
                'hos_num'=>$current_patient->hos_num,
                'dob'=>$current_patient->dob,
                'age'=>$current_patient->getAge(),
                'diagnoses'=>$current_patient->getDiagnosesTermsArray(),
                'left'=>array(
                    "VA"=>array(),
                    "CRT"=>array(),
                    "IOP"=>array(),
                ),
                'right'=>array(
                    "VA"=>array(),
                    "CRT"=>array(),
                    "IOP"=>array(),
                ),
            );
        }
        if (isset($right_reading)){
            array_push($this->custom_csv_data[$current_patient->id]['right'][$type],$right_reading);
        }
        if (isset($left_reading)){
            array_push($this->custom_csv_data[$current_patient->id]['left'][$type],$left_reading);
        }
    }
    

  public function getCommonDisorders($subspecialty_id = null, $only_name = false){
      $criteria = new CDbCriteria();
      if (isset($criteria) && isset($subspecialty_id)){
          $criteria->compare('subspecialty_id', $subspecialty_id);
      }
      $common_ophthalmic_disorders= CommonOphthalmicDisorder::model()->findAll($criteria);
      $common_ophthalmic_disorders_only_name = array();
      if ($only_name){
           foreach ($common_ophthalmic_disorders as $disorder){
               if(isset($disorder->disorder->id)) {
                   $common_ophthalmic_disorders_only_name[$disorder->disorder->id] = $disorder->disorder->term;
               }
           }
           $common_ophthalmic_disorders = $common_ophthalmic_disorders_only_name;
      }
      return $common_ophthalmic_disorders;
  }

  public function getPatientsListByDiagnosisSurgeon($surgeon_id = null, $subspecialty = null){
        $command = Yii::app()->db->createCommand()
            ->select('e2.patient_id as patient_id')
            ->from('et_ophciexamination_diagnoses eod')
            ->leftJoin('event e','e.id = eod.event_id')
            ->leftJoin('episode e2','e2.id = e.episode_id')
            ->leftJoin('firm','firm.id = e2.firm_id')
            ->leftJoin('service_subspecialty_assignment ssa','firm.service_subspecialty_assignment_id = ssa.id')
            ->group('e2.patient_id');
        if (isset($subspecialty)){
            $command->andWhere('ssa.subspecialty_id = :subspecialty_id', array(':subspecialty_id'=>$subspecialty));
        }
        if (isset($surgeon_id)){
            $command->andWhere('eod.created_user_id = :surgeon_id', array(':surgeon_id'=>$surgeon_id));
        }
        $query_list= $command->queryAll();
        $patients_list = array();
        foreach ($query_list as $patient){
            $patients_list[] = $patient['patient_id'];
        }
        return $patients_list;
    }

    public function queryDiagnosis($subspecialty_id = null, $surgeon_id =null, $start_date= null, $end_date=null){
        $command_principal = Yii::app()->db->createCommand()
            ->select('e.patient_id as patient_id, e.disorder_id as disorder_id','DISTINCT')
            ->from('episode e')
            ->leftJoin('firm f','e.firm_id = f.id')
            ->leftJoin('service_subspecialty_assignment ssa','ssa.id = f.service_subspecialty_assignment_id')
            ->where('e.disorder_id IS NOT NULL')
            ->andWhere('e.deleted = 0');

        $command_secondary = Yii::app()->db->createCommand()
            ->select('sd.patient_id as patient_id, sd.disorder_id as disorder_id','DISTINCT')
            ->from('secondary_diagnosis sd')
            ->leftJoin('episode e','e.patient_id = sd.patient_id')
            ->leftJoin('firm f','e.firm_id = f.id')
            ->leftJoin('service_subspecialty_assignment ssa','ssa.id = f.service_subspecialty_assignment_id')
            ->where('sd.disorder_id IS NOT NULL');
        if (isset($subspecialty_id)){
            $command_principal->andWhere('ssa.subspecialty_id = :subspecialty_id', array(':subspecialty_id'=>$subspecialty_id));
            $command_secondary->andWhere('ssa.subspecialty_id = :subspecialty_id', array(':subspecialty_id'=>$subspecialty_id));
        }
        if (isset($surgeon_id)){
            $command_principal->andWhere('e.created_user_id = :surgeon_id', array(':surgeon_id'=>$surgeon_id));
            $command_secondary->andWhere('sd.created_user_id = :surgeon_id', array(':surgeon_id'=>$surgeon_id));
        }
        if (isset($start_date) && $start_date !== 0){
            $command_principal->andWhere('e.created_date > TIMESTAMP("'.$start_date.'")');
            $command_secondary->andWhere('sd.created_date > TIMESTAMP("'.$start_date.'")');
        }
        if (isset($end_date)){
            $command_principal->andWhere('e.created_date < TIMESTAMP("'.$end_date.'")');
            $command_secondary->andWhere('sd.created_date < TIMESTAMP("'.$end_date.'")');
        }
        $principal_diagnoses = $command_principal->queryAll();
        $secondary_diagnoses = $command_secondary->queryAll();
        return array_merge_recursive($principal_diagnoses,$secondary_diagnoses);
    }

    public function getPatientWithoutDisorders(){
        $command_disorder_patient = Yii::app()->db->createCommand()
            ->select('patient.id', 'DISTINCT')
            ->from('patient')
            ->leftJoin('episode', 'patient.id = episode.patient_id')
            ->where('episode.disorder_id IS NOT NULL');
        $patient_with_disorder = $command_disorder_patient->queryAll();

        $command_secondary_disorder_patient = Yii::app()->db->createCommand()
            ->select('patient_id', 'DISTINCT')
            ->from('secondary_diagnosis')
            ->where(array('not in', 'patient_id', array_column($patient_with_disorder, 'id')));
        $patient_with_secondary_disorder = $command_secondary_disorder_patient->queryAll();


        $command_no_disorder_patient = Yii::app()->db->createCommand()
            ->select('id', 'DISTINCT')
            ->from('patient')
            ->where(array('not in', 'id', array_merge_recursive(array_column($patient_with_secondary_disorder, 'patient_id'), array_column($patient_with_disorder, 'id'))));

        $patient_without_disorder = $command_no_disorder_patient->queryAll();
        return $patient_without_disorder;
    }

  public function getDisorders($subspecialty_id=null, $surgeon_id = null, $start_date = null, $end_date = null){
      $disorder_list = array(
          'x'=> array(),
          'y'=>array(),
          'text' => array(),
          'customdata' => array(),
      );
      $other_drill_down_list = array(
          'x'=> array(),
          'y'=>array(),
          'text' => array(),
          'customdata' => array(),
      );
      $patient_without_disorder = array_column($this->getPatientWithoutDisorders(), 'id');
      $disorder_patient_list = array();
      $other_patient_list = array();
      $other_disorder_list = array();
      $disorder_list_csv = array();
      //get common ophthalmic disorders for given subspecialty
      $common_ophthalmic_disorders = $this->getCommonDisorders($subspecialty_id);
      foreach ($common_ophthalmic_disorders as $disorder){
          if(isset($disorder->disorder->id)){
              if (!array_key_exists($disorder->disorder->id, $disorder_patient_list)){
                  $disorder_patient_list[$disorder->disorder->id]= array(
                      'full_name' => $disorder->disorder->fully_specified_name,
                      'short_name' => $disorder->disorder->term,
                      'patient_list' => array(),
                  );
              }
          }
      }
      // this is secondary_common_ophthalmic_disorders regarding to the latest comment by toby
//      $secondary_common_ophthalmic_disorders = SecondaryToCommonOphthalmicDisorder::model()->findAll();
//      foreach ($secondary_common_ophthalmic_disorders as $disorder){
//          if(isset($disorder->disorder->id)){
//              if ($disorder->parent->subspecialty_id !== $subspecialty_id)
//                  continue;
//              if (!array_key_exists($disorder->disorder->id, $disorder_patient_list)){
//                  $disorder_patient_list[$disorder->disorder->id]= array(
//                      'full_name' => $disorder->disorder->fully_specified_name,
//                      'short_name' => $disorder->disorder->term,
//                      'patient_list' => array(),
//                  );
//              }
//          }
//      }
      $diagnoses = $this->queryDiagnosis($subspecialty_id,$surgeon_id,$start_date,$end_date);
      foreach ($diagnoses as $current_diagnosis){
          $current_patient = Patient::model()->findByPk($current_diagnosis['patient_id']);

          $disorder_id = $current_diagnosis['disorder_id'];
          $diagnosis_item = Disorder::model()->findByPk($disorder_id);
          $disorder_list_csv[$diagnosis_item->term.$current_patient->id] = array($current_patient->getFirst_name(),$current_patient->getLast_name(),$current_patient->hos_num,$current_patient->dob,$current_patient->getAge(),$diagnosis_item->term);
          if (array_key_exists($disorder_id, $disorder_patient_list)){
              if(!in_array($current_patient->id, $disorder_patient_list[$disorder_id]['patient_list'])){
                  array_push($disorder_patient_list[$disorder_id]['patient_list'], $current_patient->id);
              }
          } else {
              if (!array_key_exists($disorder_id, $other_disorder_list)){
                  $other_disorder_list[$disorder_id]= array(
                      'full_name' => $diagnosis_item->fully_specified_name,
                      'short_name' => $diagnosis_item->term,
                      'patient_list' => array(),
                  );
              }
              if (!in_array($current_patient->id, $other_disorder_list[$disorder_id]['patient_list'])){
                  array_push($other_disorder_list[$disorder_id]['patient_list'], $current_patient->id);
              }
              if(!in_array($current_patient->id, $other_patient_list)){
                  array_push($other_patient_list, $current_patient->id);
              }
          }
      }

      $i=0;
      foreach ($disorder_patient_list as $key=>$value) {
          if (count($disorder_patient_list[$key]['patient_list'])) {
              $disorder_list['y'][] = $i;
              $disorder_list['x'][] = count($disorder_patient_list[$key]['patient_list']);
              $disorder_list['text'][] = $disorder_patient_list[$key]['short_name'];
              $disorder_list['customdata'][] = $disorder_patient_list[$key]['patient_list'];
              $i++;
          }
      }

      $j=0;
      foreach ($other_disorder_list as $key=>$value){
          $other_drill_down_list['y'][] = $j;
          $other_drill_down_list['x'][] = count($other_disorder_list[$key]['patient_list']);
          $other_drill_down_list['text'][] = $other_disorder_list[$key]['short_name'];
          $other_drill_down_list['customdata'][] = $other_disorder_list[$key]['patient_list'];
          $j++;
      }

      if(count($other_patient_list)){
          $disorder_list['y'][] = $i;
          $disorder_list['x'][] = count($other_patient_list);
          $disorder_list['text'][] = 'Other';
          $disorder_list['customdata'][] = $other_drill_down_list;
          $i++;
      }

      if (count($patient_without_disorder)){
          $disorder_list['y'][] = $i;
          $disorder_list['x'][] = count($patient_without_disorder);
          $disorder_list['text'][] = 'No Diagnoses';
          $disorder_list['customdata'][] = $patient_without_disorder;

      }
      $disorder_list_csv = array_values($disorder_list_csv);
      $disorder_list['csv_data'] = $disorder_list_csv;
      return $disorder_list;
  }

    /**
     * Get all filters from sidebar, used together with actionUpdateData()
     */
  public function obtainFilters(){
      $specialty = Yii::app()->request->getParam('specialty');
      $dateFrom = Yii::app()->request->getParam('from');
      $dateTo = Yii::app()->request->getParam('to');
      $ageMin = Yii::app()->request->getParam('custom_age_min');
      $ageMax = Yii::app()->request->getParam('custom_age_max');
      $custom_diagnosis = Yii::app()->request->getParam('custom_diagnosis');
      $service_diagnosis = Yii::app()->request->getParam('service_diagnosis');
      $custom_treatment = Yii::app()->request->getParam('custom_treatment');
      $clinical_surgeon_id = Yii::app()->request->getParam('clinical_surgeon');
      $custom_surgeon_id = Yii::app()->request->getParam('custom_surgeon');
      $custom_procedure = Yii::app()->request->getParam('custom_procedure');
      $plot_va_change = Yii::app()->request->getParam('custom_plot');

      if (isset($custom_diagnosis)){
          $diagnoses_MR = $this->getIdByName(array("age-related macular degeneration","Branch retinal vein occlusion","Central retinal vein occlusion","Diabetic macular oedema"),Disorder::class,'term');
          $diagnoses_GL = $this->getIdByName(array('glaucoma', 'open-angle glaucoma', 'angle-closure glaucoma', 'Low tension glaucoma', 'Ocular hypertension'),Disorder::class,'term');
          $custom_diagnosis_array = explode(",",$custom_diagnosis);
          $custom_diagnosis = array();
          foreach ($custom_diagnosis_array as $item){
              if ($specialty === "Medical Retina"){
                      $custom_diagnosis = array_merge_recursive($custom_diagnosis,$diagnoses_MR[$item]);
              }else if ($specialty === "Glaucoma"){
                      $custom_diagnosis = array_merge_recursive($custom_diagnosis,$diagnoses_GL[$item]);
              }else{
                  $custom_diagnosis = null;
                  break;
              }
          }
      }
      if (isset($custom_treatment)){
          $treatments = $this->getIdByName(array('Lucentis', 'Eylea', 'Avastin', 'Triamcinolone', 'Ozurdex'),OphTrIntravitrealinjection_Treatment_Drug::class,'name');
          $custom_treatment = $treatments[$custom_treatment];
          $custom_treatment = 'IN ('.implode(",",$custom_treatment).')';
      }
      if (isset($custom_procedure)){
          $procedures = $this->getIdByName(array('cataract extraction','Trabeculectomy', 'aqueous shunt','Cypass Stent Insertion','Selective laser trabeculoplasty','Laser coagulation ciliary body'),Procedure::class,'term');
          $custom_procedure = $procedures[$custom_procedure];
          $custom_procedure = 'IN ('.implode(",",$custom_procedure).')';
      }
      if (isset($plot_va_change)){
          $plot_va_change = true;
      }else{
          $plot_va_change = false;
      }

      if ($dateTo){
          $dateTo = Helper::mysqlDate2JsTimestamp($dateTo);
      }else{
          $dateTo = Helper::mysqlDate2JsTimestamp(date("Y-m-d h:i:s"));
      }
      if ($dateFrom){
          $dateFrom = Helper::mysqlDate2JsTimestamp($dateFrom);
      }else{
          $dateFrom = 0;
      }

      $this->filters = array(
          'specialty'=>$specialty,
          'date_from' => $dateFrom,
          'date_to' => $dateTo,
          'age_min'=>$ageMin,
          'age_max'=>$ageMax,
          'custom_diagnosis'=>$custom_diagnosis,
          'protocol'=>null,
          'plot-va'=>null,
          'treatment'=>$custom_treatment,
          'service_diagnosis'=>$service_diagnosis,
          'clinical_surgeon_id'=>$clinical_surgeon_id,
          'custom_surgeon_id'=>$custom_surgeon_id,
          'procedure'=>$custom_procedure,
          'plot_va_change'=> ($plot_va_change)? $plot_va_change:null,
      );
  }
    public function getIdByName($name_array, $model, $name_attribute){
        $return_array = array();
        foreach ($name_array as $name){
            $items = $model::model()->findAll($name_attribute.' LIKE \'%'.$name.'%\'');
            if(isset($items)){
                $item_array = array();
                foreach ($items as $item){
                    $item_array[] = $item->id;
                }
                $return_array[] = $item_array;
            }else{
                $return_array[] = null;
            }
        }
        return $return_array;
    }
    /**
     * After "Update Chart" button is created, this function is called to get updated data based on current filters.
     */
  public function actionUpdateData(){
      $this->checkAuth();
      $this->obtainFilters(); // get current filters. Question: why not call validateFilters() in this function.

      $specialty = $this->filters['specialty'];

      if (!isset($this->surgeon)&&isset($surgeon_id)){
          $this->surgeon = $surgeon_id;
      }
      $this->custom_csv_data = array();
      if ($specialty == 'All'){
          $subspecialty_id = null;
          $custom_data = array();
      }else{
          $subspecialty_id = $this->getSubspecialtyID($specialty);
          list($left_va_list, $right_va_list) = $this->getCustomVA($specialty,$this->filters['custom_surgeon_id']);
          if ($specialty === "Glaucoma"){
              list($left_second_list,$right_second_list) = $this->getCustomIOP($specialty,$this->filters['custom_surgeon_id']);
          }elseif ($specialty === "Medical Retina"){
              list($left_second_list,$right_second_list) = $this->getCustomCRT($specialty,$this->filters['custom_surgeon_id']);
          }

          foreach (['left','right'] as $side){
              if (isset($this->filters['plot_va_change']) && $this->filters['plot_va_change']){
                  $this->filters['plot_va_change_initial_va_value'] = (empty(${$side.'_va_list'}))? null: ${$side.'_va_list'}[0]['average'];
              }
              $custom_data[] = array(
                  array(
                      'x' => array_keys(${$side.'_va_list'}),
                      'y' => array_map(
                          function ($item){
                              if (isset($this->filters['plot_va_change_initial_va_value'])){
                                  $item['average'] -= $this->filters['plot_va_change_initial_va_value'];
                              }
                              return $item['average'];
                          }, array_values(${$side.'_va_list'})),
                      'customdata'=>array_map(
                          function($item){
                              return $item['patients'];
                          },
                          array_values(${$side.'_va_list'})),
                      'error_y'=> array(
                          'type'=> 'data',
                          'array' => array_map(
                              function($item){
                                  return $item['SD'];
                              },
                              array_values(${$side.'_va_list'})),
                          'visible' => true,
                          'color' => '#aaa',
                          'thickness' => 1
                      )
                  ),
                  array(
                      'yaxis' => 'y2',
                      'x' => array_keys(${$side.'_second_list'}),
                      'y' => array_map(
                          function ($item){
                              return $item['average'];
                          }, array_values(${$side.'_second_list'})),
                      'customdata'=>array_map(
                          function($item){
                              return $item['patients'];
                          },
                          array_values(${$side.'_second_list'})),
                      'error_y' => array(
                          'type' => 'data',
                          'array' => array_map(
                              function($item){
                                  return $item['SD'];
                              },
                              array_values(${$side.'_second_list'})),
                          'visible' => true,
                          'color' => '#aaa',
                          'thickness' => 1
                      )
                  )
              );
          }
          $custom_data['csv_data']=$this->custom_csv_data;
      }
      $disorder_data = $this->getDisorders($subspecialty_id,$this->filters['clinical_surgeon_id'],$this->filters['date_from'],$this->filters['date_to']);
      $clinical_data = array(
          'x' => $disorder_data['x'],
          'y' => $disorder_data['y'],
          'text' => $disorder_data['text'],
          'customdata' =>$disorder_data['customdata'],
          'csv_data' => $disorder_data['csv_data'],
      );
      $service_data = $this->getFollowUps($subspecialty_id,$this->filters['date_from']/1000,$this->filters['date_to']/1000, $this->filters['service_diagnosis']);


      $this->renderJSON(array($clinical_data, $service_data, $custom_data));
  }

    /**
     * @param $period_name
     * @return int
     * This function is used to transfer different period type from follow up into number of days
     * Called in getFollowUps() function
     */
  public function getPeriodDate($period_name){
      switch ($period_name) {
          case 'days':
              $period = self::PERIOD_DAY;
              break;
          case 'weeks':
              $period = self::PERIOD_WEEK;
              break;
          case 'months':
              $period = self::PERIOD_MONTH;
              break;
          case 'years':
              $period = self::PERIOD_YEAR;
              break;
          default:
              $period = 0;
              break;
      }
      return $period;
  }

    /**
     * @param $subspecialty_id
     * @param null $start_date
     * @param null $end_date
     * @return array
     * This function is used to get three followup data (overdue, coming, waiting) for "Service" section
     * Todo: split this function into small chunk of code to make it more readable
     *
     */
    public function getLatestEventByType($event_type_name,$subspecialty_id)
    {
        $event_type_command = '';
        $command = Yii::app()->db->createCommand()
            ->from('event e')
            ->leftJoin('episode e2','e.episode_id = e2.id')
            ->leftJoin('patient p','p.id = e2.patient_id')
            ->leftJoin('event_type e3','e3.id = e.event_type_id')
            ->leftJoin('firm','e2.firm_id = firm.id')
            ->leftJoin('service_subspecialty_assignment ssa', 'ssa.id = firm.service_subspecialty_assignment_id')
            ->where('p.deleted <> 1 and e.deleted <> 1 and e2.deleted <>1');
        if (isset($event_type_name)){
            $command->andWhere('lower(e3.name) like lower(\'%'.$event_type_name.'%\')');
            $event_type_command = ' and lower(e3.name) like lower(\'%'.$event_type_name.'%\')';
        }
        $command->andWhere('e.event_date = (
                select MAX(e4.event_date) from event e4
                left join episode e5 ON e4.episode_id = e5.id
                left join patient p2 ON e5.patient_id = p2.id
                left join event_type e6 ON e6.id = e4.event_type_id
                WHERE p2.id = p.id and e4.deleted = 0 and e5.deleted = 0 and p2.deleted = 0'.$event_type_command.')');
        return $command;
    }
    public function getFollowUps($subspecialty_id, $start_date = null, $end_date = null, $diagnosis=null){
        $followup_patient_list = array(
            'overdue' => array(),
            'coming' => array(),
            'waiting' => array(),
        );
        $followup_csv_data = array(
            'overdue' => array(),
            'coming' => array(),
            'waiting' => array(),
        );
        $followup_elements_command =  $this->getLatestEventByType('examination',$subspecialty_id);
        $followup_elements_command->select('e.id as event_id, p.id as patient_id, e.event_date as event_date, DATE_ADD(event_date, INTERVAL IF(period.name = \'weeks\', 7 ,IF( period.name = \'months\', 30, IF(period.name = \'years\', 365, 1)))*eoc.followup_quantity DAY) as due_date, CAST(DATEDIFF(DATE_ADD(event_date, INTERVAL IF(period.name = \'weeks\', 7 ,IF( period.name = \'months\', 30, IF(period.name = \'years\', 365, 1)))*eoc.followup_quantity DAY),current_date())/7 AS INT) as weeks')
            ->leftJoin('et_ophciexamination_clinicoutcome eoc','eoc.event_id = e.id')
            ->leftJoin('period','period.id = eoc.followup_period_id')
            ->andWhere('eoc.id IS NOT NULL and eoc.followup_period_id IS NOT NULL');
        $referral_document_command = $this->getLatestEventByType('document',$subspecialty_id);
        $referral_document_command->select('e.id as event_id, p.id as patient_id, e.event_date as event_date')
            ->leftJoin('et_ophcodocument_document eod','e.id = eod.event_id')
            ->leftJoin('ophcodocument_sub_types ost','eod.event_sub_type = ost.id')
            ->andWhere('ost.name = "Referral Letter"');
        if ($diagnosis){
            $command_filtered_patients_by_diagnosis = Yii::app()->db->createCommand()
                ->select('dp.patient_id','distinct')
                ->from('('.$this->queryDiagnosesFilteredPatientListCommand(null,'followup')->getText().') AS dp');
            $referral_document_command->andWhere('p.id IN ('.$command_filtered_patients_by_diagnosis->getText().')');
            $followup_elements_command->andWhere('p.id IN ('.$command_filtered_patients_by_diagnosis->getText().')');
        }
        $followup_elements = $followup_elements_command->queryAll();
        $current_time = time();
        foreach ($followup_elements as $followup_item){
            /* Calculate the coming and overdue followups */
            $event_time = Helper::mysqlDate2JsTimestamp($followup_item['event_date'])/1000;
            if( ($start_date && $event_time < $start_date) ||
                ($end_date && $event_time > $end_date))
                continue;
            $current_patient = Patient::model()->findByPk($followup_item['patient_id']);
            $latest_worklist_time = $this->checkPatientWorklist($current_patient->id)/1000;
            $latest_time = isset($latest_worklist_time)? max($event_time, $latest_worklist_time):$event_time;
            $due_time = Helper::mysqlDate2JsTimestamp($followup_item['due_date'])/1000;
            if( $followup_item['weeks'] <= 0){
                if ($latest_time > $event_time)
                    continue;
                //Follow up is overdue
                $over_weeks = -$followup_item['weeks'];
                if ($over_weeks <= self::FOLLOWUP_WEEK_LIMITED){
                    array_push($followup_csv_data['overdue'],
                        array(
                            'patient_id'=>$current_patient->id,
                            'weeks'=>$over_weeks,
                        ));
                    if(!array_key_exists($over_weeks, $followup_patient_list['overdue'])){
                        $followup_patient_list['overdue'][$over_weeks] = array($current_patient->id);
                    } else {
                        array_push($followup_patient_list['overdue'][$over_weeks], $current_patient->id);
                    }
                }
            } else {
                if ($latest_worklist_time >$current_time && $latest_worklist_time < $due_time)
                    continue;
                $coming_weeks = $followup_item['weeks'];
                if ($coming_weeks <= self::FOLLOWUP_WEEK_LIMITED){
                    array_push($followup_csv_data['coming'],
                        array(
                            'patient_id'=>$current_patient->id,
                            'weeks'=>$coming_weeks,
                        ));
                    if(!array_key_exists($coming_weeks, $followup_patient_list['coming'])){
                        $followup_patient_list['coming'][$coming_weeks] = array($current_patient->id);
                    } else {
                        array_push($followup_patient_list['coming'][$coming_weeks], $current_patient->id);
                    }
                }
            }
        }
        $patient_tickets = \OEModule\PatientTicketing\models\Ticket::model()->findAll();
        $patientticket_api = new \OEModule\PatientTicketing\components\PatientTicketing_API();
        foreach ($patient_tickets as $ticket) {
            $ticket_followup = $patientticket_api->getFollowUp($ticket->id);
            $current_event = $ticket->event;
            $assignment_time = Helper::mysqlDate2JsTimestamp($ticket_followup['assignment_date']) / 1000;
            if( ($start_date && $assignment_time < $start_date) ||
                ($end_date && $assignment_time > $end_date))
                continue;
            if (isset($this->surgeon) && isset($current_event)){
                if ($this->surgeon !== $current_event->created_user_id){
                    continue;
                }
            }
            $current_patient = $ticket->patient;
            if ($diagnosis){
                $current_patient_diagnoses = $this->queryAllDiagnosisForPatient($current_patient->id);
                if (!in_array($diagnosis,$current_patient_diagnoses)){
                    continue;
                }
            }
            $latest_worklist_time = $this->checkPatientWorklist($current_patient->id)/1000;
            $latest_examination = $current_patient->getLatestExaminationEvent();
            if(isset($latest_examination)) {
                $latest_examination_date = Helper::mysqlDate2JsTimestamp($latest_examination->event_date) / 1000;
            } else {
                $latest_examination_date = null;
            }

            $latest_time = null;

            if(isset($latest_worklist_time)) {
                if(isset($latest_examination_date)) {
                    $latest_time = max($latest_examination_date, $latest_worklist_time);
                } else {
                    $latest_time = $latest_worklist_time;
                }
            } else {
                if(isset($latest_examination_date)) {
                    $latest_time = $latest_examination_date;
                }
            }

            $quantity = $ticket_followup['followup_quantity'];
            if ($quantity > 0) {
                $period_date = $quantity * $this->getPeriodDate($ticket_followup['followup_period']);
                $due_time = $assignment_time + $period_date * self::DAYTIME_ONE;
                if ($due_time < $current_time) {
                    if (!isset($latest_time) || $latest_time > $assignment_time)
                        continue;
                    //Follow up is overdue
                    $over_weeks = intval(($current_time - $due_time) / self::DAYTIME_ONE / self::PERIOD_WEEK);
                    if ($over_weeks <= self::FOLLOWUP_WEEK_LIMITED){
                        array_push($followup_csv_data['overdue'],
                            array(
                                'patient_id'=>$current_patient->id,
                                'weeks'=>$over_weeks,
                            )
                        );
                        if (!array_key_exists($over_weeks, $followup_patient_list['overdue'])) {
                            $followup_patient_list['overdue'][$over_weeks] = array($current_patient->id);
                        } else {
                            array_push($followup_patient_list['overdue'][$over_weeks], $current_patient->id);
                        }
                    }
                } else {
                    if ($latest_worklist_time >$current_time && $latest_worklist_time < $due_time)
                        continue;
                    $coming_weeks = intval(($due_time - $current_time) / self::DAYTIME_ONE / self::PERIOD_WEEK);
                    if ($coming_weeks <= self::FOLLOWUP_WEEK_LIMITED) {
                        array_push($followup_csv_data['coming'],
                            array(
                                'patient_id'=>$current_patient->id,
                                'weeks'=>$coming_weeks,
                            ));
                        if (!array_key_exists($coming_weeks, $followup_patient_list['coming'])) {
                            $followup_patient_list['coming'][$coming_weeks] = array($current_patient->id);
                        } else {
                            array_push($followup_patient_list['coming'][$coming_weeks], $current_patient->id);
                        }
                    }
                }
            }
        }
        /* Get the waiting follow up data, uses Document event with referral letter type and worklist time
        To calculate how long a patient will wait frm the date of referral to the date assigned in a worklist*/
        $referral_document_elements = $referral_document_command->queryAll();
        foreach ($referral_document_elements as $referral_element){
            $current_patient = Patient::model()->findByPk($referral_element['patient_id']);
            $current_referral_date = Helper::mysqlDate2JsTimestamp($referral_element['event_date']) / 1000;
            if( ($start_date && $current_referral_date < $start_date) ||
                ($end_date && $current_referral_date > $end_date))
                continue;
            $current_patient_on_worklist = WorklistPatient::model()->findByAttributes(array('patient_id'=>$current_patient->id), array('order'=>'t.when ASC'));
            if (isset($current_patient_on_worklist) && !empty($current_patient_on_worklist)){
                $appointment_time = Helper::mysqlDate2JsTimestamp($current_patient_on_worklist->when)/1000;
                if($appointment_time >= $current_referral_date){
                    $waiting_time = ceil((($appointment_time - $current_referral_date)/(self::WEEKTIME)));
                }
            }
            else {
                $current_time = time();
                if($current_time > $current_referral_date) {
                    $waiting_time = ceil((($current_time - $current_referral_date)/(self::WEEKTIME)));
                }
            }
            if (isset($waiting_time) && $waiting_time <= self::FOLLOWUP_WEEK_LIMITED) {
                array_push($followup_csv_data['waiting'],
                    array(
                        'patient_id'=>$current_patient->id,
                        'weeks'=>$waiting_time,
                    ));
                if (! isset($followup_patient_list['waiting'][$waiting_time])){
                    $followup_patient_list['waiting'][$waiting_time]= array();
                }
                array_push($followup_patient_list['waiting'][$waiting_time],$current_patient->id);
            }
        }
        ksort($followup_patient_list['waiting']);
        ksort($followup_patient_list['overdue']);
        ksort($followup_patient_list['coming']);
        return array(
            'plot_data'=>$followup_patient_list,
            'csv_data'=>$followup_csv_data,
        );
    }

  protected function queryAllDiagnosisForPatient($patient_id){
      $command = Yii::app()->db->createCommand()
          ->select('od.disorder_id AS disorder_id')
          ->from('episode e')
          ->leftJoin('event e2','e2.episode_id = e.id')
          ->leftJoin('et_ophciexamination_diagnoses eod','eod.event_id = e2.id')
          ->leftJoin('ophciexamination_diagnosis od','eod.id = od.element_diagnoses_id')
          ->where('od.id IS NOT NULL')
          ->andWhere('e.patient_id =:patient_id', array(':patient_id'=>$patient_id))
          ->group('od.id');
      $diagnoses = $command->queryAll();
      $return_data = array();
      foreach ($diagnoses as $diagnosis){
          if (!in_array($diagnosis['disorder_id'],$return_data)){
              $return_data[] = $diagnosis['disorder_id'];
          }
      }
      return $return_data;
  }
    /**
     * @param $events
     * @return mixed
     * This function is writen for the use of php usort() function
     */
  protected function sortEventByEventDate($events){
      for($i=0;$i<count($events);$i++){
          $val = $events[$i];
          $j = $i-1;
          while($j>=0 && $events[$j]->event_date > $val->event_date){
              $events[$j+1] = $events[$j];
              $j--;
          }
          $events[$j+1] = $val;
      }
      return $events;
  }

  protected function checkPatientWorklist($patient_id) {
      $latest_date = null;
      $PatientWorklists = WorklistPatient::model()->findAllByAttributes(array('patient_id' => $patient_id));
      foreach ($PatientWorklists as $item){
          if ($latest_date < Helper::mysqlDate2JsTimestamp($item->worklist->start)){
              $latest_date = Helper::mysqlDate2JsTimestamp($item->worklist->start);
          }
      }
      return $latest_date;
  }

    /**
     * Check user roles, user with "Service Manager" role can view all the data for all surgeons.
     * Otherwise, it can only view the data created by himself.
     */
    protected function checkAuth()
    {
        if (Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)) {
            $this->surgeon = null;
        } else {
            $this->surgeon = Yii::app()->user->id;
        }
    }

    /**
     * @return array
     */
    protected function getClinicalSpecialityData($speciality_name)
    {
        $this->checkAuth();
        $subspecialty_id = $this->getSubspecialtyID($speciality_name);
        $this->filters = array(
            'date_from' => 0,
            'date_to' => Helper::mysqlDate2JsTimestamp(date("Y-m-d h:i:s")),
        );
        $this->custom_csv_data = array();
        $follow_patient_list = $this->getFollowUps($subspecialty_id);
        $common_ophthalmic_disorders = $this->getCommonDisorders($subspecialty_id, true);
        $disorder_data = $this->getDisorders($subspecialty_id);

        $clinical_data = array(
            'title' => 'Disorders Section',
            'x' => $disorder_data['x'],
            'y' => $disorder_data['y'],
            'text' => $disorder_data['text'],
            'customdata' =>$disorder_data['customdata'],
            'csv_data'=>$disorder_data['csv_data'],
        );

        if($speciality_name === 'Glaucoma'){
            list($left_iop_list, $right_iop_list) = $this->getCustomIOP($speciality_name,$this->surgeon);
            list($left_va_list, $right_va_list) = $this->getCustomVA($speciality_name,$this->surgeon);
            $second_list_name = '_iop_list';
        } else {
            list($left_va_list, $right_va_list) = $this->getCustomVA($speciality_name,$this->surgeon);
            list($left_crt_list, $right_crt_list) = $this->getCustomCRT($speciality_name,$this->surgeon);
            $second_list_name = '_crt_list';
        }



        $current_user = User::model()->findByPk(Yii::app()->user->id);
        if (isset($this->surgeon)) {
            $user_list = null;
        } else {
            $user_list = User::model()->findAll();
        }
        $custom_data = array();
        foreach (['left', 'right'] as $side) {
            $custom_data[] = array(
                array(
                    'name' => 'VA',
                    'x' => array_keys(${$side . '_va_list'}),
                    'y' => array_map(
                        function ($item) {
                            return $item['average'];
                        }, array_values(${$side . '_va_list'})),
                    'customdata' => array_map(
                        function ($item) {
                            return $item['patients'];
                        },
                        array_values(${$side . '_va_list'})),
                    'error_y' => array(
                        'type' => 'data',
                        'array' => array_map(
                            function ($item) {
                                return $item['SD'];
                            },
                            array_values(${$side . '_va_list'})),
                        'visible' => true,
                        'color' => '#aaa',
                        'thickness' => 1
                    ),
                    'hoverinfo' => 'text',
                    'hovertext' => array_map(
                        function ($item) {
                            return "Average: " . $item['average'] . "<br> SD: " . $item['SD'] ."<br> Patient No: ".count($item['patients']);
                        },
                        array_values(${$side . '_va_list'})
                    ),
                ),
                array(
                    'name' => $speciality_name === 'Glaucoma' ? 'IOP' : 'CRT',
                    'yaxis' => 'y2',
                    'x' => array_keys(${$side . $second_list_name}) ,
                    'y' => array_map(
                        function ($item) {
                            return $item['average'];
                        }, array_values(${$side . $second_list_name})),
                    'customdata' => array_map(
                        function ($item) {
                            return $item['patients'];
                        },
                        array_values(${$side . $second_list_name})),
                    'error_y' => array(
                        'type' => 'data',
                        'array' => array_map(
                            function ($item) {
                                return $item['SD'];
                            },
                            array_values(${$side . $second_list_name})),
                        'visible' => true,
                        'color' => '#aaa',
                        'thickness' => 1
                    ),
                    'hoverinfo' => 'text',
                    'hovertext' => array_map(
                        function ($item) {
                            return "Average: " . $item['average'] . "<br> SD: " . $item['SD'] ."<br> Patient No: ".count($item['patients']);
                        },
                        array_values(${$side . $second_list_name})
                    ),
                )
            );
        }
        $custom_data['csv_data'] = $this->custom_csv_data;
        return array($follow_patient_list, $common_ophthalmic_disorders, $current_user, $user_list, $custom_data,$clinical_data);
    }


}