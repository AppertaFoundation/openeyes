<?php

use OEModule\OphCiExamination\models\HistoryMedicationsStopReason;

/**
 * This is the model class for table "event_medication_use".
 *
 * The followings are the available columns in table 'event_medication_use':
 * @property integer $id
 * @property string $event_id
 * @property string $copied_from_med_use_id
 * @property integer $first_prescribed_med_use_id
 * @property string $usage_type
 * @property string $usage_subtype
 * @property integer $medication_id
 * @property integer $form_id
 * @property integer $laterality
 * @property double $dose
 * @property string $dose_unit_term
 * @property integer $route_id
 * @property integer $frequency_id
 * @property integer $duration
 * @property integer $dispense_location_id
 * @property integer $dispense_condition_id
 * @property Date $start_date
 * @property Date $end_date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property int $stop_reason_id
 * @property int $prescription_item_id
 * @property string $binded_key
 *
 * The followings are the available model relations:
 * @property Event $copiedFromMedUse
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Event $event
 * @property MedicationForm $form
 * @property MedicationFrequency $frequency
 * @property Medication $medication
 * @property MedicationRoute $route
 * @property HistoryMedicationsStopReason $stopReason
 * @property EventMedicationUse $prescriptionItem
 * @property MedicationLaterality $medicationLaterality
 * @property DrugDuration $drugDuration
 */

class EventMedicationUse extends BaseElement
{
    /** This ID is used as medication_id when the user is adding a new medication using the adder dialog */
    const USER_MEDICATION_ID = -1;

    const USER_MEDICATION_SOURCE_TYPE = "LOCAL";
    const USER_MEDICATION_SOURCE_SUBTYPE = "UNMAPPED";

    /** @var bool Tracking variable used when creating/editing entries */
    public $originallyStopped = false;

    public $prescription_item_deleted = false;
    public $prescription_event_deleted = false;
    public $prescription_not_synced = false;

    public $is_copied_from_previous_event = false;

    /** @var bool Whether tapers can be added */
    public $taper_support = false;

    /* temporaryly saved properties to keep edit mode consistent through pages */
    public $group;
    public $chk_prescribe;
    public $chk_stop;
    public $medication_name;

    public function getOriginalAttributes()
    {
        return $this->originalAttributes;
    }

    public static function getUsageType()
    {
        return "OphCiExamination";
    }

