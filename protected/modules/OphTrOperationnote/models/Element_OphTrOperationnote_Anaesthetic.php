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
 * This is the model class for table "et_ophtroperationnote_anaesthetic".
 *
 * The followings are the available columns in table 'et_ophtroperationnote_anaesthetic':
 * @property integer $id
 * @property integer $event_id
 * @property integer $anaesthetic_type_id
 * @property integer $anaesthetist_id
 * @property integer $anaesthetic_delivery_id
 * @property string $anaesthetic_comment
 * @property integer $display_order
 * @property integer $anaesthetic_witness_id
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property EventType $eventType
 * @property ElementType $element_type
 * @property AnaestheticType $anaesthetic_type
 * @property Anaesthetist $anaesthetist
 * @property AnaestheticDelivery $anaesthetic_delivery
 * @property OphTrOperationnote_OperationAnaestheticAgent[] $anaesthetic_agent_assignments
 * @property AnaestheticAgent[] $anaesthetic_agents
 * @property OphTrOperationnote_AnaestheticComplication[] $anaesthetic_complications
 * @property User $witness
 */
class Element_OphTrOperationnote_Anaesthetic extends Element_OpNote
{
	public $service;
	public $surgeonlist;
	public $witness_enabled = false;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Element_OphTrOperationnote_Anaesthetic the static model class
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
		return 'et_ophtroperationnote_anaesthetic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_id, anaesthetist_id, anaesthetic_type_id, anaesthetic_delivery_id, anaesthetic_comment, anaesthetic_witness_id', 'safe'),
			array('anaesthetic_type_id', 'required'),
			array('anaesthetist_id, anaesthetic_delivery_id, anaesthetic_complications', 'validateAnaesthetic'),

			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, anaesthetist_id, anaesthetic_type_id, anaesthetic_delivery_id, anaesthetic_comment, anaesthetic_witness_id', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'anaesthetic_type' => array(self::BELONGS_TO, 'AnaestheticType', 'anaesthetic_type_id'),
			'anaesthetist' => array(self::BELONGS_TO, 'Anaesthetist', 'anaesthetist_id'),
			'anaesthetic_delivery' => array(self::BELONGS_TO, 'AnaestheticDelivery', 'anaesthetic_delivery_id'),
			'anaesthetic_agent_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_OperationAnaestheticAgent', 'et_ophtroperationnote_anaesthetic_id'),
			'anaesthetic_agents' => array(self::HAS_MANY, 'AnaestheticAgent', 'anaesthetic_agent_id',
				'through' => 'anaesthetic_agent_assignments'),
			'anaesthetic_complication_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_AnaestheticComplication', 'et_ophtroperationnote_anaesthetic_id'),
			'anaesthetic_complications' => array(self::HAS_MANY, 'OphTrOperationnote_AnaestheticComplications', 'anaesthetic_complication_id',
				'through' => 'anaesthetic_complication_assignments'),
			'witness' => array(self::BELONGS_TO, 'User', 'anaesthetic_witness_id'),
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
			'agents' => 'Agents',
			'anaesthetic_type_id' => 'Type',
			'anaesthetic_witness_id' => 'Witness',
			'anaesthetist_id' => 'Given by',
			'anaesthetic_delivery_id' => 'Delivery',
			'anaesthetic_comment' => 'Comments',
		);
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
		$criteria->compare('anaesthetic_type_id', $this->anaesthetic_type_id);
		$criteria->compare('anaesthetist_id', $this->anaesthetist_id);
		$criteria->compare('anaesthetic_type_id', $this->anaesthetic_type_id);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Should not display other anaesthetic details if the anaesthetic type is general
	 *
	 * @return bool
	 */
	public function getHidden()
	{
		return ($this->anaesthetic_type_id == 5);
	}

	/**
	 * Don't need a witness for anaesthetic unless it is administered by a nurse
	 *
	 * @return bool
	 */
	public function getWitness_hidden()
	{
		return (!$this->witness_enabled && ($this->anaesthetist_id != 3));
	}

	/**
	 * Need to delete associated records
	 * @see CActiveRecord::beforeDelete()
	 */
	protected function beforeDelete()
	{
		OphTrOperationnote_OperationAnaestheticAgent::model()->deleteAllByAttributes(array('et_ophtroperationnote_anaesthetic_id' => $this->id));
		OphTrOperationnote_AnaestheticComplication::model()->deleteAllByAttributes(array('et_ophtroperationnote_anaesthetic_id' => $this->id));
		return parent::beforeDelete();
	}

	/**
	 * Update the Anaesthetic Agents associated with the element
	 *
	 * @param $agent_ids
	 * @throws Exception
	 */
	public function updateAnaestheticAgents($agent_ids)
	{
		$curr_by_id = array();
		foreach ($this->anaesthetic_agent_assignments as $aa) {
			$curr_by_id[$aa->anaesthetic_agent_id] = $aa;
		}

		if(!empty($agent_ids)) {
			foreach ($agent_ids as $aa_id) {
				if (!isset($curr_by_id[$aa_id])) {
					$aa = new OphTrOperationnote_OperationAnaestheticAgent();
					$aa->et_ophtroperationnote_anaesthetic_id = $this->id;
					$aa->anaesthetic_agent_id = $aa_id;

					if (!$aa->save()) {
						throw new Exception('Unable to save anaesthetic agent assignment: ' . print_r($aa->getErrors(), true));
					}
				} else {
					unset($curr_by_id[$aa_id]);
				}
			}
		}
		foreach ($curr_by_id as $aa) {
			if (!$aa->delete()) {
				throw new Exception('Unable to delete anaesthetic agent assignment: '.print_r($aa->getErrors(), true));
			}
		}
	}

	/**
	 * Update the complications assigned to this element
	 *
	 * @param integer[] $complication_ids
	 * @throws Exception
	 */
	public function updateComplications($complication_ids)
	{
		$curr_by_id = array();

		foreach ($this->anaesthetic_complication_assignments as $ca) {
			$curr_by_id[$ca->anaesthetic_complication_id] = $ca;
		}

		if(!empty($complication_ids)) {
			foreach ($complication_ids as $c_id) {
				if (!isset($curr_by_id[$c_id])) {
					$ca = new OphTrOperationnote_AnaestheticComplication();
					$ca->et_ophtroperationnote_anaesthetic_id = $this->id;
					$ca->anaesthetic_complication_id = $c_id;

					if (!$ca->save()) {
						throw new Exception('Unable to save complication assignment: ' . print_r($ca->getErrors(), true));
					}
				} else {
					unset($curr_by_id[$c_id]);
				}
			}
		}

		foreach ($curr_by_id as $ca) {
			if (!$ca->delete()) {
				throw new Exception('Unable to delete complication assignment: '.print_r($ca->getErrors(), true));
			}
		}
	}

	// TODO: This should use the standard surgeons method
	public function getSurgeons()
	{
		if (!$this->surgeonlist) {
			$criteria = new CDbCriteria;
			$criteria->compare('active',true);
			$criteria->compare('is_doctor',1);
			$criteria->order = 'first_name,last_name asc';

			$this->surgeonlist = User::model()->findAll($criteria);
		}

		return $this->surgeonlist;
	}

	/**
	 * Validate the witness field if it's turned on
	 *
	 * @return bool
	 */
	public function beforeValidate()
	{
		if ($this->witness_enabled) {
			if ($this->anaesthetist_id == 3) {
				if (!$this->anaesthetic_witness_id) {
					$this->addError('anaesthetic_witness_id','Please select a witness');
				}
			}
		}

		return parent::beforeValidate();
	}

	/**
	 * Get ids of anaesthetic complications in use by the element
	 */
	public function getAnaestheticComplicationValues()
	{
		$complication_values = array();

		foreach ($this->anaesthetic_complication_assignments as $complication_assignment) {
			$complication_values[] = $complication_assignment->anaesthetic_complication_id;
		}

		return $complication_values = array();
	}

	public function validateAnaesthetic($attribute, $params)
	{

		$anaesthetics = Yii::app()->request->getPost('Element_OphTrOperationnote_Anaesthetic');
		if($anaesthetics['anaesthetic_type_id'] != 5)
		{
			$complications = Yii::app()->request->getPost('OphTrOperationnote_AnaestheticComplications');

			if($attribute == 'anaesthetist_id' && empty($anaesthetics['anaesthetist_id']))
			{
				$this->addError('anaesthetist_id', 'Anaesthetic Given by cannot be blank');
			}

			if($attribute == 'anaesthetic_delivery_id' && empty($anaesthetics['anaesthetic_delivery_id']))
			{
				$this->addError('anaesthetic_delivery_id', 'Anaesthetic Delivery cannot be blank');
			}

			if($attribute == 'anaesthetic_complications' && (!$complications || !count($complications))){
				$this->addError('anaesthetic_complications', 'Anaesthetic Complications cannot be blank.');
			}
		}
		else
		{

			$this->anaesthetist_id = 5;
			$this->anaesthetic_delivery_id = 7;
		}
	}
}
