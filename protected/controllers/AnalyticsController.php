<?php

class AnalyticsController extends BaseController
{
    const DAYTIME_ONE = 86400;
    const DAYTIME_THREE = self::DAYTIME_ONE * 3;
    const WEEKTIME = 604800;
    const PERIOD_DAY = 1;
    const PERIOD_WEEK = 7;
    const PERIOD_MONTH = 30;
    const PERIOD_YEAR = 365;
    private $current_user ;

  public $layout = '//layouts/events_and_episodes';
  protected $patient_list = array();
  protected $filters;

  protected function getSubspecialtyID($subspecialty_name){
      return Subspecialty::model()->findByAttributes(array('name'=>$subspecialty_name))->id;
  }
  public function accessRules()
  {
    return array(
      array('allow',
        'actions' => array('cataract', 'medicalRetina', 'glaucoma', 'vitreoretinal', 'ad','updateData',),
        'users'=> array('@')
      ),
    );
  }

  public function actionCataract(){
      $assetManager = Yii::app()->getAssetManager();
      $assetManager->registerScriptFile('js/dashboard/OpenEyes.Dash.js', null, null, AssetManager::OUTPUT_ALL, false);
      $subspecialty_id = $this->getSubspecialtyID('Cataract');
      $this->getDisorders($subspecialty_id);
      $this->patient_list = $this->queryCataractEventList();

      $this->render('/analytics/analytics_container',
          array(
              'specialty'=>'Cataract',
              'clinical_data'=> array(),
              'service_data'=> array(),
              'custom_data' => array(),
              'patient_list' => $this->patient_list,
          )
      );
  }
  public function actionMedicalRetina(){
      $subspecialty_id = $this->getSubspecialtyID('Medical Retina');

      $this->current_user = User::model()->findByPk(Yii::app()->user->id);
      $roles = Yii::app()->user->getRole(Yii::app()->user->id);
      $disorder_data = $this->getDisorders($subspecialty_id);
      $follow_patient_list = $this->getFollowUps($subspecialty_id);

      $this->filters = array(
          'date_from' => 0,
          'date_to' => Helper::mysqlDate2JsTimestamp(date("Y-m-d h:i:s")),
      );


      list($left_va_list, $right_va_list) = $this->getCustomVA($subspecialty_id);
      list($left_crt_list, $right_crt_list) = $this->getCustomCRT($subspecialty_id);



      $clinical_data = array(
          'title' => 'Disorders Section',
          'x' => $disorder_data['x'],
          'y' => $disorder_data['y'],
          'text' => $disorder_data['text'],
          'customdata' =>$disorder_data['customdata']
      );

      $custom_data = array();
      foreach (['left','right'] as $side){
          $custom_data[] = array(
              array(
                  'name' => 'VA',
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
                  'name' => 'CRT',
                  'yaxis' => 'y2',
                  'x' => array_keys(${$side.'_crt_list'}),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values(${$side.'_crt_list'})),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values(${$side.'_crt_list'})),
                  'error_y' => array(
                      'type' => 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values(${$side.'_crt_list'})),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              )
          );
      }

    $this->render('/analytics/analytics_container',
        array(
            'specialty'=>'Medical Retina',
            'clinical_data'=> $clinical_data,
            'service_data'=> $follow_patient_list,
            'custom_data' => $custom_data,
            'patient_list' => $this->patient_list
        )
    );
  }

  public function actionGlaucoma(){
      $subspecialty_id = $this->getSubspecialtyID('Glaucoma');
      $this->filters = array(
          'date_from' => 0,
          'date_to' => Helper::mysqlDate2JsTimestamp(date("Y-m-d h:i:s")),
      );
      list($left_iop_list, $right_iop_list) = $this->getCustomIOP($subspecialty_id);
      list($left_va_list, $right_va_list) = $this->getCustomVA($subspecialty_id);
      $disorder_data = $this->getDisorders($subspecialty_id);
      $follow_patient_list = $this->getFollowUps($subspecialty_id);

      $clinical_data = array(
          'title' => 'Disorders Section',
          'x' => $disorder_data['x'],
          'y' => $disorder_data['y'],
          'text' => $disorder_data['text'],
          'customdata' =>$disorder_data['customdata']
      );
      $custom_data = array();
      foreach (['left','right'] as $side){
          $custom_data[] = array(
              array(
                  'name' => 'VA',
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
                  'name' => 'IOP',
                  'yaxis' => 'y2',
                  'x' => array_keys(${$side.'_iop_list'}),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values(${$side.'_iop_list'})),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values(${$side.'_iop_list'})),
                  'error_y' => array(
                      'type' => 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values(${$side.'_iop_list'})),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              )
          );
      }

    $this->render('/analytics/analytics_container',
        array(
            'specialty'=>'Glaucoma',
            'clinical_data'=> $clinical_data,
            'service_data'=> $follow_patient_list,
            'custom_data' => $custom_data,
            'patient_list' => $this->patient_list
        )
    );
  }

  public function actionVitreoretinal(){
      $subspecialty_id = $this->getSubspecialtyID('Vitreoretinal');
      $disorder_data = $this->getDisorders($subspecialty_id);
      $clinical_data = array(
          'title' => 'Disorders Section',
          'x' => $disorder_data['x'],
          'y' => $disorder_data['y'],
          'text' => $disorder_data['text']
      );
      $this->render('/analytics/analytics_container',
        array(
            'specialty'=>'Vitreoretinal',
            'clinical_data'=> $clinical_data,
            'service_data'=> array(),
            'custom_data' => array(),
            'patient_list' => $this->patient_list
        )
    );
  }

  public function actionAd(){
    $this->render('/analytics/analytics_container',
        array(
            'specialty'=>'AD',
            'clinical_data'=> array(),
            'service_data'=> array(),
            'custom_data' => array()
        )
    );
  }

  public function sortByTime($a, $b){
      if($a['event_time']==$b['event_time'])
          return 0;
      return ($a['event_time']<$b['event_time'])? -1: 1;
  }

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


  public function getCustomVA($subspecialty_id) {
//      $va_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->findAll();
//      return $this->getCustomDataList($va_elements,"VA");
      $va_elements = $this->queryVA($subspecialty_id);
      return $this->getCustomDataListQuery($va_elements,"VA");
  }

  public function getCustomCRT($subspecialty_id) {
      $crt_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_OCT::model()->findAll();
      return $this->getCustomDataList($crt_elements,'CRT', $subspecialty_id);
  }


  public function getCustomIOP($subspecialty_id){
//      $iop_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure::model()->findAll();
//      return $this->getCustomDataList($iop_elements,'IOP');
      $iop_elements = $this->queryIOP($subspecialty_id);
      return $this->getCustomDataListQuery($iop_elements,"IOP");
  }

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

  public function queryVA($subspecialty_id){
      $command =  Yii::app()->db->createCommand()
          ->select('e2.patient_id as patient_id, 
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
            ->select('e2.patient_id as patient_id, 
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

    public function getCustomDataListQuery($elements,$type,$readings = null){
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

            $current_protocol = null;
                $current_patient = Patient::model()->findByPk($element['patient_id']);
                $current_time = Helper::mysqlDate2JsTimestamp($element['date']);
                // eye 1 left, 2 right, 3 both
                if ($this->validateFilters( $current_patient->getAge(), $current_protocol, $current_time)) {
                    if (!array_key_exists($current_patient->id, $this->patient_list)) {
                        $this->patient_list[$current_patient->id] = $current_patient;
                    }

                    if (!array_key_exists($current_patient->id, $patient_list)) {
                        $patient_list[$current_patient->id] = array();
                    }
                    if (isset($this->filters['diagnosis'])){
                        if ($element['eye_id'] == 1){
                            $right_reading = null;
                        }elseif ($element['eye_id'] == 2){
                            $left_reading = null;
                        }
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

  public function getCustomDataList($elements,$type,$subspecialty_id,$readings = null){
      $patient_list = array();
      $left_list = array();
      $right_list = array();

      foreach ($elements as $element){
          if ($type === "CRT"){
           $left_reading =  $element->left_crt;
           $right_reading = $element->right_crt;
          }else if ($type === "VAp"){
               $left_reading = $readings[$element->id][0];
               $right_reading = $readings[$element->id][1];
          }else{
              $left_reading = $element->getReading('left');
              $right_reading = $element->getReading('right');
          }
          $current_event = $element->event;
          if(isset($current_event->episode)) {
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

                      if (isset($this->filters['treatment'])){
                          if ($current_treatment_left !== $this->filters['treatment']){
                              $left_reading = false;
                          }
                          if ($current_treatment_right !== $this->filters['treatment']){
                              $right_reading = false;
                          }
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

  public function getDisorders($subspecialty_id, $start_date = null, $end_date = null){
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

      //get common ophthalmic disorders for given subspecialty
      $criteria = new CDbCriteria();
      $criteria->compare('subspecialty_id', $subspecialty_id);
      $common_ophthalmic_disorders = CommonOphthalmicDisorder::model()->findAll($criteria);
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
      foreach ($diagnoses_elements as $diagnosis_element_item){
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

              $diagnoses = $diagnosis_element_item->diagnoses;
              foreach($diagnoses as $diagnosis_item){
                  $disorder_id = $diagnosis_item->disorder->id;
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
          $disorder_list['y'][] = $i;
          $disorder_list['x'][] = count($disorder_patient_list[$key]['patient_list']);
          $disorder_list['text'][] = $disorder_patient_list[$key]['short_name'];
          $disorder_list['customdata'][] = $disorder_patient_list[$key]['patient_list'];
          $i++;
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

      return $disorder_list;
  }
  public function queryCataractEventList(){
      $command = Yii::app()->db->createCommand()
          ->select('event_id')
          ->from('et_ophtroperationnote_cataract');

      return $command->queryAll();
  }
  public function obtainFilters(){
      $diagnoses_MR = array("Age related macular degeneration","Branch retinal vein occlusion with macular oedema","Central retinal vein occlusion with macular oedema","Diabetic macular oedema");
      $specialty = Yii::app()->request->getParam('specialty');
      $dateFrom = Yii::app()->request->getParam('from');
      $dateTo = Yii::app()->request->getParam('to');
      $ageMin = Yii::app()->request->getParam('age-min');
      $ageMax = Yii::app()->request->getParam('age-max');
      $diagnosis = Yii::app()->request->getParam('diagnosis');
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
      );
  }

  public function actionUpdateData(){
      $specialty = Yii::app()->request->getParam('specialty');
      $this->obtainFilters();
      $subspecialty_id = $this->getSubspecialtyID($specialty);
      list($left_va_list, $right_va_list) = $this->getCustomVA($subspecialty_id);
      if ($specialty === "Glaucoma"){
          list($left_second_list,$right_second_list) = $this->getCustomIOP($subspecialty_id);
      }elseif ($specialty === "Medical Retina"){
          list($left_second_list,$right_second_list) = $this->getCustomCRT($subspecialty_id);
      }
      $subspecialty_id = $this->getSubspecialtyID($specialty);
      $disorder_data = $this->getDisorders($subspecialty_id,$this->filters['date_from'],$this->filters['date_to']);

      $clinical_data = array(
          'x' => $disorder_data['x'],
          'y' => $disorder_data['y'],
          'text' => $disorder_data['text'],
          'customdata' =>$disorder_data['customdata']
      );

      $custom_data = array();
      foreach (['left','right'] as $side){
          $custom_data[] = array(
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

      $service_data = $this->getFollowUps($subspecialty_id, $this->filters['date_from']/1000,$this->filters['date_to']/1000);
      $this->renderJSON(array($custom_data,$clinical_data, $service_data));
  }

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

  public function getFollowUps($subspecialty_id, $start_date = null, $end_date = null){
      $followup_patient_list = array(
          'overdue' => array(),
          'coming' => array(),
      );

      $followup_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome::model()->findAll();

      $current_time = time();
      foreach ($followup_elements as $followup_item){
          $current_event = $followup_item->event;
          if (isset($current_event->episode)){
              $event_time = Helper::mysqlDate2JsTimestamp($current_event->event_date)/1000;
              if( ($start_date && $event_time < $start_date) ||
                  ($end_date && $event_time > $end_date))
                  continue;

              $current_episode = $current_event->episode;
              $current_patient = $current_episode->patient;
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
                      if(!array_key_exists($over_weeks, $followup_patient_list['overdue'])){
                          $followup_patient_list['overdue'][$over_weeks] = array($current_patient->id);
                      } else {
                          array_push($followup_patient_list['overdue'][$over_weeks], $current_patient->id);
                      }

                  } else {
                      if ($latest_worklist_time >$current_time && $latest_worklist_time < $due_time)
                          continue;
                      $coming_weeks = intval(($due_time - $current_time)/self::DAYTIME_ONE/self::PERIOD_WEEK);
                      if(!array_key_exists($coming_weeks, $followup_patient_list['coming'])){
                          $followup_patient_list['coming'][$coming_weeks] = array($current_patient->id);
                      } else {
                          array_push($followup_patient_list['coming'][$coming_weeks], $current_patient->id);
                      }
                  }
              }
          }
      }

      $patient_tickets = \OEModule\PatientTicketing\models\Ticket::model()->findAll();
      $patientticket_api = new \OEModule\PatientTicketing\components\PatientTicketing_API();
      foreach ($patient_tickets as $ticket) {
          $ticket_followup = $patientticket_api->getFollowUp($ticket->id);
          $assignment_time = Helper::mysqlDate2JsTimestamp($ticket_followup['assignment_date']) / 1000;
          if( ($start_date && $assignment_time < $start_date) ||
              ($end_date && $assignment_time > $end_date))
              continue;

          $current_patient = $ticket->patient;
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
                  if (!array_key_exists($over_weeks, $followup_patient_list['overdue'])) {
                      $followup_patient_list['overdue'][$over_weeks] = array($current_patient->id);
                  } else {
                      array_push($followup_patient_list['overdue'][$over_weeks], $current_patient->id);
                  }
              } else {
                  if ($latest_worklist_time >$current_time && $latest_worklist_time < $due_time)
                      continue;

                  $coming_weeks = intval(($due_time - $current_time) / self::DAYTIME_ONE / self::PERIOD_WEEK);
                  if (!array_key_exists($coming_weeks, $followup_patient_list['coming'])) {
                      $followup_patient_list['coming'][$coming_weeks] = array($current_patient->id);
                  } else {
                      array_push($followup_patient_list['coming'][$coming_weeks], $current_patient->id);
                  }
              }
          }
      }

      ksort($followup_patient_list['overdue']);
      ksort($followup_patient_list['coming']);
      return $followup_patient_list;
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
}