<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "et_ophtrintravitinjection_complicat".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
 * @property string $oth_descrip
 *
 * The followings are the available model relations:
 *
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property array(OphTrIntravitrealinjection_Complication) $left_complications
 * @property array(OphTrIntravitrealinjection_Complication) $right_complications
 */

namespace OEModule\OphCiExamination\models;

class Element_OphCiExamination_PostOpComplications extends \SplitEventTypeElement
{
	public $service;
        public $firm;

	/**
	 * Returns the static model of the specified AR class.
	 * @return the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'et_ophciexamination_postop_complications';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
        public function rules()
        {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                    array('eye_id', 'complicationForBothEyes'),
                   // array('left_complication_id', 'side' => 'left'),
                    //  array('right_complication_id', 'side' => 'right'),
                    // The following rule is used by search().
                    // Please remove those attributes that should not be searched.
                    array('id, event_id, eye_id', 'safe', 'on' => 'search'),
            );
        }
        
        public function complicationForBothEyes($attribute,$params)
        {
            $complication_items = \Yii::app()->request->getParam('complication_items', array());
            
            if( !isset($complication_items['R']) || empty($complication_items['R'])){
                $this->addError($attribute, 'Complication must be recorded for both eyes: missing Right Eye');
            }
            
            
            if( !isset($complication_items['L']) || empty($complication_items['L'])){
                $this->addError($attribute, 'Complication must be recorded for both eyes: missing Left Eye');
            }
            
        }

	/**
	 * @return array relational rules.
	 */
	public function relations()
        {
            // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array(
                    'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                    'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
                    'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                    'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                
                    'right_values' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications', 'element_id', 'on' => 'right_values.eye_id = ' . \Eye::RIGHT),
                    'left_values' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications', 'element_id', 'on' => 'left_values.eye_id = ' . \Eye::LEFT),
                    'operation_notes' => array(self::BELONGS_TO, 'Event', 'event_id', 'on' => 'operation_notes.event_type_id = 4'),
            );
        }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event_id' => 'Event',
                        'right_values' => 'Post Op Complications'
		);
	}
        
        public function init()
        {
            $this->firm = \Firm::model()->findByPk(\Yii::app()->session['selected_firm_id']);
        }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('event_id', $this->event_id, true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}
        
        public function getRecordedComplications($eye_id, $operation_note_id = null)
        {
            
            $model = new OphCiExamination_Et_PostOpComplications;
            
            $criteria = new \CDbCriteria;

            $criteria->addCondition("t.element_id = :element_id");
            $criteria->addCondition("t.eye_id = :eye_id");
            $criteria->params['element_id'] = $this->id;
   
            if($operation_note_id){
              
                $criteria->addCondition("t.operation_note_id = :operation_note_id");
                $criteria->params['operation_note_id'] = $operation_note_id;
            }
            
            $criteria->params['eye_id'] = $eye_id;
            
            return $model->findAll($criteria);
        }
        
        public function afterSave()
        {

            $complication_items = \Yii::app()->request->getParam('complication_items', false);
            $operation_note_id = \Yii::app()->request->getParam('OphCiExamination_postop_complication_operation_note_id', null);
            
            $model = new OphCiExamination_Et_PostOpComplications;
            
            $model->deleteAllByAttributes(array(
                'element_id' => $this->id,
                'operation_note_id' => $operation_note_id,
            ));

            if($complication_items){
                
                if( isset($complication_items['R']) ){
                    foreach($complication_items['R'] as $cKey => $complication_id){

                        $model = new OphCiExamination_Et_PostOpComplications();
 
                        $model->element_id = $this->id;
                        $model->complication_id = $complication_id;
                        $model->eye_id = \Eye::RIGHT;
                        $model->operation_note_id = $operation_note_id;

                        if(!$model->save()){
                           throw new Exception('Unable to save post op complication: '.print_r($model->getErrors(), true));
                        }

                    }
                }
                if( isset($complication_items['L']) ){
                    foreach($complication_items['L'] as $cKey => $complication_id){
                        
                        $et_Complication = new OphCiExamination_Et_PostOpComplications();
                        
                        $et_Complication->element_id = $this->id;
                        $et_Complication->complication_id = $complication_id;
                        $et_Complication->eye_id = \Eye::LEFT;
                        $et_Complication->operation_note_id = $operation_note_id;

                        if(!$et_Complication->save()){
                            throw new Exception('Unable to save post op complication: '.print_r($et_Complication->getErrors(), true));
                        }
                    }
                }
            }
            
            parent::afterSave();
        }
        
        public function getOperationNoteList()
        {
            $response = array();
            $short_format = array();

            $procedureList = new \Element_OphTrOperationnote_ProcedureList();
            
            $procedureLists = $procedureList->findAll();
            
            $criteria = new \CDbCriteria;
         
            $criteria->select = "*";
            $criteria->join = "JOIN event ON t.event_id = event.id AND event.event_type_id = 4";
            $criteria->join .= " JOIN episode ON event.episode_id = episode.id";
       
            $criteria->order = "event.created_date desc";
       
            $procedureLists = $procedureList->findAll($criteria);
            
            foreach($procedureLists as $procedureList){
                
                if ( isset($procedureList->event) ){
                    $procesdures = $procedureList->procedures;

                    $date = new \DateTime($procedureList->event->created_date);

                    $name = $date->format("d M Y") . " ";

                    $name .= ($procedureList->eye_id != 3 ? ($procedureList->eye->name) : '') . " ";
                    $short_format = array();

                    foreach($procesdures as $procesdure){
                        $short_format[] = $procesdure->short_format;
                    }
                    $name .= implode(" + ", $short_format);

                    if( strlen ($name) > 60){
                        $name = substr($name, 0, 57);
                        $name .= "...";
                    }

                    $response[$procedureList->event->id] = $name;
                }
            }
           
            return $response;
        }
}
