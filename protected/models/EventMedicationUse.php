<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\HistoryMedicationsStopReason;

/**
 * This is the model class for table "event_medication_use".
 *
 * The followings are the available columns in table 'event_medication_use':
 * @property integer $id
 * @property string $event_id
 * @property string $copied_from_med_use_id
 * @property integer $latest_prescribed_med_use_id
 * @property string $usage_type
 * @property string $usage_subtype
 * @property integer $medication_id
 * @property integer $form_id
 * @property integer $laterality
 * @property double $dose
 * @property string $dose_unit_term
 * @property integer $route_id
 * @property integer $frequency_id
 * @property integer $duration_id
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
 * @property string $bound_key
 * @property string $comments
 * @property int $latest_med_use_id
 * @property int $stopped_in_event_id
 * @property bool $is_discontinued
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
 * @property MedicationDuration $medicationDuration
 */
class EventMedicationUse extends BaseElement
{
    /** This ID is used as medication_id when the user is adding a new medication using the adder dialog */
    const USER_MEDICATION_ID = -1;

    const USER_MEDICATION_SOURCE_TYPE = "LOCAL";
    const USER_MEDICATION_SOURCE_SUBTYPE = "UNMAPPED";

    /** @var bool Used to change default behaviour when converting old drugs to DMD */
    public static $local_to_dmd_conversion = false;
    private static $other_stop_reason = null;

    /** @var bool Tracking variable used when creating/editing entries */
    public $originallyStopped = false;

    public $prescription_item_deleted = false;
    public $prescription_event_deleted = false;
    public $prescription_not_synced = false;

    public $is_copied_from_previous_event = false;

    /** @var bool Whether tapers can be added */
    public $taper_support = false;

    /* temporarily saved properties to keep edit mode consistent through pages */
    public $group;
    public $chk_prescribe;
    public $chk_stop;
    public $medication_name;

