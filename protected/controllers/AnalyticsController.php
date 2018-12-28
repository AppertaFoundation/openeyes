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

    $this->render('/analytics/analytics_container');
  }

  public function actionGlaucoma(){
    $this->render('/analytics/analytics_container');

  }

  public function actionVr(){
    $this->render('/analytics/analytics_container');
  }

  public function actionAd(){
    $this->render('/analytics/analytics_container');
  }

  public function getEventsByType($event_type, $date_range = null){
    $event_type_id = EventType::model()->findByAttributes(array('name'=>$event_type))->id;
    $examination_events = Event::model()->findAllByAttributes(array('event_type_id'=>$event_type_id));
    foreach ($examination_events as $exam_item){
      Yii::log($exam_item->episode->patient->contact->first_name);
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
}