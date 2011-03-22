<?php

/**
 * This is the model class for table "site_element_type".
 *
 * The followings are the available columns in table 'site_element_type':
 * @property string $id
 * @property string $possible_element_type_id
 * @property string $specialty_id
 * @property integer $required
 * @property integer $view_number
 * @property integer $first_in_episode
 *
 * The followings are the available model relations:
 * @property EventTypeElementTypeAssignment $eventTypeElementTypeAssignment
 * @property Specialty $specialty
 */
class SiteElementType extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return SiteElementType the static model class
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
		return 'site_element_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('possible_element_type_id, specialty_id', 'required'),
			array('first_in_episode', 'numerical', 'integerOnly'=>true),
			array('possible_element_type_id, specialty_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, possible_element_type_id, specialty_id, first_in_episode', 'safe', 'on'=>'search'),
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
			'possibleElementType' => array(self::BELONGS_TO, 'PossibleElementType', 'possible_element_type_id'),
			'specialty' => array(self::BELONGS_TO, 'Specialty', 'specialty_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'possible_element_type_id' => 'Event Type Element Type Assignment',
			'specialty_id' => 'Specialty',
			'first_in_episode' => 'First In Episode',
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
		$criteria->compare('possible_element_type_id',$this->possible_element_type_id,true);
		$criteria->compare('specialty_id',$this->specialty_id,true);
		$criteria->compare('first_in_episode',$this->first_in_episode);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns an array of siteElementType objects for a given element type and specialty, adhering to the constraints imposed by the possible_element_type table
	 * It optionally further limits it's results by what is relevant to a given episode based on the 'first in episode' logic
	 *
	 * @param int $eventTypeId
	 * @param object $firm
	 *
	 * @return array Object
	 */

	public function getAllPossible($eventTypeId, $specialtyId, $episodeId = null)
	{
		$criteria = new CDbCriteria;
		$criteria->join = 'LEFT JOIN possible_element_type possibleElementType
			ON t.possible_element_type_id = possibleElementType.id';
		$criteria->addCondition('t.specialty_id = :specialty_id');
		$criteria->addCondition('possibleElementType.event_type_id = :event_type_id');
		$criteria->order = 'possibleElementType.order';
		$criteria->params = array(
			':specialty_id' => $specialtyId,
			':event_type_id' => $eventTypeId
		);

		$siteElementTypeObjects = SiteElementType::model()->findAll($criteria);
		if (!is_int($episodeId)) {
			return $siteElementTypeObjects;
		} else {
			$eventType = EventType::model()->findByPk($eventTypeId);
			$dedupedElementTypeObjects = array();
			foreach ($siteElementTypeObjects as $siteElementTypeObject) {
				if ($eventType->first_in_episode_possible == false) {
					// Render everything;
					$dedupedElementTypeObjects[] = $siteElementTypeObject;
				} elseif ($episodeId > 0 && $episode->hasEventOfType($eventType->id)) {
					// event is not first of this event type for this episode
					// Render all where first_in_episode == false;
					if ($siteElementTypeObject->first_in_episode == false) {
						$dedupedElementTypeObjects[] = $siteElementTypeObject;
					}
				} elseif ($siteElementTypeObject->first_in_episode == true) {
					// Render all where first_in_episode == true;
					$dedupedElementTypeObjects[] = $siteElementTypeObject;
				}
			}
			return $dedupedElementTypeObjects;
		}
	}
}