    public $equals_attributes = [
        'medication_id', 'dose', 'dose_unit_term', 'route_id', 'frequency_id', 'start_date', 'laterality',
    ];
    public $taper_equals_attributes = ['dose', 'frequency_id'];

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string|null $className
     * @return BaseElement|mixed
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getOriginalAttributes()
    {
        return $this->originalAttributes;
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
            array('latest_prescribed_med_use_id, medication_id, form_id, laterality, route_id, frequency_id, duration_id, dispense_location_id, dispense_condition_id, stop_reason_id, 
			        prescription_item_id, prescribe, hidden, is_discontinued', 'numerical', 'integerOnly' => true),
            array('dose', 'numerical'),
            array('laterality', 'validateLaterality'),
            array('event_id, copied_from_med_use_id, last_modified_user_id, created_user_id, bound_key', 'length', 'max' => 10),
            array('usage_type, usage_subtype, dose_unit_term', 'length', 'max' => 45),
            array('dose_unit_term', 'validateDoseUnitTerm'),
            array('usage_type', 'default', 'value' => static::getUsageType(), 'on' => 'insert'),
            array('usage_subtype', 'default', 'value' => static::getUsageSubType(), 'on' => 'insert'),
            array('end_date', 'OEFuzzyDateValidator'),
            array('end_date', 'validateEndDateStopReason'),
            array('start_date', 'OEFuzzyDateValidatorNotFuture'),
            array('start_date', 'default', 'value' => '0000-00-00'),
            array('last_modified_date, created_date, event_id, comments, latest_med_use_id, stopped_in_event_id', 'safe'),
            array('dose, route_id, frequency_id, dispense_location_id, dispense_condition_id, duration_id', 'required', 'on' => 'to_be_prescribed'),
            array('stop_reason_id', 'default', 'setOnEmpty' => true, 'value' => null),
            array('stopped_in_event_id', 'default', 'setOnEmpty' => true, 'value' => null),
            array('stop_reason_id', 'validateStopReason'),
            array(
                'id, event_id, copied_from_med_use_id, latest_prescribed_med_use_id, usage_type, usage_subtype, 
                    medication_id, form_id, laterality, dose, dose_unit_term, route_id, frequency_id, duration, 
                    dispense_location_id, dispense_condition_id, start_date, end_date, last_modified_user_id, 
                    last_modified_date, created_user_id, created_date, bound_key, latest_med_use_id', 'safe', 'on' => 'search'
            ),
        );
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
     * require laterality selection when a route is chosen that has laterality options
     */
    public function validateLaterality()
    {
        if (!$this->laterality && $this->route_id && $this->route->has_laterality === "1") {
            $this->addError('laterality', "You must specify laterality for route '{$this->route->term}'");
        }
    }

    public function validateDuration()
    {
        if ($this->tapers) {
            $on_going_duration = MedicationDuration::model()->findByAttributes(['name' => 'Ongoing']);
            if ($this->duration_id === $on_going_duration->id) {
                $this->addError('duration_id', 'Ongoing cannot be set to this medication when tapers are added');
            }
            foreach ($this->tapers as $key => $taper) {
                $is_last_taper = ($key + 1) === count($this->tapers);
                if ($taper->duration_id === $on_going_duration->id && !$is_last_taper) {
                    $this->addError("taper_{$key}_duration_id", 'Ongoing can only be set for the last taper');
                }
            }
        }
    }

    /**
     * Dose unit is required only if the dose is set
     */

    public function validateDoseUnitTerm()
    {
        if (
            !$this->hidden && $this->dose_unit_term == "" && !empty($this->dose)
            && !($this->getUsageSubtype() == "History" && $this->prescription_item_id)
        ) {
            $this->addError("dose_unit_term", "You must select a dose unit if the dose is set.");
        }
    }

    public function validateStopReason()
    {
        if ($this->end_date && !$this->stop_reason_id) {
            $this->addError("stop_reason_id", "You must select a stop reason if the medication is stopped.");
        }
    }

    public function validateEndDateStopReason()
    {
        if (!$this->end_date && $this->stop_reason_id) {
            $this->addError('end_date', "You must set an end date if a stop reason is selected.");
        }
    }

    /**
     * @inheritdoc
     */

    public function copiedFields()
    {
        return [
            'usage_type', 'usage_subtype', 'medication_id', 'start_date', 'end_date', 'latest_prescribed_med_use_id',
            'form_id', 'laterality', 'route_id', 'frequency_id', 'duration_id', 'dispense_location_id', 'dispense_condition_id', 'stop_reason_id', 'prescription_item_id',
            'dose', 'copied_from_med_use_id', 'dose_unit_term', 'bound_key', 'comments'
        ];
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
            'medicationDuration' => array(self::BELONGS_TO, MedicationDuration::class, 'duration_id'),
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
            'latest_prescribed_med_use_id' => 'Latest Prescribed Med Use',
            'usage_type' => 'Usage Type',
            'usage_subtype' => 'Usage Subtype',
            'medication_id' => 'Medication',
            'form_id' => 'Form',
            'laterality' => 'Laterality',
            'dose' => 'Dose',
            'dose_unit_term' => 'Dose Unit Term',
            'route_id' => 'Route',
            'frequency_id' => 'Frequency',
            'duration_id' => 'Duration',
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

        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('copied_from_med_use_id', $this->copied_from_med_use_id, true);
        $criteria->compare('latest_prescribed_med_use_id', $this->latest_prescribed_med_use_id);
        $criteria->compare('usage_type', $this->usage_type, true);
        $criteria->compare('usage_subtype', $this->usage_subtype, true);
        $criteria->compare('medication_id', $this->medication_id);
        $criteria->compare('form_id', $this->form_id);
        $criteria->compare('laterality', $this->laterality);
        $criteria->compare('dose', $this->dose);
        $criteria->compare('dose_unit_term', $this->dose_unit_term, true);
        $criteria->compare('route_id', $this->route_id);
        $criteria->compare('frequency_id', $this->frequency_id);
        $criteria->compare('duration_id', $this->duration_id);
        $criteria->compare('dispense_location_id', $this->dispense_location_id);
        $criteria->compare('dispense_condition_id', $this->dispense_condition_id);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);
        $criteria->compare('latest_med_use_id', $this->latest_med_use_id);
        $criteria->compare('stopped_in_event_id', $this->stopped_in_event_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function afterValidate()
    {
        if (
            $this->start_date && $this->end_date &&
            $this->start_date > $this->end_date
        ) {
            $this->addError('end_date', 'Stop date must be on or after start date');
        }

        $this->updateStateProperties();
        if ($this->copied_from_med_use_id && $this->copied_from_med_use_id !== '0' && !$this->prescription_item_id) {
            $this->is_copied_from_previous_event = true;
        }

        if ($this->isStopped() && empty($this->stopped_in_event_id)) {
            $this->stopped_in_event_id = $this->event_id;
        }
        parent::afterValidate();
    }

    /**
     * @param EventMedicationUse $medication
     * @param bool $check_laterality
     * @param bool $check_start_date
     * @return bool
     */
    public function isEqualsAttributes($medication, $check_laterality, $check_start_date = true)
    {
        $result = true;

        foreach ($this->equals_attributes as $attribute) {
            //this is required for edit mode: the "undated" posted entries will have date="00-00-00" while the new ones date=""
            if ($attribute === "start_date") {
                if ($check_start_date) {
                    $date1 = $this->isUndated() ? "0000-00-00" : $this->start_date;
                    $date2 = $medication->isUndated() ? "0000-00-00" : $medication->start_date;

                    $result = $date1 === $date2;
                }
            } elseif ($attribute === "laterality") {
                if ($check_laterality && $this->route && $this->route->isEyeRoute()) {
                    $result = $this->$attribute === $medication->$attribute;
                }
            } elseif (empty($this->$attribute) && empty($medication->$attribute)) {
                $result = true;
            } else {
                $result = $this->$attribute === $medication->$attribute;
            }

            if (!$result) {
                return $result;
            }
        }

        if (($this->prescribe || $this->isPrescription())) {
            foreach ($this->tapers as $taper) {
                foreach ($this->taper_equals_attributes as $attribute) {
                    $result = $taper->$attribute === $medication->$attribute;

                    if (!$result) {
                        return $result;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param $medication
     * @return bool
     */
    public function isDuplicate($medication): bool
    {
        $result = false;

        if ($medication->medication_id === $this->medication_id) {
            if ($this->route_id === $medication->route_id && $this->route && $this->route->has_laterality) {
                if ($this->laterality === $medication->laterality || $this->laterality === (string)Eye::BOTH || $medication->laterality === (string)Eye::BOTH) {
                    $result = true;
                }
            } elseif ($this->route_id === $medication->route_id) {
                $result = true;
            }
        }

        return $result;
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
        if (!isset($this->medication)) {
            return "";
        } else {
            return $this->medication->getLabel($short);
        }
    }

    public function routeOptions()
    {
        return (!is_null($this->route) && $this->route->has_laterality == 1) ? MedicationLaterality::model()->findAll("deleted_date IS NULL") : array();
    }

    /**
     * long returns full Left/Right/Both when true, else L/R/B
     *
     * @param $long
     * @return string
     */
    public function getLateralityDisplay(bool $long = false): string
    {
        $lname = $this->medicationLaterality ? $this->medicationLaterality->name : '';

        if (!$long) {
            switch (strtolower($lname)) {
                case 'left':
                    $lname = 'L';
                    break;
                case 'right':
                    $lname = 'R';
                    break;
                case 'both':
                    $lname = 'B';
                    break;
                default:
                    $lname = '';
            }
        }

        return $lname;
    }

    public function getDatesDisplay()
    {
        $res = array();
        if ($this->start_date) {
            $res[] = Helper::formatFuzzyDate($this->start_date);
        }
        if ($this->end_date) {
            if (count($res)) {
                $res[] = '-';
            }
            $res[] = Helper::formatFuzzyDate($this->end_date);
        }
        if ($this->stop_reason_id) {
            $res[] = "({$this->stopReason->name})";
        }
        return implode(' ', $res);
    }

    /**
     * @param include_route if true will include laterality and route in output (e.g., Right Eye)
     * @return string
     */
    public function getAdministrationDisplay(bool $include_route = false)
    {
        $parts = array('dose', 'dose_unit_term');

        if ($include_route) {
            array_push($parts, 'medicationLaterality', 'route');
        }

        array_push($parts, 'frequency');

        $res = array();

        foreach ($parts as $k) {
            if ($this->$k) {
                if ($k === 'route') {
                    $both_laterality = MedicationLaterality::model()->findByPk(MedicationLaterality::BOTH);
                    $this->$k->term = ($both_laterality && $this->medicationLaterality && $this->medicationLaterality->name === $both_laterality->name) ? $this->$k->term . 's' : $this->$k->term;
                }
                if ($k !== "dose_unit_term" || $this->dose) {
                    $res[] = $this->$k;
                }
            }
        }
        return implode(' ', $res);
    }

    public function getRouteDisplay()
    {
        return $this->route ?? '';
    }

    /**
     * @throws Exception
     * */
    public function saveTapers()
    {
        // delete existing tapers
        OphDrPrescription_ItemTaper::model()->deleteAllByAttributes(['item_id' => $this->id]);

        // add new ones
        foreach ($this->tapers as $taper) {
            $taper->item_id = $this->id;
            if (!$taper->save()) {
                foreach ($taper->getErrors() as $err) {
                    $this->addError("tapers", $err);
                }
                return false;
            }
        }

        return true;
    }

    /**
     * Returns if medication is stopped
     *
     * @return bool
     */
    public function isStopped(): bool
    {
        return isset($this->end_date) ? (strtotime($this->end_date) < strtotime(date("Y-m-d"))) : false;
    }

    /**
     * checks if model is a prescription item based on usage type
     * @return bool
     */
    public function isPrescription(): bool
    {
        return $this->usage_type === 'OphDrPrescription';
    }

    /**
     * gets earliest entry for this medication
     *
     * @return EventMedicationUse
     */
    public function getEarliestEntry(): EventMedicationUse
    {
        if (!$this->id) {
            return $this;
        }
        $previous_medication = EventMedicationUse::model()->findByAttributes(['latest_med_use_id' => $this->id]);
        return ($previous_medication && isset($previous_medication->event)) ? $previous_medication->getEarliestEntry() : $this;
    }

    /**
     * returns if of latest medication item, if it exists, that has been prescribed
     *
     * @return int
     */
    public function findLatestPrescribedItemId(): ?int
    {
        if ($this->prescription_item_id) {
            return $this->prescription_item_id;
        }
        if ($this->isPrescription()) {
            return $this->id;
        }
        $previous_medication = EventMedicationUse::model()->findByAttributes(['latest_med_use_id' => $this->id]);
        return ($previous_medication && isset($previous_medication->event)) ? $previous_medication->findLatestPrescribedItemId() : null;
    }

    public function getComments()
    {
        if ($this->comments && !empty(trim($this->comments))) {
            return $this->comments;
        }
    }

    /**
     * Gets full change history
     *
     * @param int $max_no_of_previous_history_changes
     * @return array
     * @throws Exception
     */
    public function getChangeHistory(int $max_no_of_previous_history_changes = 3): array
    {
        $change_history = $this->getMedicationChangeHistory();
        if ($this->isPrescription()) {
            $change_history = array_merge($change_history, $this->getTaperChangeHistory());
        }

        usort($change_history, function ($a, $b) {
            return strtotime($a['change_date']) < strtotime($b['change_date']) ? -1 : 1;
        });

        $no_of_changes = count($change_history);
        $offset = ($no_of_changes > $max_no_of_previous_history_changes) ? ($no_of_changes - $max_no_of_previous_history_changes) : 0;

        return array_slice($change_history, $offset, $no_of_changes);
    }

    /**
     *
     * gets history of changes for the medication
     *
     * @param $change_history array
     * @return array
     */
    public function getMedicationChangeHistory(array $change_history = []): array
    {
        $id = $this->hasLinkedPrescribedEntry() ? $this->prescription_item_id : $this->id;
        if (!$id) {
            return $change_history;
        }
        $previous_changed_medication = \EventMedicationUse::model()->findByAttributes(['latest_med_use_id' => $id]);
        if ($previous_changed_medication) {
            if (isset($previous_changed_medication->event) && $previous_changed_medication->isChangedMedication()) {
                $change_history[] = [
                    'change_date' => Helper::formatFuzzyDate($previous_changed_medication->event->event_date),
                    'dosage' => $previous_changed_medication->getDoseAndFrequency(false),
                    'frequency' => $previous_changed_medication->frequency ? $previous_changed_medication->frequency->code : '',
                    'side' => $previous_changed_medication->getTooltipLateralityDisplay($previous_changed_medication->getLateralityDisplay()),
                    'label' => $this->createdUser->getFullName()
                ];
            }
            return $previous_changed_medication->getMedicationChangeHistory($change_history);
        }

        return $change_history;
    }

    /**
     * gets history of changes for the tapers
     * @return array
     * @throws Exception
     */
    public function getTaperChangeHistory(): array
    {
        $change_history = [];
        $prescription_item = OphDrPrescription_Item::model()->findByPk($this->id);
        $start_date = new DateTime($prescription_item->start_date);
        $taper_start_date = $start_date;
        if (!in_array($prescription_item->medicationDuration->name, array('Once', 'Other', 'Ongoing'))) {
            $taper_start_date = $start_date->add(DateInterval::createFromDateString($prescription_item->medicationDuration->name));
        }
        $current_date = new DateTime();

        foreach ($prescription_item->tapers as $taper) {
            $display_date = $taper_start_date->format('Y-m-d');
            if (!in_array($taper->duration->name, array('Once', 'Other', 'Ongoing'))) {
                $taper_start_date = $taper_start_date->add(DateInterval::createFromDateString($taper->duration->name));
            }
            if ($current_date > $taper_start_date) {
                $change_history[] = [
                    'change_date' => Helper::formatFuzzyDate($display_date),
                    'dosage' => $taper->getDosage(),
                    'frequency' => $taper->frequency ? $taper->frequency->code : '',
                    'label' => 'Taper'
                ];
            }
        }

        return $change_history;
    }

    /**
     * gets tooltip display for laterality
     * @param $laterality string
     * @return string
     */
    private function getTooltipLateralityDisplay(string $laterality): string
    {

        if ($laterality === '') {
            return $laterality;
        }

        $right_icon = in_array($laterality, ['B', 'R']) ? 'R' : 'NA';
        $left_icon = in_array($laterality, ['B', 'L']) ? 'L' : 'NA';

        ob_start(); ?>
        <span class="oe-eye-lat-icons">
             <i class="oe-i laterality small '. <?= $right_icon ?> . '"></i>
             <i class="oe-i laterality small '. <?= $left_icon ?> . '"></i>
         </span>
        <?php return ob_get_clean();
    }

    /**
     * Gets the tooltip information for the change history of the medication
     *
     * @param $change_history array
     * @return string
     */
    public function getChangeHistoryTooltipContent(array $change_history): string
    {
        $change_history = array_reverse($change_history);
        ob_start(); ?>
        <?= htmlspecialchars('<b class="fade">Previous changes</b><br><hr class="divider">', ENT_QUOTES, 'UTF-8') ?>
        <?php foreach ($change_history as $key => $medication_change) {
            if ($key !== 0) {
                echo htmlspecialchars('<hr class="divider">', ENT_QUOTES, 'UTF-8');
            } ?>
        <b><?= $medication_change['change_date'] ?></b> - <?= $medication_change['label'] ?><br>
            <?php if (!empty($medication_change['dosage'])) { ?>
            <b>Dosage: </b> <?= $medication_change['dosage'] ?> <br>
            <?php } ?>
            <?php if (!empty($medication_change['frequency'])) { ?>
            <b>Frequency: </b><?= $medication_change['frequency'] ?><br>
            <?php } ?>
            <?php if (!empty($medication_change['side'])) { ?>
            <b>Side: </b> <?= htmlspecialchars($medication_change['side'], ENT_QUOTES, 'UTF-8') ?><br>
            <?php } ?>
        <?php } ?>

        <?php return ob_get_clean();
    }


    public function getTooltipContent()
    {
        $data = [];

        $medication = Medication::model()->findByPk($this->medication_id);

        $dosage = $this->getDoseAndFrequency();
        if (!empty($dosage)) {
            $data['Dosage'] = $this->getDoseAndFrequency();
        }

        if ($this->route_id) {
            $data['Route'] = $this->route->term;
        }

        $data['Start date'] = Helper::formatFuzzyDate($this->start_date);
        if ($this->end_date) {
            $data['Stop date'] = Helper::formatFuzzyDate($this->end_date);
        }
        if ($this->stop_reason_id) {
            $data['Stop reason'] = $this->stopReason->name;
        }

        if ($this->comments && !empty(trim($this->comments))) {
            $data['Comments'] = $this->comments;
        }

        // Add a blank line if there is dose/date data
        if (sizeof($data) > 0) {
            $data[''] = "";
        }

        if ($medication) {
            if ($medication->isAMP()) {
                $data['Generic'] = isset($medication->vmp_term) ? $medication->vmp_term : "N/A";
                //$data['Moiety'] = isset($medication->vtm_term) ? $medication->vtm_term : "N/A";
            }

            // No longer showing moiety for VMPs - left commented as DA have not made a final decision yet (as per 27/03/2020)
            // if ($medication->isVMP()) {
            //     $data['Moiety'] = isset($medication->vtm_term) ? $medication->vtm_term : "N/A";
            // }
        } else {
            $data['Error'] = "Error while retrieving data for medication.";
        }

        $content = array();
        foreach ($data as $key => $value) {
            $content[] = (($key) ? "<b>$key: </b> " : "") . htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        return implode("<br/>", $content);
    }

    /**
     * @return bool
     */

    public function hasRisk()
    {
        if ($this->medication) {
            return count(OEModule\OphCiExamination\models\OphCiExaminationRisk::findForMedicationSetIds(array_map(
                    function ($t) {
                        return $t->id;
                    },
                    $this->medication->medicationSets
                ))) > 0;
        } else {
            return false;
        }
    }

    public function getStartDateDisplay()
    {
        if ($this->start_date) {
            return Helper::formatFuzzyDate($this->start_date);
        } elseif (isset($this->prescriptionItem) && $this->prescriptionItem->start_date) {
            return Helper::formatFuzzyDate($this->prescriptionItem->start_date);
        }
        return "";
    }

    public function getStopDateDisplay()
    {
        return '<div class="oe-date">' . Helper::convertFuzzyDate2HTML($this->end_date) . '</div>';
    }

    public function getEndDateDisplay($default = "")
    {

        if ($this->end_date) {
            return Helper::formatFuzzyDate($this->end_date);
        } elseif ($this->prescription_item_id) {
            $stop_date = $this->prescriptionItem->stopDateFromDuration(false);
            return $stop_date ? Helper::convertDate2NHS($stop_date->format('Y-m-d')) : $this->medicationDuration->name;
        } else {
            return $default;
        }
    }


    /**
     * Gets dose and frequency for medication
     *
     * @param bool $include_frequency
     * @return string
     */
    public function getDoseAndFrequency(bool $include_frequency = true): string
    {
        $result = [];

        if ($this->dose) {
            if ($this->dose_unit_term) {
                $result[] = $this->dose . ' ' . $this->dose_unit_term;
            } else {
                $result[] = $this->dose;
            }
        }

        if ($this->frequency && $include_frequency) {
            $result[] = $this->frequency;
        }

        return implode(', ', $result);
    }

    public function getChkPrescribe()
    {
        return $this->chk_prescribe;
    }

    public function setChkPrescribe($prescribe)
    {
        $this->chk_prescribe = $prescribe;
    }


    public function loadFromExisting($element)
    {
        parent::loadFromExisting($element);
        $this->updateStateProperties();
        $this->event_id = $element->event_id;
        $this->latest_med_use_id = $element->latest_med_use_id;
        if (!$this->prescription_item_id) {
            $this->is_copied_from_previous_event = true;
            $this->copied_from_med_use_id = $element->copied_from_med_use_id ? $element->copied_from_med_use_id : $element->event_id;
        } else {
            if (!$this->prescriptionItem->event) {
                $this->prescription_event_deleted = true;
            }
        }
    }

    /**
     * Abstraction to set up the entry state based on its current attributes
     */
    protected function updateStateProperties()
    {
        if ($this->isStopped()) {
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
        $this->bound_key = $item->bound_key;
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
        $attrs = [
            'medication_id', 'medication', 'route_id', 'route', 'laterality', 'medicationLaterality',
            'dose', 'dose_unit_term', 'frequency_id', 'frequency', 'comments'
        ];

        if ($this->start_date === null) { //this affects both OE-9616 && OE-9475
            $attrs[] = 'start_date';
        }
        foreach ($attrs as $attr) {
            $this->$attr = $item->$attr;
        }
        if (!$this->stop_reason_id) {
            $this->stop_reason_id = $item->stop_reason_id;
        }
        if (!$this->end_date) {
            $end_date = $item->end_date;
            $compare_date = new DateTime();

            if ($this->event && $this->event->event_date) {
                $compare_date = DateTime::createFromFormat('Y-m-d', $this->event->event_date);
            }
            if ($end_date && $end_date < $compare_date) {
                $this->originallyStopped = true;
                $this->end_date = $end_date;
            }
        }
    }

    public function setStopReasonTo($stop_reason)
    {
        $stop_reason_model = HistoryMedicationsStopReason::model()->findByAttributes(['name' => $stop_reason]);
        if ($stop_reason_model) {
            $this->stop_reason_id = $stop_reason_model->id;
        }
    }

    /**
     * Gets previous stop reason if exists
     *
     * @param $latest_med_use_id int
     * @return string | null
     */
    public function getPreviousStopReason(int $latest_med_use_id): ?string
    {
        $previous_medication = EventMedicationUse::model()->findByPk($latest_med_use_id);
        return $previous_medication && $previous_medication->stop_reason_id ? implode(',', [$previous_medication->stop_reason_id, $previous_medication->stopReason->name]) : null;
    }

    /**
     * Checks if medication has been changed and stopped
     * @return bool
     * */
    public function isChangedMedication(): bool
    {
        $medication_changed_stop_reason_id = HistoryMedicationsStopReason::getMedicationParametersChangedId();
        return $this->stop_reason_id === $medication_changed_stop_reason_id;
    }

    /**
     * Checks if medication is continued based on previous medication
     *
     * @param EventMedicationUse $previous_medication
     * @return bool
     */
    public function isContinuedMedication(EventMedicationUse $previous_medication): bool
    {
        if ($this->isUndated()) {
            if ($previous_medication->event_id !== $this->event_id) {
                return true;
            } elseif ($previous_medication->usage_subtype !== $this->usage_subtype) {
                return true;
            }
        } elseif (!$previous_medication->isUndated() && strtotime($previous_medication->start_date) < strtotime(date('Y-m-d'))) {
            return true;
        }

        return false;
    }

    /**
     * Checks if medication has been continued
     * @return EventMedicationUse
     */
    public function getContinuedMedication(): ?EventMedicationUse
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('latest_med_use_id = :latest_med_use_id');
        $criteria->params['latest_med_use_id'] = $this->hasLinkedPrescribedEntry() ? $this->prescription_item_id : $this->id;
        $previous_medication = EventMedicationUse::model()->find($criteria);

        if ($previous_medication && isset($previous_medication->event)) {
            if ($this->isContinuedMedication($previous_medication)) {
                return $previous_medication;
            } else {
                return $previous_medication->getContinuedMedication();
            }
        }

        return null;
    }

    /**
     * Checks if medication has a linked prescription that was prescribed from the element
     * @return bool
     * */
    public function hasLinkedPrescribedEntry(): bool
    {
        return $this->prescription_item_id && $this->prescribe;
    }

    /**
     * Gets latest medication recorded
     *
     * @return EventMedicationUse
     */
    public function getLatestMedication(): EventMedicationUse
    {
        if ($this->latest_med_use_id) {
            $latest_medication = EventMedicationUse::model()->findByPk($this->latest_med_use_id);
            if ($latest_medication && isset($latest_medication->event)) {
                return $latest_medication->getLatestMedication();
            }
        }

        return $this;
    }

    /**
     * Checks if start date has been set
     * @return bool
     * */
    public function isUndated(): bool
    {
        return empty($this->start_date) || $this->start_date === '0000-00-00';
    }

    public function beforeValidate()
    {
        if ($this->medication_id == self::USER_MEDICATION_ID) {
            $medication = new Medication();
            $medication->preferred_term = $this->medication_name;
            $medication->short_term = $this->medication_name;
            $medication->source_type = self::USER_MEDICATION_SOURCE_TYPE;
            $medication->source_subtype = self::USER_MEDICATION_SOURCE_SUBTYPE;
            $medication->preferred_code = Medication::getNextUnmappedPreferredCode();
            if ($medication->save()) {
                $medication->addDefaultSearchIndex();
                $this->medication_id = $medication->id;
            } else {
                $this->addError("medication_id", "There has been an error while saving the new medication '" . $this->medication_name . "'");
            }
        }

        if (EventMedicationUse::$local_to_dmd_conversion) {
            if (isset($this->end_date) && !isset($this->stop_reason_id)) {
                if (!isset(EventMedicationUse::$other_stop_reason)) {
                    EventMedicationUse::$other_stop_reason = HistoryMedicationsStopReason::model()->find("name = :name", [":name" => "Other"]);
                }
                $this->stop_reason_id = EventMedicationUse::$other_stop_reason->id;
            }
            if (isset($this->dose) && (!isset($this->dose_unit_term) || $this->dose_unit_term == "")) {
                $medication = Medication::model()->findByPk($this->medication_id);
                $this->dose_unit_term = ($medication && $medication->default_dose_unit_term) ?
                    $medication->default_dose_unit_term :
                    'unit';
            }
        }

        return parent::beforeValidate();
    }


    /**
     * @inheritdoc
     */
    protected function afterFind()
    {
        parent::afterFind();
        $this->updateStateProperties();
        if ($this->copied_from_med_use_id) {
            $this->is_copied_from_previous_event = true;
        }
    }

    /**
     * @inheritdoc
     */
    protected function afterSave()
    {
        if (!($this->usage_subtype === 'Management' && $this->prescribe)) {
            $prescription_api = Yii::app()->moduleAPI->get('OphDrPrescription');
            $examination_api = Yii::app()->moduleAPI->get('OphCiExamination');
            if ($prescription_api && $examination_api) {
                $existing_prescription_items = $this->getExistingItems($prescription_api, OphDrPrescription_Item::model());
                $existing_history_med_items = $this->getExistingItems($examination_api, EventMedicationUse::model());

                foreach (['OphDrPrescription' => $existing_prescription_items, 'OphCiExamination' => $existing_history_med_items] as $usage_type => $items) {
                    if ($items) {
                        $latest_item = end($items);
                        if ($latest_item->id !== $this->latest_med_use_id) {
                            if ($usage_type === 'OphDrPrescription') {
                                $latest_item_end_date = $latest_item->stopDateFromDuration();
                                if (!is_null($latest_item_end_date)) {
                                    $formatted_end_date = $latest_item_end_date->format('Y-m-d');
                                } else {
                                    $formatted_end_date = $latest_item->end_date;
                                }
                            } else {
                                $latest_item_end_date = $latest_item->end_date;
                                $formatted_end_date = $latest_item->end_date;
                            }
                            if (is_null($latest_item_end_date) || $formatted_end_date >= date('Y-m-d')) {
                                $attributes = ['latest_med_use_id' => $this->id];
                                if (!$this->isEqualsAttributes($latest_item, true, false)) {
                                    $latest_item->setStopReasonTo('Medication parameters changed');
                                    $attributes = array_merge($attributes, ['end_date' => date('Y-m-d'), 'stop_reason_id', 'is_discontinued' => false]);
                                }
                                $latest_item->saveAttributes($attributes);
                            }
                        }
                    }
                }
            }
            if (!$this->isPrescription()) {
                $this->latest_prescribed_med_use_id = $this->findLatestPrescribedItemId();
                if ($this->getIsNewRecord()) { // needs replacing with something better, was used previously so will use here for now
                    $this->setIsNewRecord(false);
                    $this->saveAttributes(['latest_prescribed_med_use_id']);
                    $this->setIsNewRecord(true);
                } else {
                    $this->saveAttributes(['latest_prescribed_med_use_id']);
                }
            }
        }

        parent::afterSave();
    }

    /**
     * @param $api
     * @param $model
     * @return array
     */
    private function getExistingItems($api, $model): array
    {
        $patient = $this->event->getPatient();
        $is_prescription = is_a($model, 'OphDrPrescription_Item');

        if ($is_prescription) {
            $exclude_models = $model->with('prescription', 'prescription.event', 'prescription.event.episode')
                ->findAll('medication_id !=? and episode.patient_id=?', [$this->medication_id, $patient->id]);
        } else {
            $exclude_models = $model->with('event', 'event.episode')
                ->findAll('(medication_id !=? and episode.patient_id=?) or prescribe=?', [$this->medication_id, $patient->id, 1]);
        }

        $exclude_ids = array_map(function ($item) {
            return $item->id;
        }, $exclude_models);
        $exclude_ids[] = $this->id;

        if ($is_prescription) {
            $items = $api->getPrescriptionItemsForPatient($patient, $exclude_ids);
        } else {
            $items = $api->getEventMedicationUseItemsForPatient($patient, $exclude_ids);
        }

        return array_filter($items, function ($item) {
            return is_null($item->latest_med_use_id);
        });
    }
}
