<?php

class AnalyticsController extends BaseController
{
  public $layout = '//layouts/events_and_episodes';

  public function accessRules()
  {
    return array(
      array('allow',
        'actions' => array('medicalRetina', 'glaucoma', 'vr', 'ad'),
        'users'=> array('@')
      ),
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
          ), array(
              'name' => 'IOP',
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
            'specialty'=>'Glaucoma',
            'clinical_data'=> $clinical_data,
            'service_data'=> $service_data,
            'custom_data' => $custom_data
        )
    );
  }

  public function actionVr(){
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

  public function getCustomVA() {
    $va_patient_count = array();
    $examination_events = $this->getEventsByType('Examination');
    foreach ($examination_events as $exam_item){
      if (isset($exam_item->visual_acuity)){

      }
    }
  }

  public function getCustomCRT() {

  }

  public function getCustomIOP(){

  }

  public function getDisorders(){

  }
}