<?php

/**
 * This is the model class for table "event_medication_uses".
 *
 * The followings are the available columns in table 'event_medication_uses':
 * @property integer $id
 * @property string $event_id
 * @property string $copied_from_med_use_id
 * @property integer $first_prescribed_med_use_id
 * @property string $usage_type
 * @property string $usage_subtype
 * @property integer $ref_medication_id
 * @property integer $form_id
 * @property integer $laterality
 * @property double $dose
 * @property string $dose_unit_term
 * @property integer $route_id
 * @property integer $frequency_id
 * @property integer $duration
 * @property integer $dispense_location_id
 * @property integer $dispense_condition_id
 * @property string $start_date_string_YYYYMMDD
 * @property string $end_date_string_YYYYMMDD
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property int $stop_reason_id
 * @property int $prescription_item_id
 *
 * The followings are the available model relations:
 * @property Event $copiedFromMedUse
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Event $event
 * @property RefMedicationForm $form
 * @property RefMedicationFrequency $frequency
 * @property RefMedication $refMedication
 * @property RefMedicationRoute $route
 * @property HistoryMedicationsStopReason $stopReason
 * @property EventMedicationUse $prescriptionItem
 * @property RefMedicationLaterality $refMedicationLaterality
 */

use \OEModule\OphCiExamination\models\HistoryMedicationsStopReason;

class EventMedicationUse extends BaseElement
{
    /** @var bool Tracking variable used when creating/editing entries */
    public $originallyStopped = false;

    // TODO implement correct initialization of these properties

    public $prescription_item_deleted = false;
    public $prescription_event_deleted = false;
    public $prescription_not_synced = false;

    public $is_copied_from_previous_event = false;

