<?php

class AnalyticsController extends BaseController
{
    const DAYTIME_ONE = 86400;
    const DAYTIME_THREE = self::DAYTIME_ONE * 3;
    const WEEKTIME = 604800;

  public $layout = '//layouts/events_and_episodes';
  protected $patient_list = array();

  public function accessRules()
  {
    return array(
      array('allow',
        'actions' => array('cataract', 'medicalRetina', 'glaucoma', 'vitreoretinal', 'ad'),
        'users'=> array('@')
      ),
    );
  }

  public function actionCataract(){
      $assetManager = Yii::app()->getAssetManager();
      $assetManager->registerScriptFile('js/dashboard/OpenEyes.Dash.js', null, null, AssetManager::OUTPUT_ALL, false);

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
      list($left_va_list, $right_va_list) = $this->getCustomVA();
      list($left_crt_list, $right_crt_list) = $this->getCustomCRT();

      $filters = array(
          'date_from' => null,
          'date-to' => null
      );
      if (isset($_POST)) {
          if (isset($_POST['date-range-from'])){
              $filters['date_from'] = $_POST['date-range-from'];
              $filters['date-to'] = $_POST['date-range-to'];
          }
      }

      $clinical_data = array(
          'title' => 'Clinical Section',
          'y'=> array(1,2,3,4,5,6),
          'x'=> array(18, 9, 10, 7, 13, 16)
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
//          'error_y'=> array(
//              'type'=> 'data',
//              'array' => array(1, 2, 1, 2, 1),
//              'visible' => true,
//              'color' => '#aaa',
//              'thickness' => 1
//          )
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
//              'error_y' => array(
//                  'type' => 'data',
//                  'array' => array(2, 1, 2, 1, 2, 1),
//                  'visible' => true,
//                  'color' => '#aaa',
//                  'thickness' => 1
//              )
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
      list($left_iop_list, $right_iop_list) = $this->getCustomIOP();
      list($left_va_list, $right_va_list) = $this->getCustomVA();

      $clinical_data = array(
          'title' => 'Clinical Section',
          'x'=> array(1,2,3,4,5,6),
          'y'=> array(18, 9, 10, 7, 13, 16)
      );

      $service_data = array(
          'title' => 'Service Section',
          'x' => array(12, 5, 9, 7, 6),
          'y' => array(0, 1, 2, 3, 4, 5)
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
//              'error_y'=> array(
//                  'type'=> 'data',
//                  'array' => array(1, 2, 1, 2, 1),
//                  'visible' => true,
//                  'color' => '#aaa',
//                  'thickness' => 1
//              )
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
//              'error_y' => array(
//                  'type' => 'data',
//                  'array' => array(2, 1, 2, 1, 2, 1),
//                  'visible' => true,
//                  'color' => '#aaa',
//                  'thickness' => 1
//              )
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
    $this->render('/analytics/analytics_container',
        array(
            'specialty'=>'Vitreoretinal',
            'clinical_data'=> array(),
            'service_data'=> array(),
            'custom_data' => array()
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

  public function getEventsByType($event_type, $date_range = null){
    $event_type_id = EventType::model()->findByAttributes(array('name'=>$event_type))->id;
    $examination_events = Event::model()->findAllByAttributes(array('event_type_id'=>$event_type_id));
    foreach ($examination_events as $exam_item){
    }
    return $examination_events;
  }

  public function getElementByType($element_type, $date_range = null){
      $element_type_id = ElementType::model()->findByAttributes(array('name'=>$element_type))->id;

  }

  public function getCustomVA() {
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
                          ${$side.'_va_list'}[$current_week]['patients'][] = $patient_id;
                      } else {
                          ${$side.'_va_list'}[$current_week] = array(
                              'count'=> 1,
                              'sum' => $va_item[$side.'_reading'],
                              'patients' => array($patient_id),
                          );
                      }
                  }
              }
          }
      }
      foreach (['left', 'right'] as $side){
          foreach (${$side.'_va_list'} as &$va_item){
              $va_item['average'] = round($va_item['sum']/$va_item['count']);
          }
      }
      ksort($left_va_list);
      ksort($right_va_list);
      return [$left_va_list,$right_va_list];
  }

  public function getCustomCRT() {
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
                          ${$side.'_crt_list'}[$current_week]['patients'][] = $patient_id;
                      } else {
                          ${$side.'_crt_list'}[$current_week] = array(
                              'count'=> 1,
                              'sum' => $crt_item[$side.'_crt'],
                              'patients' => array($patient_id),
                          );
                      }
                  }
              }
          }
      }
      foreach (['left', 'right'] as $side){
          foreach (${$side.'_crt_list'} as &$crt_item){
              $crt_item['average'] = round($crt_item['sum']/$crt_item['count']);
          }
      }
      ksort($left_crt_list);
      ksort($right_crt_list);
      return [$left_crt_list,$right_crt_list];
  }


  public function sortByTime($a, $b){
      if($a['event_time']==$b['event_time'])
          return 0;
      return ($a['event_time']<$b['event_time'])? -1: 1;
  }

  public function getCustomIOP(){
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
                          ${$side.'_iop_list'}[$current_week]['patients'][] = $patient_id;
                      } else {
                          ${$side.'_iop_list'}[$current_week] = array(
                              'count'=> 1,
                              'sum' => $iop_item[$side.'_reading'],
                              'patients' => array($patient_id),
                          );
                      }
                  }
              }

          }
      }

      foreach (['left', 'right'] as $side){
          foreach (${$side.'_iop_list'} as &$iop_item){
              $iop_item['average'] = round($iop_item['sum']/$iop_item['count']);
          }
      }
      ksort($left_iop_list);
      ksort($right_iop_list);

      return [$left_iop_list, $right_iop_list];
  }


  public function getDisorders(){

  }
}