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
        'actions' => array('cataract', 'medicalRetina', 'glaucoma', 'vitreoretinal', 'ad','customData'),
        'users'=> array('@')
      ),
    );
  }

  public function actionCataract(){
      $assetManager = Yii::app()->getAssetManager();
      $assetManager->registerScriptFile('js/dashboard/OpenEyes.Dash.js', null, null, AssetManager::OUTPUT_ALL, false);

      $this->getDisorders();
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
      $va_patient_list = array();
      $left_va_list = array();
      $right_va_list = array();
      $va_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->findAll();
      foreach ($va_elements as $va){
          $left_reading = $va->getReading('left');
          $right_reading = $va->getReading('right');
          $current_event = $va->event;
          if(isset($current_event->episode)){
              $current_episode = $current_event->episode;
              $current_patient = $current_episode->patient;
              if (!array_key_exists($current_patient->id, $this->patient_list)){
                  $this->patient_list[$current_patient->id] = $current_patient;
              }
              $current_time = Helper::mysqlDate2JsTimestamp($current_event->event_date);
              if ($current_time>=$filters['date_from'] && $current_time<=$filters['date_to']){
                  if (!array_key_exists($current_patient->id, $va_patient_list)){
                      $va_patient_list[$current_patient->id]=array();
                  }
                  array_push($va_patient_list[$current_patient->id], array(
                          'left_reading'=>$left_reading,
                          'right_reading'=>$right_reading,
                          'event_time' => $current_time,
                          'weeks'=> 0
                      )
                  );
              }

          }
      }
      foreach ($va_patient_list as $patient_id => &$patient_vas){
          usort($patient_vas, array($this, 'sortByTime'));
          $start_time = $patient_vas[0]['event_time']/1000;
          foreach ($patient_vas as &$va_item){
              $current_week = floor(($va_item['event_time']/1000 - $start_time) / self::WEEKTIME);
              $va_item['weeks'] = $current_week;
              foreach (['left', 'right'] as $side){
                  if($va_item[$side.'_reading']){
                      if (array_key_exists((int)$current_week, ${$side.'_va_list'})){
                          ${$side.'_va_list'}[$current_week]['count']+=1;
                          ${$side.'_va_list'}[$current_week]['sum']+=$va_item[$side.'_reading'];
                          ${$side.'_va_list'}[$current_week]['readings'][] = $va_item[$side.'_reading'];
                          ${$side.'_va_list'}[$current_week]['patients'][] = $patient_id;
                      } else {
                          ${$side.'_va_list'}[$current_week] = array(
                              'count'=> 1,
                              'sum' => $va_item[$side.'_reading'],
                              'readings' => array($va_item[$side.'_reading']),
                              'patients' => array($patient_id),
                          );
                      }
                  }
              }
          }
      }
      foreach (['left', 'right'] as $side){
          foreach (${$side.'_va_list'} as &$va_item){
              if ($va_item['count'] > 1){
                  $va_item['average'] = round($va_item['sum']/$va_item['count']);
                  $va_item['SD'] = $this->calculateStandardDeviation($va_item['readings'], $va_item['sum'], $va_item['count']);

              } else {
                  $va_item['average'] = $va_item['sum'];
                  $va_item['SD'] = 0;
              }
          }
      }
      ksort($left_va_list);
      ksort($right_va_list);
      return [$left_va_list,$right_va_list];
  }

  public function getCustomCRT($filters) {
      $crt_patient_list = array();
      $left_crt_list = array();
      $right_crt_list = array();
      $crt_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_OCT::model()->findAll();
      foreach ($crt_elements as $crt){
          $left_crt = $crt->left_crt;
          $right_crt = $crt->right_crt;
          $current_event = $crt->event;
          if(isset($current_event->episode)){
              $current_episode = $current_event->episode;
              $current_patient = $current_episode->patient;
              if (!array_key_exists($current_patient->id, $this->patient_list)){
                  $this->patient_list[$current_patient->id] = $current_patient;
              }
              $current_time = Helper::mysqlDate2JsTimestamp($current_event->event_date);
              if ($current_time>=$filters['date_from'] && $current_time<=$filters['date_to']){
                  if (!array_key_exists($current_patient->id, $crt_patient_list)){
                    $crt_patient_list[$current_patient->id]=array();
                  }
                  array_push($crt_patient_list[$current_patient->id], array(
                        'left_crt'=>$left_crt,
                        'right_crt'=>$right_crt,
                        'event_time' => $current_time,
                         'weeks'=> 0
                      )
                  );
            }
          }
      }
      foreach ($crt_patient_list as $patient_id => &$patient_crts){
          usort($patient_crts, array($this, 'sortByTime'));
          $start_time = $patient_crts[0]['event_time']/1000;
          foreach ($patient_crts as &$crt_item){
              $current_week = floor(($crt_item['event_time']/1000 - $start_time) / self::WEEKTIME);
              $crt_item['weeks'] = $current_week;
              foreach (['left', 'right'] as $side){
                  if($crt_item[$side.'_crt']){
                      if (array_key_exists((int)$current_week, ${$side.'_crt_list'})){
                          ${$side.'_crt_list'}[$current_week]['count']+=1;
                          ${$side.'_crt_list'}[$current_week]['sum']+=$crt_item[$side.'_crt'];
                          ${$side.'_crt_list'}[$current_week]['values'][]=$crt_item[$side.'_crt'];
                          ${$side.'_crt_list'}[$current_week]['patients'][] = $patient_id;
                      } else {
                          ${$side.'_crt_list'}[$current_week] = array(
                              'count'=> 1,
                              'sum' => $crt_item[$side.'_crt'],
                              'values' => array($crt_item[$side.'_crt']),
                              'patients' => array($patient_id),
                          );
                      }
                  }
              }
          }
      }
      foreach (['left', 'right'] as $side){
          foreach (${$side.'_crt_list'} as &$crt_item){
              if($crt_item['count'] > 1){
                  $crt_item['average'] = round($crt_item['sum']/$crt_item['count']);
                  $crt_item['SD'] = $this->calculateStandardDeviation($crt_item['values'], $crt_item['sum'], $crt_item['count']);
              } else {
                  $crt_item['average'] = $crt_item['sum'];
                  $crt_item['SD'] = 0;
              }
          }
      }
      ksort($left_crt_list);
      ksort($right_crt_list);
      return [$left_crt_list,$right_crt_list];
  }


  public function getCustomIOP($filters){
      $iop_patient_list = array();
      $left_iop_list = array();
      $right_iop_list = array();
      $iop_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure::model()->findAll();

      foreach ($iop_elements as $iop){
          $left_reading = $iop->getReading('left');
          $right_reading = $iop->getReading('right');

          $current_event = $iop->event;
          if(isset($current_event->episode)){
              $current_episode = $current_event->episode;
              $current_patient = $current_episode->patient;
              if (!array_key_exists($current_patient->id, $this->patient_list)){
                  $this->patient_list[$current_patient->id] = $current_patient;
              }

              $current_time = Helper::mysqlDate2JsTimestamp($current_event->event_date);
              if ($current_time>=$filters['date_from'] && $current_time<=$filters['date_to']){
                  if (!array_key_exists($current_patient->id, $iop_patient_list)){
                    $iop_patient_list[$current_patient->id]=array();
                  }
                 array_push($iop_patient_list[$current_patient->id], array(
                      'left_reading'=>$left_reading,
                      'right_reading'=>$right_reading,
                      'event_time' => $current_time,
                      'weeks'=> 0
                  )
                );
              }
          }
      }

      foreach ($iop_patient_list as $patient_id => &$patient_iops){
          usort($patient_iops, array($this, 'sortByTime'));
          $start_time = $patient_iops[0]['event_time']/1000;
          foreach ($patient_iops as &$iop_item){
              $current_week = floor(($iop_item['event_time']/1000 - $start_time) / self::WEEKTIME);
              $iop_item['weeks'] = $current_week;
              foreach (['left', 'right'] as $side){
                  if($iop_item[$side.'_reading']){
                      if (array_key_exists((int)$current_week, ${$side.'_iop_list'})){
                          ${$side.'_iop_list'}[$current_week]['count']+=1;
                          ${$side.'_iop_list'}[$current_week]['sum']+=$iop_item[$side.'_reading'];
                          ${$side.'_iop_list'}[$current_week]['readings'][]=$iop_item[$side.'_reading'];
                          ${$side.'_iop_list'}[$current_week]['patients'][] = $patient_id;
                      } else {
                          ${$side.'_iop_list'}[$current_week] = array(
                              'count'=> 1,
                              'sum' => $iop_item[$side.'_reading'],
                              'readings' => array($iop_item[$side.'_reading']),
                              'patients' => array($patient_id),
                          );
                      }
                  }
              }

          }
      }

      foreach (['left', 'right'] as $side){
          foreach (${$side.'_iop_list'} as &$iop_item){
              if ($iop_item['count'] > 1){
                  $iop_item['average'] = round($iop_item['sum']/$iop_item['count']);
                  $iop_item['SD'] = $this->calculateStandardDeviation($iop_item['readings'], $iop_item['sum'], $iop_item['count']);
              } else {
                  $iop_item['average'] = $iop_item['sum'];
                  $iop_item['SD'] = 0;
              }
          }
      }
      ksort($left_iop_list);
      ksort($right_iop_list);

      return [$left_iop_list, $right_iop_list];
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
      $dateFrom = Yii::app()->request->getParam('date-range-from');
      $dateTo = Yii::app()->request->getParam('data-range-to');
      $ageMin = Yii::app()->request->getParam('age-min');
      $ageMax = Yii::app()->request->getParam('age-max');
      $diagnosis = Yii::app()->request->getParam('diagnosis');
      $protocol = Yii::app()->request->getParam('protocol');
      $plotVA = Yii::app()->request->getParam('plot-VA');
      $specialty = Yii::app()->request->getParam('specialty');

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
      if (!isset($ageMax)){
          $ageMax = null;
      }
      if (!isset($ageMin)){
          $ageMin = 0;
      }
      if (!isset($diagnosis)){
          $diagnosis = null;
      }
      if (!isset($protocol)){
          $protocol = null;
      }
      if (!isset($plotVA)){
          $plotVA = null;
      }

      $filters = array(
          'date_from' => $dateFrom,
          'date_to' => $dateTo,
          'age_min'=>$ageMin,
          'age_max'=>$ageMax,
          'diagnosis'=>$diagnosis,
          'protocol'=>$protocol,
          'plot-va'=>$plotVA,
      );
      $va_list = $this->getCustomVA($filters);
      if ($specialty === "Glaucoma"){
          $second_list = $this->getCustomIOP($filters);
      }elseif ($specialty === "Medical Retina"){
          $second_list = $this->getCustomCRT($filters);
      }

      $custom_data = array(
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
              ),array(
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
          )

      );
      $this->renderJSON($custom_data);
  }
}