    /* temporaryly saved properties to keep edit mode consistent through pages */
    public $group;
    public $chk_continue;
    public $chk_prescribe;
    public $chk_stop;

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
		return 'event_medication_uses';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('usage_type, ref_medication_id, start_date_string_YYYYMMDD', 'required'),
			array('first_prescribed_med_use_id, ref_medication_id, form_id, laterality, route_id, frequency_id, duration, dispense_location_id, dispense_condition_id, stop_reason_id, prescription_item_id, continue, prescribe', 'numerical', 'integerOnly'=>true),
			array('dose', 'numerical'),
			array('event_id, copied_from_med_use_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('usage_type, usage_subtype, dose_unit_term', 'length', 'max'=>45),
			array('usage_type', 'default', 'value' => static::getUsageType(), 'on' => 'insert'),
			array('usage_subtype', 'default', 'value' => static::getUsageSubType(), 'on' => 'insert'),
			array('start_date_string_YYYYMMDD, end_date_string_YYYYMMDD', 'length', 'max'=>8),
			array('last_modified_date, created_date, event_id', 'safe'),
			array('dose, route_id, frequency_id', 'required', 'on' => 'to_be_prescribed'),
            array('stop_reason_id', 'default', 'setOnEmpty' => true, 'value' => null),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, event_id, copied_from_med_use_id, first_prescribed_med_use_id, usage_type, usage_subtype, ref_medication_id, form_id, laterality, dose, dose_unit_term, route_id, frequency_id, duration, dispense_location_id, dispense_condition_id, start_date_string_YYYYMMDD, end_date_string_YYYYMMDD, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
		);
	}

    /**
     * @inheritdoc
     */

    public function copiedFields()
    {
        return ['usage_type', 'usage_subtype', 'ref_medication_id', 'start_date_string_YYYYMMDD', 'end_date_string_YYYYMMDD', 'first_prescribed_med_use_id',
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
			'form' => array(self::BELONGS_TO, 'RefMedicationForm', 'form_id'),
			'frequency' => array(self::BELONGS_TO, 'RefMedicationFrequency', 'frequency_id'),
			'refMedication' => array(self::BELONGS_TO, 'RefMedication', 'ref_medication_id'),
			'route' => array(self::BELONGS_TO, 'RefMedicationRoute', 'route_id'),
            'stopReason' => array(self::BELONGS_TO, HistoryMedicationsStopReason::class, 'stop_reason_id'),
            'prescriptionItem' => array(self::BELONGS_TO, self::class, 'prescription_item_id'),
            'refMedicationLaterality' => array(self::BELONGS_TO, RefMedicationLaterality::class, 'laterality')
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
			'ref_medication_id' => 'Ref Medication',
			'form_id' => 'Form',
			'laterality' => 'Laterality',
			'dose' => 'Dose',
			'dose_unit_term' => 'Dose Unit Term',
			'route_id' => 'Route',
			'frequency_id' => 'Frequency',
			'duration' => 'Duration',
			'dispense_location_id' => 'Dispense Location',
			'dispense_condition_id' => 'Dispense Condition',
			'start_date_string_YYYYMMDD' => 'Start Date',
			'end_date_string_YYYYMMDD' => 'End Date',
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
		$criteria->compare('ref_medication_id',$this->ref_medication_id);
		$criteria->compare('form_id',$this->form_id);
		$criteria->compare('laterality',$this->laterality);
		$criteria->compare('dose',$this->dose);
		$criteria->compare('dose_unit_term',$this->dose_unit_term,true);
		$criteria->compare('route_id',$this->route_id);
		$criteria->compare('frequency_id',$this->frequency_id);
		$criteria->compare('duration',$this->duration);
		$criteria->compare('dispense_location_id',$this->dispense_location_id);
		$criteria->compare('dispense_condition_id',$this->dispense_condition_id);
		$criteria->compare('start_date_string_YYYYMMDD',$this->start_date_string_YYYYMMDD,true);
		$criteria->compare('end_date_string_YYYYMMDD',$this->end_date_string_YYYYMMDD,true);
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
        return $this->ref_medication_id ? ($short ? $this->refMedication->short_term : $this->refMedication->preferred_term) : '';
    }

    public function routeOptions()
    {
        return RefMedicationLaterality::model()->findAll();
    }

    public function getLateralityDisplay()
    {
        $lname = $this->refMedicationLaterality ? $this->refMedicationLaterality->name : '';
        switch (strtolower($lname)) {
            case 'left':
                return 'L';
            case 'right':
                return 'R';
            case 'both':
                return 'B';
            default:
                return '?';
        }
    }

    public function getDatesDisplay()
    {
        $res = array();
        if ($this->start_date_string_YYYYMMDD) {
            $res[] = \Helper::formatFuzzyDate($this->start_date);
        }
        if ($this->end_date_string_YYYYMMDD) {
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
        foreach (array('dose', 'dose_unit_term', 'refMedicationLaterality', 'route', 'frequency') as $k) {
            if ($this->$k) {
                $res[] = $this->$k;
            }
        }
        return implode(' ', $res);
    }

    /**
     * @return bool
     */

    public function hasRisk()
    {
        // TODO Rewrite once tags support is implemented for RefMediaction
        /*
        $med = $this->drug ? : $this->medication_drug ? : null;

        if ($med) {
            return count(OphCiExaminationRisk::findForTagIds(array_map(
                    function($t) {
                        return $t->id;
                    }, $med->tags
                ))) > 0;
        } else {
            return false;
        }*/
        return false;
    }

    /**
     * Getter to make this class compatible with HistoryMedicationsEntry
     *
     * @return null|string
     */

    public function getStart_date()
    {
        return is_null($this->start_date_string_YYYYMMDD) ? null : substr($this->start_date_string_YYYYMMDD, 0, 4).'-'
                .substr($this->start_date_string_YYYYMMDD, 4, 2).'-'
                .substr($this->start_date_string_YYYYMMDD, 6, 2);
    }

    public function getStartDateDisplay()
    {
        $res = array();
        if ($this->start_date) {
            $res[] = \Helper::formatFuzzyDate($this->start_date);
        }
        return implode(' ', $res);
    }

    /**
     * Getter to make this class compatible with HistoryMedicationsEntry
     *
     * @return null|string
     */

    public function getEnd_date()
    {
        return is_null($this->end_date_string_YYYYMMDD) ? null : substr($this->end_date_string_YYYYMMDD, 0, 4).'-'
            .substr($this->end_date_string_YYYYMMDD, 4, 2).'-'
            .substr($this->end_date_string_YYYYMMDD, 6, 2);
    }

    /**
     * Setter to make this class compatible with HistoryMedicationsEntry
     *
     * @return $this
     */

    public function setStart_date($date)
    {
        $this->start_date_string_YYYYMMDD = is_null($date) ? null : str_replace('-', '', $date);
        return $this;
    }

    /**
     * Setter to make this class compatible with HistoryMedicationsEntry
     *
     * @return $this
     */

    public function setEnd_date($date)
    {
        $this->end_date_string_YYYYMMDD = is_null($date) ? null : str_replace('-', '', $date);
        return $this;
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
            foreach (array('drug_id', 'dose', 'route_id', 'frequency_id') as $attr) {
                if ($this->$attr != $item->$attr) {
                    $this->prescription_not_synced = true;
                    break;
                }
            }
            // TODO: resolve the disparity in attribute names here
            if ($this->laterality !== $item->route_option_id) {
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
        $attrs = ['ref_medication_id', 'refMedication', 'route_id', 'route', 'laterality', 'refMedicationLaterality',
                  'dose', 'frequency_id', 'frequency', 'start_date_string_YYYYMMDD'];
        foreach ($attrs as $attr) {
            $this->$attr = $item->$attr;
        }
        if (!$this->end_date_string_YYYYMMDD) {
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



}
