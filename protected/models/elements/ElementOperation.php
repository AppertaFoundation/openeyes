<?php

/**
 * This is the model class for table "element_operation".
 *
 * The followings are the available columns in table 'element_operation':
 * @property string $id
 * @property string $event_id
 * @property integer $eye
 * @property string $comments
 * @property integer $total_duration
 * @property integer $consultant_required
 * @property integer $anaesthetist_required
 * @property integer $anaesthetic_type
 * @property integer $overnight_stay
 * @property integer $schedule_timeframe
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property Procedure[] $procedures
 */
class ElementOperation extends BaseElement
{
	const EYE_LEFT = 0;
	const EYE_RIGHT = 1;
	const EYE_BOTH = 2;
	
	const CONSULTANT_NOT_REQUIRED = 0;
	const CONSULTANT_REQUIRED = 1;
	
	const ANAESTHETIC_TOPICAL = 0;
	const ANAESTHETIC_LOCAL_WITH_COVER = 1;
	const ANAESTHETIC_LOCAL = 2;
	const ANAESTHETIC_LOCAL_WITH_SEDATION = 3;
	const ANAESTHETIC_GENERAL = 4;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return ElementOperation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'element_operation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('eye, total_duration, consultant_required, anaesthetist_required, anaesthetic_type, overnight_stay, schedule_timeframe', 'numerical', 'integerOnly'=>true),
			array('comments', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, eye, comments, total_duration, consultant_required, anaesthetist_required, anaesthetic_type, overnight_stay, schedule_timeframe', 'safe', 'on'=>'search'),
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
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'procedures' => array(self::MANY_MANY, 'Procedure', 'operation_procedure_assignment(operation_id, procedure_id)', 'order' => 'display_order ASC'),
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
			'eye' => 'Eye(s)',
			'comments' => 'Comments',
			'total_duration' => 'Total Duration',
			'consultant_required' => 'Consultant Required',
			'anaesthetist_required' => 'Anaesthetist Required',
			'anaesthetic_type' => 'Anaesthetic Type',
			'overnight_stay' => 'Overnight Stay',
			'schedule_timeframe' => 'Schedule Timeframe',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('event_id',$this->event_id,true);
		$criteria->compare('eye',$this->eye);
		$criteria->compare('comments',$this->comments,true);
		$criteria->compare('total_duration',$this->total_duration);
		$criteria->compare('consultant_required',$this->consultant_required);
		$criteria->compare('anaesthetist_required',$this->anaesthetist_required);
		$criteria->compare('anaesthetic_type',$this->anaesthetic_type);
		$criteria->compare('overnight_stay',$this->overnight_stay);
		$criteria->compare('schedule_timeframe',$this->schedule_timeframe);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Set default values for forms on create
	 */
	public function setDefaultOptions()
	{
		$this->consultant_required = self::CONSULTANT_REQUIRED;
		$this->anaesthetic_type = self::ANAESTHETIC_TOPICAL;
		$this->overnight_stay = 0;
		$this->total_duration = 0;
	}
	
	/**
	 * Return list of options for eye
	 * @return array 
	 */
	public function getEyeOptions()
	{
		return array(
			self::EYE_LEFT => 'Left',
			self::EYE_RIGHT => 'Right',
			self::EYE_BOTH => 'Both',
		);
	}
	
	public function getEyeText() {
		switch ($this->eye) {
			case self::EYE_LEFT:
				$text = 'Left';
				break;
			case self::EYE_RIGHT:
				$text = 'Right';
				break;
			case self::EYE_BOTH:
				$text = 'Both';
				break;
			default:
				$text = 'Unknown';
				break;
		}
		
		return $text;
	}
	
	/**
	 * Return list of options for consultant
	 * @return array 
	 */
	public function getConsultantOptions()
	{
		return array(
			self::CONSULTANT_REQUIRED => 'Yes',
			self::CONSULTANT_NOT_REQUIRED => 'No',
		);
	}
	
	public function getBooleanText($field) {
		switch ($this->$field) {
			case 1:
				$text = 'Yes';
				break;
			default:
				$text = 'No';
				break;
		}
		
		return $text;
	}
	
	/**
	 * Return list of options for anaesthetic type
	 * @return array 
	 */
	public function getAnaestheticOptions()
	{
		return array(
			self::ANAESTHETIC_TOPICAL => 'Topical',
			self::ANAESTHETIC_LOCAL => 'Local',
			self::ANAESTHETIC_LOCAL_WITH_COVER => 'Local with cover',
			self::ANAESTHETIC_LOCAL_WITH_SEDATION => 'Local with sedation',
			self::ANAESTHETIC_GENERAL => 'General'
		);
	}
	
	public function getAnaestheticText() {
		switch ($this->anaesthetic_type) {
			case self::ANAESTHETIC_TOPICAL:
				$text = 'Topical';
				break;
			case self::ANAESTHETIC_LOCAL:
				$text = 'Local';
				break;
			case self::ANAESTHETIC_LOCAL_WITH_COVER:
				$text = 'Local with cover';
				break;
			case self::ANAESTHETIC_LOCAL_WITH_SEDATION:
				$text = 'Local with sedation';
				break;
			case self::ANAESTHETIC_GENERAL:
				$text = 'General';
				break;
			default:
				$text = 'Unknown';
				break;
		}
		
		return $text;
	}
	
	/**
	 * Return list of options for overnight stay
	 * @return array 
	 */
	public function getOvernightOptions()
	{
		return array(
			1 => 'Yes',
			0 => 'No',
		);
	}
	
	protected function beforeSave()
	{
		$anaesthetistRequired = array(
			self::ANAESTHETIC_LOCAL_WITH_COVER, self::ANAESTHETIC_LOCAL_WITH_SEDATION,
			self::ANAESTHETIC_GENERAL
		);
		$this->anaesthetist_required = in_array($this->anaesthetic_type, $anaesthetistRequired);

		return parent::beforeSave();
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		$operationId = $this->id;
		// first wipe out any existing procedures so we start from scratch
		OperationProcedureAssignment::model()->deleteAll('operation_id = :id', 
			array(':id' => $operationId));
		
		$order = 1;
		
		if (!empty($_POST['Procedures'])) {
			foreach ($_POST['Procedures'] as $id) {
				$procedure = new OperationProcedureAssignment;
				$procedure->operation_id = $operationId;
				$procedure->procedure_id = $id;
				$procedure->display_order = $order;
				if (!$procedure->save()) {
					throw new Exception('Unable to save procedure');
				}

				$order++;
			}
		}
	}
}