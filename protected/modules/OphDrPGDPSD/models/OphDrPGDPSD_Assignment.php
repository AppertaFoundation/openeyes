<?php
/**
 * This is the model class for table "ophdrpgdpsd_assignment".
 *
 * The followings are the available columns in table 'ophdrpgdpsd_assignment':
 * @property integer $id
 * @property integer $patient_id
 * @property integer $pgdpsd_id
 * @property integer $last_modified_user_id
 * @property string $last_modified_date
 * @property integer $created_user_id
 * @property string $created_date
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property OphDrPGDPSD_PGDPSD $pgdpsd
 * @property Patient $patient
 * @property WorklistPatient $worklist_patient
 */
class OphDrPGDPSD_Assignment extends \BaseActiveRecordVersioned
{
    public const INACTIVE = 0;
    public const STATUS_TODO = 1;
    public const STATUS_PART_DONE = 2;
    public const STATUS_COMPLETE = 3;
    public const EXPIRED = 4;
    public $temp_meds = array();
    protected $saved_meds = array();
    protected $auto_update_relations = true;
    private $_is_relevant = true;
    public $create_wp = false;
    private $_confirmed = 0;
    public $is_record_admin = false;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrpgdpsd_assignment';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('status, assigned_meds', 'required'),
            array('_confirmed', 'validateConfirmation'),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            // The following rule is used by search().
            array('patient_id, pgdpsd_id, status, visit_id, assigned_meds, comment', 'safe'),
            array('institution_id', 'default', 'value' => Yii::app()->session->get('selected_institution_id'), 'on' => 'insert'),
            array('id, patient_id, visit_id, pgdpsd_id, status, comment_id', 'safe', 'on'=>'search'),
        );
    }

    public function defaultScope()
    {
        $selected_institution_id = Yii::app()->session->get('selected_institution_id');
        if (!$selected_institution_id) {
            return array();
        }
        $table_alias = $this->getTableAlias(false, false);
        return array(
            'condition' =>"$table_alias.institution_id = :institution_id",
            'params' => array(
                ':institution_id' => $selected_institution_id,
            )
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
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'pgdpsd' => array(self::BELONGS_TO, 'OphDrPGDPSD_PGDPSD', 'pgdpsd_id'),
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'worklist_patient' => array(self::BELONGS_TO, 'WorklistPatient', 'visit_id'),
            'assigned_meds' => array(self::HAS_MANY, 'OphDrPGDPSD_AssignmentMeds', 'assignment_id'),
            'elements' => array(
                self::MANY_MANY,
                'Element_DrugAdministration',
                'et_drug_administration_assignments(assignment_id, element_id)',
                'order' => 'elements.created_date DESC'
            ),
            'comment' => array(self::BELONGS_TO, 'OphDrPGDPSD_Assignment_Comment', 'comment_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
        );
    }

   /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'patient_id' => 'Patient',
            'pgdpsd_id' => 'PGDPSD',
            'status' => 'Status',
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
        $criteria->compare('patient_id', $this->patient_id);
        $criteria->compare('visit_id', $this->visit_id);
        $criteria->compare('pgdpsd_id', $this->pgdpsd_id);
        $criteria->compare('status', $this->status);
        $criteria->compare('comment_id', $this->comment_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function todoAndActive($patient_id, $event_date, $is_prescriber)
    {
        $condition = array(
            'condition' => '(t.status = :todo OR t.status = :active) AND t.patient_id = :patient_id AND t.active = 1',
            'params' => array(
                ':todo' => self::STATUS_TODO,
                ':active' => self::STATUS_PART_DONE,
                ':patient_id' => $patient_id,
            ),
            'with' => array(
                'worklist_patient' => array(
                    'condition' => "DATE_FORMAT(worklist_patient.`when`, '%Y-%m-%d') >= :event_date",
                    'params' => array(
                        ':event_date' => $event_date
                    ),

                )
            ),
            'order' => 'worklist_patient.`when`'
        );
        $this->getDbCriteria()->mergeWith($condition);
        return $this;
    }
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphDrPGDPSD_Assignment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function cacheMeds($meds = array())
    {
        $assigned_meds = array();
        $orig_meds = array();
        $pair_keys = array();
        foreach ($this->assigned_meds as $med) {
            $orig_meds[$med->id] = $med;
        }
        foreach ($meds as $key => &$entry_data) {
            $entry_data['administered'] = isset($entry_data['administered']) ? $entry_data['administered'] : 0;
            $entry_data['administered_time'] = isset($entry_data['administered_time']) && $entry_data['administered_time'] ? date('Y-m-d H:i:s', $entry_data['administered_time'] / 1000) : null;
            $entry_data['administered_by'] = isset($entry_data['administered_by']) && $entry_data['administered_by'] ? $entry_data['administered_by'] : null;
            $id = isset($entry_data['id']) ? $entry_data['id'] : null;
            if ($id && $orig_meds[$id]) {
                $entry = $orig_meds[$id];
            } else {
                $entry = new \OphDrPGDPSD_AssignmentMeds();
            }
            unset($entry_data['id']);
            $entry->attributes = $entry_data;
            $entry_data['pair_key'] = isset($entry_data['pair_key']) && !empty($entry_data['pair_key']) ? $entry_data['pair_key'] : null;
            if ($entry_data['pair_key']) {
                $term = $entry->medication->getLabel(true);
                $dose_unit = "{$entry->dose} {$entry->dose_unit_term}";
                $route = "{$entry->route}";
                if (!isset($pair_keys[$entry_data['pair_key']])) {
                    $pair_keys[$entry_data['pair_key']]['key'] = substr(bin2hex(openssl_random_pseudo_bytes(10)), 0, 10);
                    $pair_keys[$entry_data['pair_key']]['med_info'] = "$term $dose_unit $route";
                    $pair_keys[$entry_data['pair_key']]['med_obj'] = $entry;
                }
                if ($pair_keys[$entry_data['pair_key']]['med_info'] !== "$term $dose_unit $route") {
                    $entry->pair_key = null;
                    $pair_keys[$entry_data['pair_key']]['med_obj']->pair_key = null;
                } else {
                    $entry->pair_key = $pair_keys[$entry_data['pair_key']]['key'];
                }
            }
            $entry->validate();
            $errors = $entry->getErrors();
            if ($errors) {
                foreach ($errors as $attr => $msg) {
                    if ($attr === 'assignment_id') {
                        continue;
                    }
                    $this->addError("_{$key}_{$attr}", "{$entry->medication->getLabel()} {$msg[0]}");
                }
            }
            $assigned_meds[] = $entry;
        }
        $this->assigned_meds = $assigned_meds;
    }

    public function saveComment($comment)
    {
        if (!$this->comment) {
            $comment_obj = new \OphDrPGDPSD_Assignment_Comment();
        } else {
            $comment_obj = $this->comment;
        }
        $comment_obj->comment = $comment;
        $comment_obj->commented_by = \Yii::app()->user->id;
        if ($comment_obj->isModelDirty()) {
            $comment_obj->save();
        }
        $this->comment_id = $comment_obj->id;
    }

    /**
     * @param bool|false $get_dict
     * @param PathwayStep|null $step
     * @return false|string|string[]
     */
    public function getStatusDetails(bool $get_dict = false, PathwayStep $step = null)
    {
        $todo_next = $this->worklist_patient && $this->worklist_patient->pathway && $this->worklist_patient->pathway->start_time ? 'todo-next' : 'todo';

        if ($step) {
            $status_dict = array(
                PathwayStep::STEP_REQUESTED => array(
                    'text' => "Waiting to be done",
                    'css' => $todo_next
                ),
                PathwayStep::STEP_STARTED => array(
                    'text' => "Currently active",
                    'css' => 'active'
                ),
                PathwayStep::STEP_COMPLETED => array(
                    'text' => "Completed",
                    'css' => 'done'
                ),
            );
        } else {
            $status_dict = array(
                self::STATUS_TODO => array(
                    'text' => "Waiting to be done",
                    'css' => $todo_next
                ),
                self::STATUS_PART_DONE => array(
                    'text' => "Currently active",
                    'css' => 'active'
                ),
                self::STATUS_COMPLETE => array(
                    'text' => "Completed",
                    'css' => 'done'
                ),
            );
        }

        return $get_dict
            ? json_encode(array_values($status_dict))
            : $status_dict[($step->status ?? $this->status)];
    }

    public function getAssignedMeds()
    {
        // assign eye route and non-eye route separately to avoid overhead if statements
        $assigned_meds = array(
            'eye' => array(),
            'other' => array(),
        );
        $administerd = array();
        foreach ($this->assigned_meds as $med) {
            $term = $med->medication->getLabel(true);
            $dose_unit = "{$med->dose} {$med->dose_unit_term}";
            $route = "{$med->route}";
            $loop_key = $med->pair_key ? : $med->id;
            $expected_administered_num = $med->pair_key ? 2 : 1;
            if (!isset($administerd[$loop_key])) {
                $administerd[$loop_key] = 0;
            }
            if ($med->route->isEyeRoute()) {
                $lat_str = intval($med->laterality) === MedicationLaterality::LEFT ? 'Left' : 'Right';
                $administerd[$loop_key] = $administerd[$loop_key] + intval($med->administered);
                $assigned_meds['eye'][$loop_key]['term'] = $term;
                $assigned_meds['eye'][$loop_key]['dose_unit'] = $dose_unit;
                $assigned_meds['eye'][$loop_key]['administration_status'] = $administerd[$loop_key] === $expected_administered_num ? 'tick' : 'waiting';
                $assigned_meds['eye'][$loop_key][$lat_str] = $med;
            } else {
                $assigned_meds['other'][$loop_key]['term'] = $term;
                $assigned_meds['other'][$loop_key]['dose_unit'] = $dose_unit;
                $assigned_meds['other'][$loop_key]['administration_status'] = $med->administered ? 'tick' : 'waiting';
                $assigned_meds['other'][$loop_key]['route'] = $route;
                $assigned_meds['other'][$loop_key]['obj'] = $med;
            }
        }
        return $assigned_meds;
    }

    public function updateStatus()
    {
        $entries = $this->assigned_meds;
        $administered_count = 0;
        $status = $this->status;
        foreach ($this->assigned_meds as $med) {
            $event_entry = $med->event_entry;
            if ($event_entry && (!$event_entry->event || ($event_entry->event && $event_entry->event->deleted) || !$event_entry->element)) {
                // assignment med has corresponding event medication use but event is deleted
                $med->administered_id = null;
                $med->administered = 0;
                $med->administered_time = null;
                $med->administered_by = null;
                $med->save();
            }
            if ($med->administered) {
                $administered_count++;
            }
        }
        if (count($entries) === $administered_count) {
            $status = $this::STATUS_COMPLETE;
        } elseif ($administered_count > 0) {
            $status = $this::STATUS_PART_DONE;
        } else {
            $status = $this::STATUS_TODO;
        }
        $this->status = (string)$status;
        if ($this->isModelDirty()) {
            $this->save();
        }
    }

    public function checkAuth($user)
    {
        $authed_users = array();
        if ($this->pgdpsd) {
            foreach ($this->pgdpsd->teams as $team) {
                $authed_users = array_merge($team->getAllUsers(), $authed_users);
            }
            $authed_users = array_merge($this->pgdpsd->users, $authed_users);
        }
        $authed_user_ids = array_map(function ($user) {
            return $user->id;
        }, $authed_users);
        return in_array($user->id, $authed_user_ids);
    }

    public function getAppointmentDetails()
    {
        $ret = array(
            'date' => null,
            'time' => null,
            'appt_details_dom' => null,
            'valid_date_dom' => null,
        );
        if (!$this->worklist_patient) {
            if ($this->create_wp) {
                $date = 'Today';
                $time = date("H:i");
                $valid_date = \Helper::convertDate2NHS(date("Y-m-d"), ' ');
                $ret = array(
                    'date' => 'Today',
                    'time' => date("H:i"),
                    'appt_details_dom' => "<i class='oe-i small start pad'></i><span>$date<span class='fade'><small> at </small>$time </span>Unbooked Appointment</span>",
                    'valid_date_dom' => "<span class='highlighter'>Assigned for: $valid_date</span>",
                );
            }
            return $ret;
        }
        $assignment_date = $this->worklist_patient->when ? : $this->created_date;
        $date = date("Y-m-d", strtotime($assignment_date));
        $time = date("H:i", strtotime($assignment_date));
        $valid_date = \Helper::convertDate2NHS($date, ' ');
        $wl_name = $this->worklist_patient->worklist->name;
        $warning_css = '';
        if ($date === date("Y-m-d")) {
            $date = 'Today';
        } else {
            $date = $valid_date;
        }
        if (intval($this->status) === self::STATUS_COMPLETE) {
            $completed_date = max(
                array_map(function ($med) {
                    return $med->administered_time;
                }, $this->assigned_meds)
            );
            $completed_date = \Helper::convertDate2NHS($completed_date, ' ');
            return array(
                'date' => $date,
                'time' => $time,
                'appt_details_dom' => "<i class='oe-i small start pad'></i><span>$date<span class='fade'><small> at </small>$time </span>$wl_name</span>",
                'valid_date_dom' => "<span><small class='fade'>Completed: </small>$completed_date</span>"
            );
        }
        return array(
            'date' => $date,
            'time' => $time,
            'appt_details_dom' => "<i class='oe-i small start pad'></i><span>$date<span class='fade'><small> at </small>$time </span>$wl_name</span>",
            'valid_date_dom' => "<span class='highlighter $warning_css'>Assigned for: $valid_date</span>"
        );
    }

    public function getAssignmentTypeAndName()
    {
        if (!$this->pgdpsd) {
            $name = "{$this->createdUser->getFullName()} (Custom)";
            $type = 'Custom';
        } else {
            $name = $this->pgdpsd->name;
            $type = 'Preset';
        }
        return array(
            'name' => $name,
            'type' => $type,
        );
    }

    protected function afterFind()
    {
        parent::afterFind();
        if ($this->active) {
            $this->updateStatus();
        }
    }

    // for arry_unique in DrugAdministration widget
    public function __toString()
    {
        // if there is no id (new record), generate random unique string
        return $this->id ? : md5(uniqid(rand(), true));
    }

    public function validateConfirmation($attribute_name)
    {
        if (!$this->confirmed) {
            $this->addError('Assignment', "Order must be either assigned or recorded as administered");
            return false;
        }
        return true;
    }

    public function getConfirmed()
    {
        if (!$this->isNewRecord) {
            return 1;
        } else {
            return $this->_confirmed;
        }
    }

    public function setConfirmed($val)
    {
        $this->_confirmed = $val;
    }

    public function getIsRelevant()
    {
        $current_user = \User::model()->findByPk(\Yii::app()->user->id);
        $is_prescriber = \Yii::app()->user->checkAccess('Prescribe');
        $is_med_admin = \Yii::app()->user->checkAccess('Med Administer');
        if ($this->worklist_patient) {
            $appt_date = date('Y-m-d', strtotime($this->worklist_patient->when));
            $today = date('Y-m-d');
            if ($appt_date > $today || (!$this->checkAuth($current_user) && !$is_prescriber && !$is_med_admin)) {
                $this->_is_relevant = false;
            }
        }
        return $this->_is_relevant;
    }
}
