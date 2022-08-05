<?php
/**
 * This is the model class for table "ophdrpgdpsd_assignment_meds".
 *
 * The followings are the available columns in table 'ophdrpgdpsd_assignment_meds':
 * @property integer $id
 * @property integer $medication_id
 * @property integer $dose
 * @property string $dose_unit_term
 * @property integer $route_id
 * @property integer $pgdpsd_id
 *
 * The followings are the available model relations:
 * @property Medication $medication
 * @property MedicationRoute $route
 * @property OphDrPGDPSD_PGDPSD $pgdpsd
 */
class OphDrPGDPSD_AssignmentMeds extends BaseActiveRecordVersioned
{
    const ADMINISTERED = 1;
    const ADMINISTERED_CANCELLED = 2;
    const FOR_FUTURE = 3;
    const CANCELLED = 4;
    private $save_only_if_dirty = true;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrpgdpsd_assignment_meds';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        $common_rules =  array(
            array('assignment_id, medication_id, dose, dose_unit_term, route_id', 'required'),
            array('assignment_id, medication_id, dose', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            array('assignment_id, medication_id, dose, dose_unit_term, route_id, laterality, pair_key, administered, administered_time, administered_by, administered_id', 'safe'),
            array('id, medication_id, dose, dose_unit_term, route_id, laterality, pair_key, administered, administered_time, administered_by, administered_id', 'safe', 'on'=>'search'),
        );

        return $common_rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'medication' => array(self::BELONGS_TO, 'Medication', 'medication_id'),
            'assignment' => array(self::BELONGS_TO, 'OphDrPGDPSD_Assignment', 'assignment_id'),
            'medicationLaterality' => array(self::BELONGS_TO, 'MedicationLaterality', 'laterality_id'),
            "route" => array(self::BELONGS_TO, 'MedicationRoute', 'route_id'),
            "administered_user" => array(self::BELONGS_TO, 'User', 'administered_by'),
            "event_entry" => array(self::BELONGS_TO, 'Element_DrugAdministration_record', 'administered_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'assignment_id' => 'Assignment',
            'medication_id' => 'Medication',
            'dose' => 'Dose',
            'dose_unit_term' => 'Unit Term',
            'route_id' => 'Route',
            'administered' => 'Administered',
            'administered_time' => 'Administered Time',
            'administered_by' => 'Administered By',
            'administered_id' => 'Administered In'
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

        $criteria->compare('id', $this->id);
        $criteria->compare('medication_id', $this->medication_id, true);
        $criteria->compare('dose', $this->dose, true);
        $criteria->compare('dose_unit_term', $this->dose_unit_term, true);
        $criteria->compare('route_id', $this->route_id, true);
        $criteria->compare('pgdpsd_id', $this->pgdpsd_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PsdDrugSetItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function formatAdministerTime()
    {
        $completed_time = '';
        if ($this->administered_time) {
            $completed_time = date_format(date_create($this->administered_time), 'H:i');
        }
        return $completed_time;
    }

    public function getState()
    {
        $assignment = $this->assignment;
        if ($assignment && !(bool)$assignment->active) {
            return $this->administered ? self::ADMINISTERED_CANCELLED : self::CANCELLED;
        }
        if ($assignment && $this->administered) {
            return self::ADMINISTERED;
        }

        if ($assignment) {
            $appointment = $assignment->worklist_patient;
            $appointment_time = $appointment ? ($appointment->when ?? $appointment->worklist->start) : null;
            if ($appointment_time) {
                $date = date("Y-m-d", strtotime($appointment_time));
                if (strtotime($date) > strtotime(date("Y-m-d"))) {
                    return self::FOR_FUTURE;
                }
            }
        }

        return 0;
    }

    /**
     * @return array - $administered_ts, $administered_nhs, $administered_time, $administered_time_ui, $administered_user, $state_css
     */
    public function getAdministerDetails($is_edit = false)
    {
        $administered_ts = null;
        $administered_nhs = null;
        $administered_time = null;
        $administered_time_ui = null;
        $administered_user = null;
        $is_prescriber = \Yii::app()->user->checkAccess('Prescribe');
        $is_med_admin = \Yii::app()->user->checkAccess('Med Administer');
        switch ($this->state) {
            case self::ADMINISTERED:
            case self::ADMINISTERED_CANCELLED:
                $is_cancelled = $this->state === self::ADMINISTERED_CANCELLED;
                $css = "tick-green";
                $administered_user = $this->administered_user->getFullName();
                $administered_ts = strtotime($this->administered_time);
                $administered_nhs = \Helper::convertDate2NHS($this->administered_time, ' ');
                $administered_time = date('H:i', strtotime($this->administered_time));
                $administered_time_ui = $is_prescriber && $is_edit && !$is_cancelled ? "<input type='time' value='{$administered_time}'>" : $administered_time;
                break;
            case self::FOR_FUTURE:
                $css = $is_edit ? "waiting selected" : "start";
                $administered_user = "<small class='fade'>Assigned to date/clinic</small>";
                break;
            case self::CANCELLED:
                $css = "cross-red no-click";
                $administered_user = "<small class='fade'>Cancelled</small>";
                break;
            default:
                $css = "waiting selected";
                $administered_user = "<small class='fade'>Waiting to administer</small>";
                break;
        }
        $action_icon = array(
            'class' => '',
            'attribute' => ''
        );
        if ($this->state === self::FOR_FUTURE) {
            $action_icon['class'] = " small-icon start";
            $action_icon['attribute'] = "";
        } elseif ($this->state === self::CANCELLED) {
            $action_icon['class'] = " info small-icon js-has-tooltip";
            $action_icon['attribute'] = "data-tooltip-content='Block has been cancelled, this drug was not administered'";
        } elseif ($this->state === self::ADMINISTERED_CANCELLED) {
            $action_icon['class'] = " no-permissions small-icon js-has-tooltip";
            $action_icon['attribute'] = "data-tooltip-content='Can not remove administered drugs'";
        } elseif (!$this->assignment->pgdpsd && !$this->assignment->worklist_patient) {
            if ($this->administered) {
                $action_icon['class'] = " no-permissions small-icon js-has-tooltip";
                $action_icon['attribute'] = "data-tooltip-content='Can not remove administered drugs'";
            } else {
                if ($is_prescriber || $is_med_admin) {
                    $action_icon['class'] = " trash js-remove-med";
                    $action_icon['attribute'] = "";
                } else {
                    $action_icon['class'] = " no-permissions small-icon js-has-tooltip";
                    $action_icon['attribute'] = "data-tooltip-content='No Permission'";
                }
            }
        } else {
            $action_icon['class'] = " no-permissions small-icon js-has-tooltip";
            $action_icon['attribute'] = "data-tooltip-content='Drugs within a Preset Order not be changed'";
        }
        return array(
            'administered_ts' => $administered_ts,
            'administered_nhs' => $administered_nhs,
            'administered_time' => $administered_time,
            'administered_time_ui' => $administered_time_ui,
            'administered_user' => $administered_user,
            'state_css' => $css,
            'action_icon' => $action_icon,
        );
    }
}
