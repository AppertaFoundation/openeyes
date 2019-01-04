<?php

class AnalyticsController extends BaseController
{
    const DAYTIME_ONE = 86400;
    const DAYTIME_THREE = self::DAYTIME_ONE * 3;
    const WEEKTIME = 604800;

  public $layout = '//layouts/events_and_episodes';

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
              'custom_data' => array()
          )
      );
  }
  public function actionMedicalRetina(){
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
          'x' => array(0,1,2,3,4,5),
          'y' => array(12,13,14,16, 40,29),
          'error_y'=> array(
              'type'=> 'data',
              'array' => array(1, 2, 1, 2, 1),
              'visible' => true,
              'color' => '#aaa',
              'thickness' => 1
          )
      ), array(
          'name' => 'CRT',
              'x' => array(3.5, 4, 6, 7, 9),
              'y' => array(34, 25, 61, 12, 15),
              'error_y' => array(
                  'type' => 'data',
                  'array' => array(2, 1, 2, 1, 2, 1),
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
            'custom_data' => $custom_data
        )
    );
  }

  public function actionGlaucoma(){
      $left_iop_list = $this->getCustomIOP();

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
              'x' => array(0,1,2,3,4,5),
              'y' => array(12,13,14,16, 40,29),
              'error_y'=> array(
                  'type'=> 'data',
                  'array' => array(1, 2, 1, 2, 1),
                  'visible' => true,
                  'color' => '#aaa',
                  'thickness' => 1
              )
          ),
          array(
              'name' => 'IOP',
              'x' => array_keys($left_iop_list),
              'y' => array_map(
                  function ($item){
                      return $item['average'];
              }, array_values($left_iop_list)),
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
            'custom_data' => $custom_data
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
    $va_patient_count = array();
    $examination_events = $this->getEventsByType('Examination');
    foreach ($examination_events as $exam_item){
      if (isset($exam_item->visual_acuity)){

      }
    }
  }

  public function getCustomCRT() {
      $crt_patient_list = array();
      $examination_events = $this->getEventsByType('Examination');
      foreach ($examination_events as $exam_item){
          $current_patient = $exam_item->patient->id;
          $current_crts = array();
      }
  }

  public function queryIOP(){

  }

  public function sortByTime($a, $b){
      if($a['event_time']==$b['event_time'])
          return 0;
      return ($a['event_time']<$b['event_time'])? -1: 1;
  }

  public function getCustomIOP(){
      $iop_patient_list = array();
      $patient_list = array();
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
              if (!array_key_exists($current_patient->id, $patient_list)){
                  $patient_list[$current_patient->id] = array(
                      'hospital_number' => $current_patient->hos_num,
                      'gender' => $current_patient->gender,
                      'age' => $current_patient->getAge(),
                      'name' => $current_patient->getFullName(),
                  );
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
      return $left_iop_list;
  }


  public function getDisorders(){

  }
}