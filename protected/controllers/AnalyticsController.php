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
      $va_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->findAll();
      return $this->getCustomDataList($va_elements,$filters,"VA");
  }

  public function getCustomCRT($filters) {
      $crt_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_OCT::model()->findAll();
      return $this->getCustomDataList($crt_elements,$filters,'CRT');
  }


  public function getCustomIOP($filters){
      $iop_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure::model()->findAll();
      return $this->getCustomDataList($iop_elements, $filters,'IOP');
  }

  public function validateFilters($filters, $age, $protocol, $treatment, $diagnosis, $date){
      $return_value = true;
      if (isset($filters['age_min'])){
          $return_value = ($age >= $filters['age_min']);
      }
      if (isset($filters['age_max'])){
          $return_value = ($age <= $filters['age_max']);
      }
      if (isset($filters['date_to'])){
          $return_value = ($date < $filters['date_to']);
      }
      if (isset($filters['date_from'])){
          $return_value = ($date > $filters['date_from']);
      }
      if (isset($filters['diagnosis'])){
          $return_value = (in_array($diagnosis,$filters['diagnosis']));
      }
      if (isset($filters['treatment'])){
          $return_value = (in_array($filters['treatment'],$treatment));
      }

      return $return_value;

  }
  public function getCustomDataList($elements,$filters,$type){

      $patient_list = array();
      $left_list = array();
      $right_list = array();

      foreach ($elements as $element){
          if ($type === "CRT"){
           $left_reading =  $element->left_crt;
           $right_reading = $element->right_crt;
          }else{
              $left_reading = $element->getReading('left');
              $right_reading = $element->getReading('right');
          }
          $current_event = $element->event;
          if(isset($current_event->episode)) {
              $current_episode = $current_event->episode;
              $current_patient = $current_episode->patient;
              $current_time = Helper::mysqlDate2JsTimestamp($current_event->event_date);
              $current_treatments = "";
              $current_diagnosis = "";
              $current_protocol = "";
              $current_patient_age = "";
              if ($this->validateFilters($filters, $current_patient_age, $current_protocol, $current_treatments, $current_diagnosis, $current_time)) {
                  if (!array_key_exists($current_patient->id, $this->patient_list)) {
                      $this->patient_list[$current_patient->id] = $current_patient;
                  }

                  if (!array_key_exists($current_patient->id, $patient_list)) {
                      $patient_list[$current_patient->id] = array();
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
                          ${$side.'_list'}[$current_week]['patients'][] = $patient_id;
                      } else {
                          ${$side.'_list'}[$current_week] = array(
                              'count'=> 1,
                              'sum' => $data_item[$side.'_reading'],
                              'patients' => array($patient_id),
                          );
                      }
                  }
              }

          }
      }

      foreach (['left', 'right'] as $side){
          foreach (${$side.'_list'} as &$data_item){
              $data_item['average'] = round($data_item['sum']/$data_item['count']);
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
      $dateFrom = Yii::app()->request->getParam('from');
      $dateTo = Yii::app()->request->getParam('to');
      $ageMin = Yii::app()->request->getParam('age-min');
      $ageMax = Yii::app()->request->getParam('age-max');
      $diagnosis = Yii::app()->request->getParam('diagnosis');
      $protocol = Yii::app()->request->getParam('protocol');
      $plotVA = Yii::app()->request->getParam('plot-VA');
      $specialty = Yii::app()->request->getParam('specialty');
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
          ),
//          array(
//          'yaxis' =>'y2',
//          'x' => [100,200,300,400],
//          'y' => [1,3,null,4],
//          'customdata'=>array_map(
//              function($item){
//                  return $item['patients'];
//              },
//              array_values($second_list[0])),
//      )

      );
      $this->renderJSON($custom_data);
  }
}