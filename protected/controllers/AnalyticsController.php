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
        'actions' => array('cataract', 'medicalRetina', 'glaucoma', 'updateData','allSubspecialties'),
        'users'=> array('@')
      ),
    );
  }

    /**
     * Function actionCataract(), actionMedicalRetina(), actionGlaucoma() are the main function for those three subspecialties
     * The function grab data for all the plots.
     */
    public function actionAllSubspecialties(){
        $this->checkAuth();
        $subspecialty_id = null;
        $this->current_user = User::model()->findByPk(Yii::app()->user->id);
        $disorder_data = $this->getDisorders($subspecialty_id);
        $this->filters = array(
            'date_from' => 0,
            'date_to' => Helper::mysqlDate2JsTimestamp(date("Y-m-d h:i:s")),
        );

        $follow_patient_list = $this->getFollowUps($subspecialty_id,$this->surgeon);
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
              'user_list' => $user_list,
              'current_user'=>$current_user,
          )
      );
  }
  public function actionMedicalRetina(){
      list($follow_patient_list, $common_ophthalmic_disorders, $current_user, $user_list, $custom_data) = $this->getClinicalSpecialityData('Medical Retina');
      $this->render('/analytics/analytics_container',
          array(
              'specialty' => 'Medical Retina',
              'service_data' => $follow_patient_list,
              'custom_data' => $custom_data,
              'patient_list' => $this->patient_list,
              'user_list' => $user_list,
              'current_user' => $current_user,
              'common_disorders' => $common_ophthalmic_disorders,
          )
      );
  }
  public function actionGlaucoma(){
      list($follow_patient_list, $common_ophthalmic_disorders, $current_user, $user_list, $custom_data) = $this->getClinicalSpecialityData('Glaucoma');
      $this->render('/analytics/analytics_container',
          array(
              'specialty' => 'Glaucoma',
              'service_data' => $follow_patient_list,
              'custom_data' => $custom_data,
              'patient_list' => $this->patient_list,
              'user_list' => $user_list,
              'current_user' => $current_user,
              'common_disorders' => $common_ophthalmic_disorders,
          )
      );
  }

  public function sortByTime($a, $b){
      if($a['event_time']==$b['event_time'])
          return 0;
      return ($a['event_time']<$b['event_time'])? -1: 1;
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
  public function getCustomVA($subspecialty_id,$surgeon = null) {
      //As the dataset is huge and to speed up the performance, sql queries are used to get all data.
      $va_elements = $this->queryVA($subspecialty_id);
      return $this->getCustomDataListQuery($va_elements,"VA",$surgeon);
  }

  public function getCustomCRT($subspecialty_id,$surgeon=null) {
      $crt_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_OCT::model()->findAll();
      return $this->getCustomDataList($crt_elements,'CRT', $subspecialty_id, $surgeon);
  }

  public function getCustomIOP($subspecialty_id,$surgeon=null){
      $iop_elements = $this->queryIOP($subspecialty_id);
      return $this->getCustomDataListQuery($iop_elements,"IOP",$surgeon);
  }

    /**
     * @param $age
     * @param $protocol Todo: seems this parameter is never used, need specify.
     * @param $date
     * @return bool
     *
     */
  public function validateFilters($age, $protocol, $date){
      $return_value = true;
      if (isset($this->filters['age_min'])){
          $return_value = ($age >= (int)$this->filters['age_min']);
      }
      if (isset($this->filters['age_max']) && $return_value){
          $return_value = ($age <= (int)$this->filters['age_max']);
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
     * Function queryVA() and queryIOP() to get all data from visual acuity element and intraocular pressure element
     * Return the record in dataset, used by getCustomVA() and getCustomIOP() functions.
     */
    public function queryVA($subspecialty_id){
        $command =  Yii::app()->db->createCommand()
            ->select('e2.patient_id as patient_id, e.created_user_id as created_user_id,
          eov.id as va_id, 
          d.term as term, 
          AVG(ovr.value) as reading, 
          e.event_date as date, 
          od.eye_id as eye_id, 
          ovr.side as side, 
          IF(ovr.side=1, AVG(ovr.value), null) AS left_reading, 
          IF(ovr.side=0, AVG(ovr.value), null) AS right_reading')
            ->from('et_ophciexamination_visualacuity eov')
            ->join('ophciexamination_visualacuity_reading ovr','eov.id = ovr.element_id')
            ->join('event e','eov.event_id = e.id')
            ->join('episode e2','e.episode_id = e2.id')
            ->leftJoin('et_ophciexamination_diagnoses eod','e.id = eod.event_id')
            ->leftJoin('ophciexamination_diagnosis od','od.element_diagnoses_id = eod.id')
            ->leftJoin('disorder d','od.disorder_id = d.id')
            ->leftJoin('firm','e2.firm_id = firm.id')
            ->leftJoin('service_subspecialty_assignment ssa', 'ssa.id = firm.service_subspecialty_assignment_id')
            ->where('ssa.subspecialty_id=:subspecialty_id', array(':subspecialty_id'=>$subspecialty_id))
            ->group('va_id, side');

        if(isset($this->filters['diagnosis'])){
            foreach ($this->filters['diagnosis'] as $diagnosis){
                $command->andWhere(array('like','term',$diagnosis));
            }
        }

        return $command->queryAll();
    }
    public function queryIOP($subspecialty_id){
        $command =  Yii::app()->db->createCommand()
            ->select('e2.patient_id as patient_id, e.created_user_id as created_user_id,
            eiop.id as iop_id, d.term as term, AVG(oir.value) as reading, e.event_date as date, od.eye_id as eye_id, IF(oiv.eye_id = 1, 1, 0) as side, IF(oiv.eye_id=1, AVG(oir.value), null) AS left_reading, IF(oiv.eye_id=2, AVG(oir.value), null) AS right_reading')
            ->from('et_ophciexamination_intraocularpressure eiop')
            ->join('ophciexamination_intraocularpressure_value oiv','oiv.element_id = eiop.id')
            ->join('event e','eiop.event_id = e.id')
            ->join('episode e2','e.episode_id = e2.id')
            ->join('ophciexamination_intraocularpressure_reading oir','oiv.reading_id = oir.id')
            ->leftJoin('et_ophciexamination_diagnoses eod','e.id = eod.event_id')
            ->leftJoin('ophciexamination_diagnosis od','od.element_diagnoses_id = eod.id')
            ->leftJoin('disorder d','od.disorder_id = d.id')
            ->leftJoin('firm','e2.firm_id = firm.id')
            ->leftJoin('service_subspecialty_assignment ssa', 'ssa.id = firm.service_subspecialty_assignment_id')
            ->where('ssa.subspecialty_id=:subspecialty_id', array(':subspecialty_id'=>$subspecialty_id))
            ->group('iop_id, side');

        if(isset($this->filters['diagnosis'])){
            foreach ($this->filters['diagnosis'] as $diagnosis){
                $command->andWhere(array('like','term',$diagnosis));
            }
        }

        return $command->queryAll();
    }

    /**
     * @return mixed
     * Get all the cataract elements in operation note event
     * Used for the drill down list.
     */
    public function queryCataractEventList(){
        $return_data = array();
        $command = Yii::app()->db->createCommand()
            ->select('eoc.event_id as event_id, CONCAT(c.title," ",c.first_name, " ", c.last_name) as patient_name, proc.term as event_procedure')
            ->from('et_ophtroperationnote_cataract eoc')
            ->join('event e', 'e.id = eoc.event_id')
            ->join('episode ep', 'ep.id = e.episode_id')
            ->join('patient p','p.id = ep.patient_id')
            ->join('contact c', 'c.id = p.contact_id')
            ->join('et_ophtroperationnote_procedurelist eop','eop.event_id = eoc.event_id')
            ->leftJoin('ophtroperationnote_procedurelist_procedure_assignment oppa', 'oppa.procedurelist_id = eop.id')
            ->leftJoin('proc','proc.id = oppa.proc_id');
        $raw_data = $command->queryAll();
        foreach ($raw_data as $row){
            if (array_key_exists($row['event_id'],$return_data)){
                $return_data[$row['event_id']]['procedures'] .= ', '.$row['event_procedure'];
            }else{
                $return_data[$row['event_id']] = array(
                    'event_id' => $row['event_id'],
                    'patient_name'=>$row['patient_name'],
                    'procedures'=>$row['event_procedure'],
                );
            }
        }
        return array_values($return_data);
    }

    public function getPatientsListBySurgeon($surgeon_id = null){
        $command = Yii::app()->db->createCommand()
            ->select('e2.patient_id as patient_id')
            ->from('et_ophtroperationnote_surgeon surgeon')
            ->join('event e','e.id = surgeon.event_id')
            ->join('episode e2','e2.id = e.episode_id');
        if (isset($surgeon_id)){
            $command->where('surgeon.surgeon_id = :surgeon_id', array(':surgeon_id'=>$surgeon_id));
        }
        $query_list= $command->queryAll();
        $patients_list = array();
        foreach ($query_list as $patient){
            $patients_list[] = $patient['patient_id'];
            if (!array_key_exists($patient['patient_id'], $this->patient_list)){
                $this->patient_list[$patient['patient_id']] = Patient::model()->findByPk($patient['patient_id']);
            }
            
        }
        return $patients_list;
  }

  public function getCustomDataListQuery($elements,$type,$surgeon_id=null,$readings = null){
        $patient_list = array();
        $left_list = array();
        $right_list = array();

        foreach ($elements as $element){
            if ($type === "CRT"){
                $left_reading =  $element->left_crt;
                $right_reading = $element->right_crt;
            } else{
                $left_reading = $element['left_reading'];
                $right_reading = $element['right_reading'];
            }

            /*Only use data which is created by the selected surgeon*/
            if (isset($surgeon_id)){
                if ($surgeon_id !== $element['created_user_id']){
                    continue;
                }
            }
            $current_protocol = null;
                $current_patient = Patient::model()->findByPk($element['patient_id']);
                $current_time = Helper::mysqlDate2JsTimestamp($element['date']);

                /* Add patient in this->patient_list if not exist, prepare for drill down list,
                Get each patient's left and right eye readings as well as event time */
                if ($this->validateFilters( $current_patient->getAge(), $current_protocol, $current_time)) {
                    if (!array_key_exists($current_patient->id, $this->patient_list)) {
                        $this->patient_list[$current_patient->id] = $current_patient;
                    }

                    if (!array_key_exists($current_patient->id, $patient_list)) {
                        $patient_list[$current_patient->id] = array();
                    }
                    if  (!array_key_exists($current_patient->id, $this->custom_csv_data)){
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
                    // eye 1 left, 2 right, 3 both
                    if (isset($this->filters['diagnosis'])){
                        if ($element['eye_id'] == 1){
                            $right_reading = null;
                        }elseif ($element['eye_id'] == 2){
                            $left_reading = null;
                        }
                    }
                    if (isset($right_reading)){
                        array_push($this->custom_csv_data[$current_patient->id]['right'][$type],$right_reading);
                    }
                    if (isset($left_reading)){
                        array_push($this->custom_csv_data[$current_patient->id]['left'][$type],$left_reading);
                    }
                    array_push($patient_list[$current_patient->id], array(
                            'left_reading' => $left_reading,
                            'right_reading' => $right_reading,
                            'event_time' => $current_time,
                            'weeks' => 0
                        )
                    );
                }
        }

        /* Sort all the data of each patient by time
        Then calculate each record's weeks from the start time
        Re-structure the data by using weeks as key
        Calculate the record count and sum at the same time for later use. */
        foreach ($patient_list as $patient_id => &$patient_data){
            usort($patient_data, array($this, 'sortByTime'));
            $start_time = $patient_data[0]['event_time']/1000;
            foreach ($patient_data as &$data_item){
                $current_week = floor(($data_item['event_time']/1000 - $start_time) / self::WEEKTIME);
                $data_item['weeks'] = $current_week;
                foreach (['left', 'right'] as $side){
                    if($data_item[$side.'_reading']){
                        if (array_key_exists((int)$current_week, ${$side.'_list'})){
                            ${$side.'_list'}[$current_week]['count']+=1;
                            ${$side.'_list'}[$current_week]['sum']+=$data_item[$side.'_reading'];
                            ${$side.'_list'}[$current_week]['values'][] =$data_item[$side.'_reading'];

                            ${$side.'_list'}[$current_week]['patients'][] = $patient_id;
                        } else {
                            ${$side.'_list'}[$current_week] = array(
                                'count'=> 1,
                                'sum' => $data_item[$side.'_reading'],
                                'values'=> array($data_item[$side.'_reading']),
                                'patients' => array($patient_id),
                            );
                        }
                    }
                }
            }
        }

        /* Use data above to calculate average and SD value */
        foreach (['left', 'right'] as $side){
            foreach (${$side.'_list'} as &$data_item){
                if ($data_item['count']>1){
                    $data_item['average'] = round($data_item['sum']/$data_item['count']);
                    $data_item['SD'] = $this->calculateStandardDeviation($data_item['values'],$data_item['sum'],$data_item['count']);
                }else{
                    $data_item['average'] = $data_item['sum'];
                    $data_item['SD'] = 0;
                }

            }
        }

        ksort($left_list);
        ksort($right_list);
        return [$left_list,$right_list];
    }

  public function getCustomDataList($elements,$type,$subspecialty_id,$surgeon_id=null,$readings = null){
      $patient_list = array();
      $left_list = array();
      $right_list = array();
      foreach ($elements as $element){
          if ($type === "CRT"){
           $left_reading =  $element->left_crt;
           $right_reading = $element->right_crt;
          }else if ($type === "VA"){
               $left_reading = $readings[$element->id][0];
               $right_reading = $readings[$element->id][1];
          }else{
              $left_reading = $element->getReading('left');
              $right_reading = $element->getReading('right');
          }
          $current_event = $element->event;
          if(isset($current_event->episode)) {
              if (isset($surgeon_id)){
                  if ($surgeon_id !== $current_event->created_user_id){
                      continue;
                  }
              }
              $current_episode = $current_event->episode;
              $current_subspecialty_id = $current_episode-> getSubspecialtyID();
              if ($current_subspecialty_id == $subspecialty_id){
                  $current_patient = $current_episode->patient;
                  $current_time = Helper::mysqlDate2JsTimestamp($current_event->event_date);
                  $current_diagnoses = \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::model()->findByAttributes(array('event_id'=>$current_event->id));
                  // eye 1 left, 2 right, 3 both
                  $current_diagnoses_left = array();
                  $current_diagnoses_right = array();
                  if (!empty($current_diagnoses)){
                      $current_diagnoses = $current_diagnoses->diagnoses;
                      foreach ($current_diagnoses as $diagnosis){
                          if ($diagnosis->eye_id == 2 || $diagnosis->eye_id ==3){
                              $current_diagnoses_right[] = $diagnosis->disorder->term;
                          }
                          if ($diagnosis->eye_id == 1 || $diagnosis->eye_id ==3){
                              $current_diagnoses_left[] = $diagnosis->disorder->term;
                          }
                      }
                  }
                  $current_treatment_left="";
                  $current_treatment_right="";
                  $current_treatment=Element_OphTrIntravitrealinjection_Treatment::model()->findByAttributes(array('event_id'=>$current_event->id));
                  $current_protocol = "";
                  $validation = $this->validateFilters( $current_patient->getAge(), $current_protocol, $current_time);
                  if ($this->validateFilters($current_patient->getAge(), $current_protocol, $current_time)) {
                      if (!array_key_exists($current_patient->id, $this->patient_list)) {
                          $this->patient_list[$current_patient->id] = $current_patient;
                      }

                      if (!array_key_exists($current_patient->id, $patient_list)) {
                          $patient_list[$current_patient->id] = array();
                      }
                      if  (!array_key_exists($current_patient->id, $this->custom_csv_data)){
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

                      if (isset($this->filters['diagnosis'])){
                          if (!empty($current_diagnoses_left)){
                              $i = 1;
                              foreach ($current_diagnoses_left as $diagnosis){
                                  if (in_array($diagnosis, $this->filters['diagnosis'])){
                                      break;
                                  }
                                  if ($i == count($current_diagnoses_left)){
                                      $left_reading = false;
                                  }
                                  $i += 1;
                              }
                          }else{
                              $left_reading = false;
                          }


                          if (!empty($current_diagnoses_right)){
                              $i = 1;
                              foreach ($current_diagnoses_right as $diagnosis){
                                  if (in_array($diagnosis, $this->filters['diagnosis'])){
                                      break;
                                  }
                                  if ($i == count($current_diagnoses_right)){
                                      $right_reading = false;
                                  }
                                  $i += 1;
                              }
                          }else{
                              $right_reading = false;
                          }
                      }
                      if ($right_reading){
                          array_push($this->custom_csv_data[$current_patient->id]['right'][$type],$right_reading);
                      }
                      if ($left_reading){
                          array_push($this->custom_csv_data[$current_patient->id]['left'][$type],$left_reading);
                      }

                      array_push($patient_list[$current_patient->id], array(
                              'left_reading' => $left_reading,
                              'right_reading' => $right_reading,
                              'event_time' => $current_time,
                              'weeks' => 0
                          )
                      );
                  }
              }
          }

      }
      foreach ($patient_list as $patient_id => &$patient_data){
          usort($patient_data, array($this, 'sortByTime'));
          $start_time = $patient_data[0]['event_time']/1000;
          foreach ($patient_data as &$data_item){
              $current_week = floor(($data_item['event_time']/1000 - $start_time) / self::WEEKTIME);
              $data_item['weeks'] = $current_week;
              foreach (['left', 'right'] as $side){
                  if($data_item[$side.'_reading']){
                      if (array_key_exists((int)$current_week, ${$side.'_list'})){
                          ${$side.'_list'}[$current_week]['count']+=1;
                          ${$side.'_list'}[$current_week]['sum']+=$data_item[$side.'_reading'];
                          ${$side.'_list'}[$current_week]['values'][] =$data_item[$side.'_reading'];
                          ${$side.'_list'}[$current_week]['patients'][] = $patient_id;
                      } else {
                          ${$side.'_list'}[$current_week] = array(
                              'count'=> 1,
                              'sum' => $data_item[$side.'_reading'],
                              'values'=> array($data_item[$side.'_reading']),
                              'patients' => array($patient_id),
                          );
                      }
                  }
              }

          }
      }

      foreach (['left', 'right'] as $side){
          foreach (${$side.'_list'} as &$data_item){
              if ($data_item['count']>1){
                  $data_item['average'] = round($data_item['sum']/$data_item['count']);
                  $data_item['SD'] = $this->calculateStandardDeviation($data_item['values'],$data_item['sum'],$data_item['count']);
              }else{
                  $data_item['average'] = $data_item['sum'];
                  $data_item['SD'] = 0;
              }

          }
      }

      ksort($left_list);
      ksort($right_list);
      return [$left_list,$right_list];
  }

  public function getCommonDisorders($subspecialty_id, $only_name = false){
      $criteria = new CDbCriteria();
      if (isset($criteria)){
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

  public function getDisorders($subspecialty_id, $surgeon_id = null, $start_date = null, $end_date = null){
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

      //get all the diagnoses and the patient list
      $diagnoses_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::model()->findAll();
      $all_patients = $this->getPatientsListBySurgeon($this->surgeon);
      foreach ($diagnoses_elements as $diagnosis_element_item){
          if (isset($surgeon_id)){
              if ($surgeon_id !== $diagnosis_element_item->created_user_id){
                  continue;
              }
          }
          $current_event = $diagnosis_element_item->event;
          if(isset($current_event->episode)) {
              $current_time = Helper::mysqlDate2JsTimestamp($current_event->event_date);
              if( ($start_date && $current_time < $start_date) ||
                  ($end_date && $current_time > $end_date))
                  continue;

              $current_episode = $current_event->episode;
              $current_patient = $current_episode->patient;
              if (!array_key_exists($current_patient->id, $this->patient_list)){
                  $this->patient_list[$current_patient->id] = $current_patient;
              }
              if (in_array($current_patient->id, $all_patients)){
                    $all_patients = array_diff($all_patients,array(($current_patient->id)));
              }
              $diagnoses = $diagnosis_element_item->diagnoses;
              foreach($diagnoses as $diagnosis_item){

                  $disorder_id = $diagnosis_item->disorder->id;
                  array_push($disorder_list_csv,array($current_patient->getFirst_name(),$current_patient->getLast_name(),$current_patient->hos_num,$current_patient->dob,$current_patient->getAge(),$diagnosis_item->disorder->term));

                  if (array_key_exists($disorder_id, $disorder_patient_list)){
                      if(!in_array($current_patient->id, $disorder_patient_list[$disorder_id]['patient_list'])){
                          array_push($disorder_patient_list[$disorder_id]['patient_list'], $current_patient->id);
                      }
                  } else {
                      if (!array_key_exists($disorder_id, $other_disorder_list)){
                          $other_disorder_list[$disorder_id]= array(
                              'full_name' => $diagnosis_item->disorder->fully_specified_name,
                              'short_name' => $diagnosis_item->disorder->term,
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
          }
      }

      $i=0;
      foreach ($disorder_patient_list as $key=>$value){
          if (count($disorder_patient_list[$key]['patient_list']) || $disorder_patient_list[$key]['short_name'] == "No abnormality detected"){
              $disorder_list['y'][] = $i;
              if ($disorder_patient_list[$key]['short_name'] == "No abnormality detected"){
                  $disorder_patient_list[$key]['patient_list'] = array_merge_recursive( $disorder_patient_list[$key]['patient_list'],$all_patients);
              }
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

      $disorder_list['y'][] = $i;
      $disorder_list['x'][] = count($other_patient_list);
      $disorder_list['text'][] = 'Other';
      $disorder_list['customdata'][] = $other_drill_down_list;
      $disorder_list['csv_data'] = $disorder_list_csv;
      return $disorder_list;
  }

    /**
     * Get all filters from sidebar, used together with actionUpdateData()
     */
  public function obtainFilters(){
      $diagnoses_MR = array("Age related macular degeneration","Branch retinal vein occlusion with macular oedema","Central retinal vein occlusion with macular oedema","Diabetic macular oedema");
      $specialty = Yii::app()->request->getParam('specialty');
      $dateFrom = Yii::app()->request->getParam('from');
      $dateTo = Yii::app()->request->getParam('to');
      $ageMin = Yii::app()->request->getParam('age-min');
      $ageMax = Yii::app()->request->getParam('age-max');
      $diagnosis = Yii::app()->request->getParam('diagnosis');
      $service_diagnosis = Yii::app()->request->getParam('serviceDiagnosis');

      if (isset($diagnosis)){
          if ($specialty === "Medical Retina"){
              $diagnosis = array($diagnoses_MR[$diagnosis]);
          }else{
              $diagnosis = null;
          }
      }
      $protocol = Yii::app()->request->getParam('protocol');
      $plotVA = Yii::app()->request->getParam('plot-VA');
      $treatment = Yii::app()->request->getParam('treatment');

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
          'date_from' => $dateFrom,
          'date_to' => $dateTo,
          'age_min'=>$ageMin,
          'age_max'=>$ageMax,
          'diagnosis'=>$diagnosis,
          'protocol'=>$protocol,
          'plot-va'=>$plotVA,
          'treatment'=>$treatment,
          'service_diagnosis'=>$service_diagnosis,
      );
  }

    /**
     * After "Update Chart" button is created, this function is called to get updated data based on current filters.
     */
  public function actionUpdateData(){
      $this->checkAuth();
      $specialty = Yii::app()->request->getParam('specialty');
      $clinical_surgeon_id = Yii::app()->request->getParam('clinical_surgeon_id');
      $service_surgeon_id = Yii::app()->request->getParam('service_surgeon_id');

      if (!isset($this->surgeon)&&isset($surgeon_id)){
          $this->surgeon = $surgeon_id;
      }
      $this->custom_csv_data = array();
      $this->obtainFilters(); // get current filters. Question: why not call validateFilters() in this function.

      if ($specialty == 'All'){
          $subspecialty_id = null;
          $disorder_data = $this->getDisorders($subspecialty_id,$clinical_surgeon_id,$this->filters['date_from'],$this->filters['date_to']);
          $clinical_data = array(
              'x' => $disorder_data['x'],
              'y' => $disorder_data['y'],
              'text' => $disorder_data['text'],
              'customdata' =>$disorder_data['customdata'],
              'csv_data' => $disorder_data['csv_data'],
          );
      }else{
          $subspecialty_id = $this->getSubspecialtyID($specialty);
          list($left_va_list, $right_va_list) = $this->getCustomVA($subspecialty_id,$clinical_surgeon_id);
          if ($specialty === "Glaucoma"){
              list($left_second_list,$right_second_list) = $this->getCustomIOP($subspecialty_id,$clinical_surgeon_id);
          }elseif ($specialty === "Medical Retina"){
              list($left_second_list,$right_second_list) = $this->getCustomCRT($subspecialty_id,$clinical_surgeon_id);
          }
          $clinical_data = array();
          foreach (['left','right'] as $side){
              $clinical_data[] = array(
                  array(
                      'x' => array_keys(${$side.'_va_list'}),
                      'y' => array_map(
                          function ($item){
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
          $clinical_data['csv_data']=$this->custom_csv_data;
      }
      $service_data = $this->getFollowUps($subspecialty_id, $service_surgeon_id,$this->filters['date_from']/1000,$this->filters['date_to']/1000, $this->filters['service_diagnosis']);


      $this->renderJSON(array($clinical_data, $service_data));
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
  public function getFollowUps($subspecialty_id,$surgeon = null, $start_date = null, $end_date = null, $diagnosis=null){

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

      $followup_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome::model()->findAll();

      $current_time = time();
      foreach ($followup_elements as $followup_item){
          $current_event = $followup_item->event;
          if (isset($current_event->episode)){

              if (isset($surgeon)){
                  if ($surgeon !== $current_event->created_user_id){
                      continue;
                  }
              }

              /* Calculate the coming and overdue followups */
              $event_time = Helper::mysqlDate2JsTimestamp($current_event->event_date)/1000;
              if( ($start_date && $event_time < $start_date) ||
                  ($end_date && $event_time > $end_date))
                  continue;
              $current_episode = $current_event->episode;
              $current_subspecialty = $current_episode->getSubspecialtyID();
              if ($current_subspecialty == $subspecialty_id || !isset($subspecialty_id)){
                  $current_patient = $current_episode->patient;
                  if ($diagnosis){
                      $current_patient_diagnoses = $this->queryAllDiagnosisForPatient($current_patient->id);
                      if (!in_array($diagnosis,$current_patient_diagnoses)){
                          continue;
                      }
                  }
                  $latest_worklist_time = $this->checkPatientWorklist($current_patient->id)/1000;
                  $latest_examination = Helper::mysqlDate2JsTimestamp($current_patient->getLatestExaminationEvent()->event_date)/1000;
                  $latest_time = isset($latest_worklist_time)? max($latest_examination, $latest_worklist_time):$latest_examination;

                  if (!array_key_exists($current_patient->id, $this->patient_list)){
                      $this->patient_list[$current_patient->id] = $current_patient;
                  }

                  $quantity = $followup_item->followup_quantity;
                  if($quantity > 0) {
                      $period_date = $quantity * $this->getPeriodDate($followup_item->followup_period->name);
                      $due_time = $event_time + $period_date*self::DAYTIME_ONE;
                      if( $due_time < $current_time){
                          if ($latest_time > $event_time)
                              continue;
                          //Follow up is overdue
                          $over_weeks = intval(($current_time - $due_time)/self::DAYTIME_ONE / self::PERIOD_WEEK);
                          array_push($followup_csv_data['overdue'],array($current_patient->getFirst_name(),$current_patient->getLast_name(),$current_patient->hos_num,$current_patient->dob,$current_patient->getAge(),$current_patient->getDiagnosesTermsArray(),$over_weeks));
                          if(!array_key_exists($over_weeks, $followup_patient_list['overdue'])){
                              $followup_patient_list['overdue'][$over_weeks] = array($current_patient->id);
                          } else {
                              array_push($followup_patient_list['overdue'][$over_weeks], $current_patient->id);
                          }

                      } else {
                          if ($latest_worklist_time >$current_time && $latest_worklist_time < $due_time)
                              continue;
                          $coming_weeks = intval(($due_time - $current_time)/self::DAYTIME_ONE/self::PERIOD_WEEK);
                          array_push($followup_csv_data['coming'],array($current_patient->getFirst_name(),$current_patient->getLast_name(),$current_patient->hos_num,$current_patient->dob,$current_patient->getAge(),$current_patient->getDiagnosesTermsArray(),$coming_weeks));
                          if(!array_key_exists($coming_weeks, $followup_patient_list['coming'])){
                              $followup_patient_list['coming'][$coming_weeks] = array($current_patient->id);
                          } else {
                              array_push($followup_patient_list['coming'][$coming_weeks], $current_patient->id);
                          }
                      }
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
          if (isset($this->surgeon)){
              if ($this->surgeon !== $current_event->created_user_id){
                  continue;
              }
          }
          $current_episode = $current_event->episode;
          $current_subspecialty = $current_episode->getSubspecialtyID();
          if($current_subspecialty!=$subspecialty_id && isset($subspecialty_id))
              continue;

          $current_patient = $ticket->patient;
          if ($diagnosis){
              $current_patient_diagnoses = $this->queryAllDiagnosisForPatient($current_patient->id);
              if (!in_array($diagnosis,$current_patient_diagnoses)){
                  continue;
              }
          }
          $latest_worklist_time = $this->checkPatientWorklist($current_patient->id)/1000;
          $latest_examination = Helper::mysqlDate2JsTimestamp($current_patient->getLatestExaminationEvent()->event_date)/1000;
          $latest_time = isset($latest_worklist_time)? max($latest_examination, $latest_worklist_time):$latest_examination;
          $quantity = $ticket_followup['followup_quantity'];
          if ($quantity > 0) {
              $period_date = $quantity * $this->getPeriodDate($ticket_followup['followup_period']);
              $due_time = $assignment_time + $period_date * self::DAYTIME_ONE;

              if ($due_time < $current_time) {
                  if ($latest_time > $assignment_time)
                      continue;
                  //Follow up is overdue
                  $over_weeks = intval(($current_time - $due_time) / self::DAYTIME_ONE / self::PERIOD_WEEK);
                  array_push($followup_csv_data['overdue'],array($current_patient->getFirst_name(),$current_patient->getLast_name(),$current_patient->hos_num,$current_patient->dob,$current_patient->getAge(),$current_patient->getDiagnosesTermsArray(),$coming_weeks));
                  if (!array_key_exists($over_weeks, $followup_patient_list['overdue'])) {
                      $followup_patient_list['overdue'][$over_weeks] = array($current_patient->id);
                  } else {
                      array_push($followup_patient_list['overdue'][$over_weeks], $current_patient->id);
                  }
              } else {
                  if ($latest_worklist_time >$current_time && $latest_worklist_time < $due_time)
                      continue;

                  $coming_weeks = intval(($due_time - $current_time) / self::DAYTIME_ONE / self::PERIOD_WEEK);
                  array_push($followup_csv_data['coming'],array($current_patient->getFirst_name(),$current_patient->getLast_name(),$current_patient->hos_num,$current_patient->dob,$current_patient->getAge(),$current_patient->getDiagnosesTermsArray(),$coming_weeks));
                  if (!array_key_exists($coming_weeks, $followup_patient_list['coming'])) {
                      $followup_patient_list['coming'][$coming_weeks] = array($current_patient->id);
                  } else {
                      array_push($followup_patient_list['coming'][$coming_weeks], $current_patient->id);
                  }
              }
          }
      }

      /* Get the waiting follow up data, uses Document event with referral letter type and worklist time
      To calculate how long a patient will wait frm the date of referral to the date assigned in a worklist*/
      $referral_document_elements = Element_OphCoDocument_Document::model()->findAll();

      foreach ($referral_document_elements as $referral_element){
          if ($referral_element->sub_type->name !== 'Referral Letter'){
              continue;
          }
          $current_event = $referral_element->event;
          if (isset($current_event)){
              $current_episode = $current_event->episode;
              $current_subspecialty = $current_episode->getSubspecialtyID();
              if (($current_subspecialty !== $subspecialty_id && isset($subspecialty_id)) || !isset($current_episode->firm_id) ){
                  continue;
              }
              if (isset($this->surgeon)){
                  if ($this->surgeon !== $current_event->created_user_id){
                      continue;
                  }
              }
              $current_patient = $current_episode->patient;
              if (isset($diagnosis)){
                  $current_patient_diagnoses = $this->queryAllDiagnosisForPatient($current_patient->id);
                  if (!in_array($diagnosis,$current_patient_diagnoses)){
                      continue;
                  }
              }
              $current_referral_date = Helper::mysqlDate2JsTimestamp($referral_element->created_date) / 1000;
              if( ($start_date && $current_referral_date < $start_date) ||
                  ($end_date && $current_referral_date > $end_date))
                  continue;
              $current_patient_on_worklist = WorklistPatient::model()->findByAttributes(array('patient_id'=>$current_patient->id), array('order'=>'t.when ASC'));
              if (isset($current_patient_on_worklist) && !empty($current_patient_on_worklist)){
                  $appointment_time = Helper::mysqlDate2JsTimestamp($current_patient_on_worklist->when)/1000;
                  if($appointment_time >= $current_referral_date){
                      $waiting_time = ceil((($appointment_time - $current_referral_date)/(self::WEEKTIME)));
                      array_push($followup_csv_data['waiting'],array($current_patient->getFirst_name(),$current_patient->getLast_name(),$current_patient->hos_num,$current_patient->dob,$current_patient->getAge(),$current_patient->getDiagnosesTermsArray(),$waiting_time));
                      if (! isset($followup_patient_list['waiting'][$waiting_time])){
                          $followup_patient_list['waiting'][$waiting_time]= array();
                      }
                      if (!array_key_exists($current_patient->id, $this->patient_list)){
                          $this->patient_list[$current_patient->id] = $current_patient;
                      }
                      array_push($followup_patient_list['waiting'][$waiting_time],$current_patient->id);
                  }
              }
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
          Yii::log($this->surgeon);
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

        if($speciality_name === 'Glaucoma'){
            list($left_iop_list, $right_iop_list) = $this->getCustomIOP($subspecialty_id);
            list($left_va_list, $right_va_list) = $this->getCustomVA($subspecialty_id);
        } else {
            list($left_va_list, $right_va_list) = $this->getCustomVA($subspecialty_id,$this->surgeon);
            list($left_crt_list, $right_crt_list) = $this->getCustomCRT($subspecialty_id,$this->surgeon);
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
                    )
                ),
                array(
                    'name' => $speciality_name === 'Glaucoma' ? 'IOP' : 'CRT',
                    'yaxis' => 'y2',
                    'x' => $speciality_name === 'Glaucoma' ? array_keys(${$side . '_iop_list'}) : array_keys(${$side.'_crt_list'}) ,
                    'y' => array_map(
                        function ($item) {
                            return $item['average'];
                        }, $speciality_name === 'Glaucoma' ? array_keys(${$side . '_iop_list'}) : array_keys(${$side.'_crt_list'})),
                    'customdata' => array_map(
                        function ($item) {
                            return $item['patients'];
                        },
                        $speciality_name === 'Glaucoma' ? array_keys(${$side . '_iop_list'}) : array_keys(${$side.'_crt_list'})),
                    'error_y' => array(
                        'type' => 'data',
                        'array' => array_map(
                            function ($item) {
                                return $item['SD'];
                            },
                            $speciality_name === 'Glaucoma' ? array_keys(${$side . '_iop_list'}) : array_keys(${$side.'_crt_list'})),
                        'visible' => true,
                        'color' => '#aaa',
                        'thickness' => 1
                    )
                )
            );
        }
        $custom_data['csv_data'] = $this->custom_csv_data;
        return array($follow_patient_list, $common_ophthalmic_disorders, $current_user, $user_list, $custom_data);
    }


}