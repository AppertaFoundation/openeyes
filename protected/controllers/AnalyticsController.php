<?php

class AnalyticsController extends BaseController
{
    const DAYTIME_ONE = 86400;
    const DAYTIME_THREE = self::DAYTIME_ONE * 3;
    const WEEKTIME = 604800;
    private $current_user ;

  public $layout = '//layouts/events_and_episodes';
  protected $patient_list = array();

  protected function getSubspecialtyID($subspecialty_name){
      return Subspecialty::model()->findByAttributes(array('name'=>$subspecialty_name))->id;
  }
  public function accessRules()
  {
    return array(
      array('allow',
        'actions' => array('cataract', 'medicalRetina', 'glaucoma', 'vitreoretinal', 'ad','customData','showLog'),
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
      $filters = array(
          'date_from' => 0,
          'date_to' => Helper::mysqlDate2JsTimestamp(date("Y-m-d h:i:s")),
      );
      if (isset($_POST)) {
          if (isset($_POST['date-range-from'])){
              $filters['date_from'] = $_POST['date-range-from'];
              $filters['date-to'] = $_POST['date-range-to'];
          }
      }

      list($left_va_list, $right_va_list) = $this->getCustomVA($filters);
      list($left_crt_list, $right_crt_list) = $this->getCustomCRT($filters);



      $clinical_data = array(
          'title' => 'Disorders Section',
          'x' => $disorder_data['x'],
          'y' => $disorder_data['y'],
          'text' => $disorder_data['text'],
          'customdata' =>$disorder_data['customdata']
      );

      $service_data = array(
          'title' => 'Service Section',
          'x' => array(1, 2, 3, 4, 5, 6),
          'y' => array(1, 1, 2, 3, 4, 5)
      );

      $custom_data = array(
          //left side eye data array
          array(
              array(
                  'name' => 'VA',
                  'x' => array_keys($left_va_list),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values($left_va_list)),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values($left_va_list)),
                  'error_y'=> array(
                      'type'=> 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values($left_va_list)),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              ), array(
              'name' => 'CRT',
              'yaxis' =>'y2',
              'x' => array_keys($left_crt_list),
              'y' => array_map(
                  function ($item){
                      return $item['average'];
                  }, array_values($left_crt_list)),
              'customdata'=>array_map(
                  function($item){
                      return $item['patients'];
                  },
                  array_values($left_crt_list)),
              'error_y' => array(
                  'type' => 'data',
                  'array' => array_map(
                      function($item){
                          return $item['SD'];
                      },
                      array_values($left_crt_list)),
                  'visible' => true,
                  'color' => '#aaa',
                  'thickness' => 1
              )
            )
          ),
          //right side eye data array
          array(
              array(
                  'name' => 'VA',
                  'x' => array_keys($right_va_list),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values($right_va_list)),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values($right_va_list)),
                  'error_y'=> array(
                      'type'=> 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values($right_va_list)),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              ), array(
              'name' => 'CRT',
              'yaxis' =>'y2',
              'x' => array_keys($right_crt_list),
              'y' => array_map(
                  function ($item){
                      return $item['average'];
                  }, array_values($right_crt_list)),
              'customdata'=>array_map(
                  function($item){
                      return $item['patients'];
                  },
                  array_values($right_crt_list)),
              'error_y' => array(
                  'type' => 'data',
                  'array' => array_map(
                      function($item){
                          return $item['SD'];
                      },
                      array_values($right_crt_list)),
                  'visible' => true,
                  'color' => '#aaa',
                  'thickness' => 1
              )
          )
          ),
      );

    $this->render('/analytics/analytics_container',
        array(
            'specialty'=>'Medical Retina',
            'clinical_data'=> $clinical_data,
            'service_data'=> $service_data,
            'custom_data' => $custom_data,
            'patient_list' => $this->patient_list
        )
    );
  }

  public function actionGlaucoma(){
      $subspecialty_id = $this->getSubspecialtyID('Glaucoma');
      $disorder_data = $this->getDisorders($subspecialty_id);
      $filters = array(
          'date_from' => 0,
          'date_to' => Helper::mysqlDate2JsTimestamp(date("Y-m-d h:i:s")),
      );
      list($left_iop_list, $right_iop_list) = $this->getCustomIOP($filters);
      list($left_va_list, $right_va_list) = $this->getCustomVA($filters);

      $service_data = array(
          'title' => 'Clinical Section',
          'x'=> array(1,2,3,4,5,6),
          'y'=> array(18, 9, 10, 7, 13, 16)
      );

      $clinical_data = array(
          'title' => 'Disorders Section',
          'x' => $disorder_data['x'],
          'y' => $disorder_data['y'],
          'text' => $disorder_data['text']
      );

      $custom_data = array(
          //left side eye data array
          array(
              array(
                  'name' => 'VA',
                  'x' => array_keys($left_va_list),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values($left_va_list)),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values($left_va_list)),
                  'error_y'=> array(
                      'type'=> 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values($left_va_list)),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              ),
              array(
                  'name' => 'IOP',
                  'yaxis' => 'y2',
                  'x' => array_keys($left_iop_list),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values($left_iop_list)),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values($left_iop_list)),
                  'error_y' => array(
                      'type' => 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values($left_iop_list)),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              )
          ),
          //right side eye data array
          array(
              array(
                  'name' => 'VA',
                  'x' => array_keys($right_va_list),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values($right_va_list)),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values($right_va_list)),
                  'error_y'=> array(
                      'type'=> 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values($right_va_list)),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              ),
              array(
                  'name' => 'IOP',
                  'yaxis' => 'y2',
                  'x' => array_keys($right_iop_list),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values($right_iop_list)),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values($right_iop_list)),
                  'error_y' => array(
                      'type' => 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values($right_iop_list)),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              )
          ),

      );

    $this->render('/analytics/analytics_container',
        array(
            'specialty'=>'Glaucoma',
            'clinical_data'=> $clinical_data,
            'service_data'=> $service_data,
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


  public function getCustomVA($filters) {
//      $va_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->findAll();
//      return $this->getCustomDataList($va_elements,$filters,"VA");
      $va_elements = $this->queryVA($filters);
      return $this->getCustomDataListQuery($va_elements,$filters,"VA");
  }

  public function getCustomCRT($filters) {
      $crt_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_OCT::model()->findAll();
      return $this->getCustomDataList($crt_elements,$filters,'CRT');
  }


  public function getCustomIOP($filters){
//      $iop_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure::model()->findAll();
//      return $this->getCustomDataList($iop_elements, $filters,'IOP');
      $iop_elements = $this->queryIOP($filters);
      return $this->getCustomDataListQuery($iop_elements,$filters,"IOP");
  }

  public function validateFilters($filters, $age, $protocol, $date){
      $return_value = true;
      if (isset($filters['age_min'])){
          $return_value = ($age >= (int)$filters['age_min']);
      }
      if (isset($filters['age_max']) && $return_value){
          $return_value = ($age <= (int)$filters['age_max']);
      }
      if (isset($filters['date_to']) && $return_value){
          $return_value = ($date < $filters['date_to']);
      }
      if (isset($filters['date_from']) && $return_value){
          $return_value = ($date > $filters['date_from']);
      }

      return $return_value;

  }

  public function queryVA($filters){
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
          ->group('va_id, side');

      if(isset($filters['diagnosis'])){
          $command->andWhere(array('like','term',$filters['diagnosis']));
      }

      return $command->queryAll();
  }

    public function queryIOP($filters){
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
            ->group('iop_id, side');

        if(isset($filters['diagnosis'])){
            $command->andWhere(array('like','term',$filters['diagnosis']));
        }

        return $command->queryAll();
    }

    public function getCustomDataListQuery($elements,$filters,$type,$readings = null){
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
                if ($this->validateFilters($filters, $current_patient->getAge(), $current_protocol, $current_time)) {
                    if (!array_key_exists($current_patient->id, $this->patient_list)) {
                        $this->patient_list[$current_patient->id] = $current_patient;
                    }

                    if (!array_key_exists($current_patient->id, $patient_list)) {
                        $patient_list[$current_patient->id] = array();
                    }
                    if (isset($filters['diagnosis'])){
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



  public function getCustomDataList($elements,$filters,$type,$readings = null){
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
              $validation = $this->validateFilters($filters, $current_patient->getAge(), $current_protocol, $current_time);
              if ($this->validateFilters($filters, $current_patient->getAge(), $current_protocol, $current_time)) {
                  if (!array_key_exists($current_patient->id, $this->patient_list)) {
                      $this->patient_list[$current_patient->id] = $current_patient;
                  }

                  if (!array_key_exists($current_patient->id, $patient_list)) {
                      $patient_list[$current_patient->id] = array();
                  }

                  if (isset($filters['treatment'])){
                     if ($current_treatment_left !== $filters['treatment']){
                         $left_reading = false;
                     }
                     if ($current_treatment_right !== $filters['treatment']){
                         $right_reading = false;
                     }
                  }
                  if (isset($filters['diagnosis'])){
                      if (!empty($current_diagnoses_left)){
                          $i = 1;
                          foreach ($current_diagnoses_left as $diagnosis){
                              if (in_array($diagnosis, $filters['diagnosis'])){
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
                              if (in_array($diagnosis, $filters['diagnosis'])){
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
              if( ($start_date && $current_time < Helper::mysqlDate2JsTimestamp($start_date)) ||
                  ($end_date && $current_time > Helper::mysqlDate2JsTimestamp($end_date)))
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
  public function actionCustomData(){
      $specialty = Yii::app()->request->getParam('specialty');
      $diagnoses_MR = array("Age related macular degeneration","Branch retinal vein occlusion with macular oedema","Central retinal vein occlusion with macular oedema","Diabetic macular oedema");
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

      $filters = array(
          'date_from' => $dateFrom,
          'date_to' => $dateTo,
          'age_min'=>$ageMin,
          'age_max'=>$ageMax,
          'diagnosis'=>$diagnosis,
          'protocol'=>$protocol,
          'plot-va'=>$plotVA,
          'treatment'=>$treatment,
      );

      $va_list = $this->getCustomVA($filters);
      if ($specialty === "Glaucoma"){
          $second_list = $this->getCustomIOP($filters);
      }elseif ($specialty === "Medical Retina"){
          $second_list = $this->getCustomCRT($filters);
      }

      $custom_data = array(
          array(
              array(
                  'x' => array_keys($va_list[0]),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values($va_list[0])),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values($va_list[0])),
                  'error_y'=> array(
                      'type'=> 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values($va_list[0])),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              ),
              array(
                  'yaxis' =>'y2',
                  'x' => array_keys($second_list[0]),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values($second_list[0])),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values($second_list[0])),
                  'error_y'=> array(
                      'type'=> 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values($second_list[0])),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              ),
          ),
          array(
              array(
                  'x' => array_keys($va_list[1]),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values($va_list[1])),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values($va_list[1])),
                  'error_y'=> array(
                      'type'=> 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values($va_list[1])),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              ),
              array(
                  'yaxis' =>'y2',
                  'x' => array_keys($second_list[1]),
                  'y' => array_map(
                      function ($item){
                          return $item['average'];
                      }, array_values($second_list[1])),
                  'customdata'=>array_map(
                      function($item){
                          return $item['patients'];
                      },
                      array_values($second_list[1])),
                  'error_y'=> array(
                      'type'=> 'data',
                      'array' => array_map(
                          function($item){
                              return $item['SD'];
                          },
                          array_values($second_list[1])),
                      'visible' => true,
                      'color' => '#aaa',
                      'thickness' => 1
                  )
              ),
          ),

      );
      $this->renderJSON($custom_data);
  }
}