    public static function getUsageSubtype()
    {
        return "History";
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'event_medication_use';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('usage_type, medication_id', 'required'),
			array('first_prescribed_med_use_id, medication_id, form_id, laterality, route_id, frequency_id, duration, dispense_location_id, dispense_condition_id, stop_reason_id, prescription_item_id, prescribe, hidden', 'numerical', 'integerOnly'=>true),
			array('dose', 'numerical'),
			array('laterality', 'validateLaterality'),
			array('event_id, copied_from_med_use_id, last_modified_user_id, created_user_id, binded_key', 'length', 'max'=>10),
			array('usage_type, usage_subtype, dose_unit_term', 'length', 'max'=>45),
			array('dose_unit_term', 'validateDoseUnitTerm'),
			array('usage_type', 'default', 'value' => static::getUsageType(), 'on' => 'insert'),
			array('usage_subtype', 'default', 'value' => static::getUsageSubType(), 'on' => 'insert'),
			array('end_date', 'OEFuzzyDateValidator'),
			array('start_date', 'OEFuzzyDateValidatorNotFuture'),
			array('last_modified_date, created_date, event_id', 'safe'),
			array('dose, route_id, frequency_id, dispense_location_id, dispense_condition_id, duration', 'required', 'on' => 'to_be_prescribed'),
            array('stop_reason_id', 'default', 'setOnEmpty' => true, 'value' => null),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, event_id, copied_from_med_use_id, first_prescribed_med_use_id, usage_type, usage_subtype, medication_id, form_id, laterality, dose, dose_unit_term, route_id, frequency_id, duration, dispense_location_id, dispense_condition_id, start_date, end_date, last_modified_user_id, last_modified_date, created_user_id, created_date, binded_key', 'safe', 'on'=>'search'),
		);
	}

    /**
     * require laterality selection when a route is chosen that has laterality options
     */
    public function validateLaterality()
    {
        if (!$this->laterality && $this->route_id && $this->route->has_laterality === "1") {
            $this->addError('option_id', "You must specify laterality for route '{$this->route->term}'");
        }
    }

	/**
	 * Dose unit is required only if the dose is set
	 */

	public function validateDoseUnitTerm()
	{
		if(!$this->hidden && $this->dose_unit_term == "" && $this->dose != "") {
			$this->addError("dose_unit_term", "You must select a dose unit if the dose is set.");
		}
    }

    /**
     * @inheritdoc
     */

    public function copiedFields()
    {
        return ['usage_type', 'usage_subtype', 'medication_id', 'start_date', 'end_date', 'first_prescribed_med_use_id',
                'form_id', 'laterality', 'route_id', 'frequency_id', 'duration', 'dispense_location_id', 'dispense_condition_id', 'stop_reason_id', 'prescription_item_id',
                'dose', 'copied_from_med_use_id', 'dose_unit_term'];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'copiedFromMedUse' => array(self::BELONGS_TO, 'Event', 'copied_from_med_use_id'),
			'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'form' => array(self::BELONGS_TO, MedicationForm::class, 'form_id'),
			'frequency' => array(self::BELONGS_TO, MedicationFrequency::class, 'frequency_id'),
			'medication' => array(self::BELONGS_TO, Medication::class, 'medication_id'),
			'route' => array(self::BELONGS_TO, MedicationRoute::class, 'route_id'),
			'stopReason' => array(self::BELONGS_TO, HistoryMedicationsStopReason::class, 'stop_reason_id'),
			'prescriptionItem' => array(self::BELONGS_TO, OphDrPrescription_Item::class, 'prescription_item_id'),
			'medicationLaterality' => array(self::BELONGS_TO, MedicationLaterality::class, 'laterality'),
			'drugDuration' => array(self::BELONGS_TO, DrugDuration::class, 'duration'),
			'dispenseLocation' => array(self::BELONGS_TO, OphDrPrescription_DispenseLocation::class, 'dispense_location_id'),
			'dispenseCondition' => array(self::BELONGS_TO, OphDrPrescription_DispenseCondition::class, 'dispense_condition_id'),
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
			'copied_from_med_use_id' => 'Copied From Med Use',
			'first_prescribed_med_use_id' => 'First Prescribed Med Use',
			'usage_type' => 'Usage Type',
			'usage_subtype' => 'Usage Subtype',
			'medication_id' => 'Medication',
			'form_id' => 'Form',
			'laterality' => 'Laterality',
			'dose' => 'Dose',
			'dose_unit_term' => 'Dose Unit Term',
			'route_id' => 'Route',
			'frequency_id' => 'Frequency',
			'duration' => 'Duration',
			'dispense_location_id' => 'Dispense Location',
			'dispense_condition_id' => 'Dispense Condition',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'last_modified_user_id' => 'Last Modified User',
			'last_modified_date' => 'Last Modified Date',
			'created_user_id' => 'Created User',
			'created_date' => 'Created Date',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('event_id',$this->event_id,true);
		$criteria->compare('copied_from_med_use_id',$this->copied_from_med_use_id,true);
		$criteria->compare('first_prescribed_med_use_id',$this->first_prescribed_med_use_id);
		$criteria->compare('usage_type',$this->usage_type,true);
		$criteria->compare('usage_subtype',$this->usage_subtype,true);
		$criteria->compare('medication_id',$this->medication_id);
		$criteria->compare('form_id',$this->form_id);
		$criteria->compare('laterality',$this->laterality);
		$criteria->compare('dose',$this->dose);
		$criteria->compare('dose_unit_term',$this->dose_unit_term,true);
		$criteria->compare('route_id',$this->route_id);
		$criteria->compare('frequency_id',$this->frequency_id);
		$criteria->compare('duration',$this->duration);
		$criteria->compare('dispense_location_id',$this->dispense_location_id);
		$criteria->compare('dispense_condition_id',$this->dispense_condition_id);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('last_modified_user_id',$this->last_modified_user_id,true);
		$criteria->compare('last_modified_date',$this->last_modified_date,true);
		$criteria->compare('created_user_id',$this->created_user_id,true);
		$criteria->compare('created_date',$this->created_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EventMedicationUse the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function afterValidate()
	{
		if ($this->start_date && $this->end_date &&
			$this->start_date > $this->end_date) {
			$this->addError('end_date', 'Stop date must be on or after start date');
		}
		parent::afterValidate();
	}

    /**
     * @return bool
     */
    public function prescriptionNotCurrent()
    {
        return ($this->prescription_item_id
            && ($this->prescription_item_deleted
                || $this->prescription_not_synced
                || $this->prescription_event_deleted));
    }

    public function getMedicationDisplay($short = false)
    {
        if(!isset($this->medication)) {
            return "";
        }
        else {
            return $this->medication->getLabel($short);
        }
    }

    public function routeOptions()
    {
        return (!is_null($this->route) && $this->route->has_laterality == 1) ? MedicationLaterality::model()->findAll("deleted_date IS NULL") : array();
    }

    public function getLateralityDisplay()
    {
        $lname = $this->medicationLaterality ? $this->medicationLaterality->name : '';
        switch (strtolower($lname)) {
            case 'left':
                return 'L';
            case 'right':
                return 'R';
            case 'both':
                return 'B';
            default:
                return '';
        }
    }

    public function getDatesDisplay()
    {
        $res = array();
        if ($this->start_date) {
            $res[] = \Helper::formatFuzzyDate($this->start_date);
        }
        if ($this->end_date) {
            if (count($res)) {
                $res[] = '-';
            }
            $res[] = \Helper::formatFuzzyDate($this->end_date);
        }
        if ($this->stop_reason_id) {
            $res[] = "({$this->stopReason->name})";
        }
        return implode(' ', $res);
    }

    public function getAdministrationDisplay()
    {
        $res = array();
        foreach (array('dose', 'dose_unit_term', 'medicationLaterality', 'route', 'frequency') as $k) {
            if ($this->$k) {
            		if($k !== "dose_unit_term" || $this->dose) {
									$res[] = $this->$k;
								}
            }
        }
        return implode(' ', $res);
    }

    /**
     * @return bool
     */

    public function hasRisk()
    {
        if ($this->medication) {
            return count(OEModule\OphCiExamination\models\OphCiExaminationRisk::findForMedicationSetIds(array_map(
                    function($t) {
                        return $t->id;
                    }, $this->medication->medicationSets
                ))) > 0;
        } else {
            return false;
        }
    }

    public function getStartDateDisplay()
    {
        if ($this->start_date) {
            return \Helper::formatFuzzyDate($this->start_date);
        }
        else {
            return "";
        }
    }

    public function getStopDateDisplay()
    {
        return '<div class="oe-date">' . \Helper::convertFuzzyDate2HTML($this->end_date) . '</div>';
    }

    public function getEndDateDisplay($default = "")
    {
        if ($this->end_date) {
            return \Helper::formatFuzzyDate($this->end_date);
        } else {
            return $default;
        }
    }

    public function getDoseAndFrequency()
    {
        $result = [];

        if($this->dose){
            if($this->dose_unit_term) {
                $result[] = $this->dose . ' ' . $this->dose_unit_term;
            } else {
                $result[] = $this->dose;
            }
        }

        if($this->frequency){
            $result[] = $this->frequency;
        }

        return implode(' , ', $result    );
    }

    public function getChk_prescribe()
    {
        return $this->chk_prescribe;
    }

    public function setChk_prescribe( $prescribe)
    {
        $this->chk_prescribe = $prescribe;
    }


    public function loadFromExisting($element)
    {
        parent::loadFromExisting($element);
        $this->updateStateProperties();
        $this->is_copied_from_previous_event = true;
    }

    /**
     * Abstraction to set up the entry state based on its current attributes
     */
    protected function updateStateProperties()
    {
        if ($this->end_date !== null && $this->end_date < date('Y-m-d')) {
            $this->originallyStopped = true;
        }
        /* TODO Check what was happening here previously
        if ($this->prescription_item_id) {
            $this->initialiseFromPrescriptionItem();
        }
        */
    }

    /**
     * @inheritdoc
     */
    protected function afterFind()
    {
        parent::afterFind();
        $this->updateStateProperties();
    }

    public function loadFromPrescriptionItem($item)
    {
        $this->prescription_item_id = $item->id;
        $this->prescriptionItem = $item;
        $this->initialiseFromPrescriptionItem();
    }

    /**
     * When an entry is related to a prescription item, it's attributes should match,
     * and if not we need to set flags on it so that the user can be alerted as
     * appropriate.
     */
    protected function initialiseFromPrescriptionItem()
    {
        if (!$item = $this->prescriptionItem) {
            $this->prescription_item_deleted = true;
            $this->prescription_not_synced = true;
            return;
        }

        if (!$item->event) {
            // default scope on the event will mean event relation is null if it's been deleted
            $this->prescription_event_deleted = true;
            return;
        }

        if ($this->isNewRecord) {
            // must be creating a new 'shadow' record so we default everything from the prescription item
            $this->cloneFromPrescriptionItem($item);
        } else {
            // need to check if the prescription item still has the same values
            foreach (array('medication_id', 'dose', 'route_id', 'frequency_id') as $attr) {
                if ($this->$attr != $item->$attr) {
                    $this->prescription_not_synced = true;
                    break;
                }
            }

            if ($this->laterality !== $item->laterality) {
                $this->prescription_not_synced = true;
            }
        }
    }

    /**
    * Set all the appropriate attributes on this Entry to those on the given
    * prescription item.
    *
    * @param $item
    */

    private function clonefromPrescriptionItem($item)
    {
        $attrs = ['medication_id', 'medication', 'route_id', 'route', 'laterality', 'medicationLaterality',
                  'dose','dose_unit_term', 'frequency_id', 'frequency', 'start_date'];
        foreach ($attrs as $attr) {
            $this->$attr = $item->$attr;
        }
        if (!$this->end_date) {
            $end_date = $item->end_date;
            $compare_date = new \DateTime();

            if ($this->event && $this->event->event_date) {
                $compare_date = \DateTime::createFromFormat('Y-m-d', $this->event->event_date);
            }
            if ($end_date && $end_date < $compare_date) {
                $this->originallyStopped = true;
                $this->end_date = $end_date;
            }
        }
    }

    public function beforeValidate()
    {
        if($this->medication_id == self::USER_MEDICATION_ID) {
            $medication = new Medication();
            $medication->preferred_term = $this->medication_name;
            $medication->short_term = $this->medication_name;
            $medication->source_type = self::USER_MEDICATION_SOURCE_TYPE;
            $medication->source_subtype = self::USER_MEDICATION_SOURCE_SUBTYPE;
            $medication->preferred_code = self::USER_MEDICATION_SOURCE_SUBTYPE;
            if($medication->save()) {
                $medication->addDefaultSearchIndex();
                $this->medication_id = $medication->id;
            }
            else {
                $this->addError("medication_id", "There has been an error while saving the new medication '".$this->medication_name."'");
            }
        }

        return parent::beforeValidate();
    }
}
