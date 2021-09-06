<?php

/**
 * This is the model class for table "pathway".
 *
 * The followings are the available columns in table 'pathway':
 * @property int $id
 * @property int $worklist_patient_id
 * @property int $pathway_type_id
 * @property bool $did_not_attend
 * @property string $owner_id
 * @property string $start_time
 * @property string $end_time
 * @property string status
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property User $owner
 * @property PathwayComment $comment
 * @property PathwayType $type
 * @property WorklistPatient $worklist_patient
 * @property PathwayStep[] $requested_steps
 * @property PathwayStep[] $started_steps
 * @property PathwayStep[] $completed_steps
 * @property PathwayStep[] $steps
 */
class Pathway extends BaseActiveRecordVersioned
{
    public const STATUS_LATER = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_STUCK = 2;
    public const STATUS_WAITING = 3;
    public const STATUS_DELAYED = 4;
    public const STATUS_BREAK = 5;
    public const STATUS_DISCHARGED = 6;
    public const STATUS_DONE = 7;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pathway';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('worklist_patient_id, pathway_type_id', 'required'),
            array('worklist_patient_id, pathway_type_id', 'numerical', 'integerOnly' => true),
            array('owner_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('start_time, end_time, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array(
                'id, worklist_patient_id, pathway_type_id, owner_id, start_time, end_time',
                'safe',
                'on' => 'search'
            ),
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
            'owner' => array(self::BELONGS_TO, 'User', 'owner_id'),
            'type' => array(self::BELONGS_TO, 'PathwayType', 'pathway_type_id'),
            'worklist_patient' => array(self::BELONGS_TO, 'WorklistPatient', 'worklist_patient_id'),
            'comment' => array(self::HAS_ONE, 'PathwayComment', 'pathway_id'),
            'requested_steps' => array(
                self::HAS_MANY,
                'PathwayStep',
                'pathway_id',
                'condition' => 'status IS NULL OR status IN (' . implode(
                    ', ',
                    [PathwayStep::STEP_REQUESTED, PathwayStep::STEP_CONFIG]
                ) . ')',
                'order' => '`order`'
            ),
            'started_steps' => array(
                self::HAS_MANY,
                'PathwayStep',
                'pathway_id',
                'condition' => 'status = ' . PathwayStep::STEP_STARTED,
                'order' => '`order`'
            ),
            'completed_steps' => array(
                self::HAS_MANY,
                'PathwayStep',
                'pathway_id',
                'condition' => 'status = ' . PathwayStep::STEP_COMPLETED,
                'order' => '`order`'
            ),
            'steps' => array(self::HAS_MANY, 'PathwayStep', 'pathway_id', 'order' => 'status DESC, `order` ASC'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'worklist_patient_id' => 'Worklist Patient',
            'pathway_type_id' => 'Pathway Type',
            'owner_id' => 'Owner',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
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
        $criteria->compare('worklist_patient_id', $this->worklist_patient_id);
        $criteria->compare('pathway_type_id', $this->pathway_type_id);
        $criteria->compare('owner_id', $this->owner_id, true);
        $criteria->compare('start_time', $this->start_time, true);
        $criteria->compare('end_time', $this->end_time, true);

        return new CActiveDataProvider(
            $this,
            array(
                'criteria' => $criteria,
            )
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Pathway the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return int[]
     */
    public static function inProgressStatuses()
    {
        return array(
            self::STATUS_ACTIVE,
            self::STATUS_STUCK,
            self::STATUS_WAITING,
            self::STATUS_DELAYED,
            self::STATUS_BREAK,
        );
    }

    /**
     * Add a step to a queue relevant to its current status.
     * @param PathwayStep $step The step to add
     * @return bool True if the step was successfully enqueued; otherwise false.
     * @throws Exception
     */
    public function enqueue(PathwayStep $step): bool
    {
        if ((int)$step->status === PathwayStep::STEP_REQUESTED) {
            $end_position = Yii::app()->db->createCommand()
                ->select('MAX(`order`)')
                ->from('pathway_step')
                ->where('pathway_id = :id AND status IN (-1, 0)')
                ->bindValues([':id' => $this->id])
                ->queryScalar();
        } else {
            $end_position = Yii::app()->db->createCommand()
                ->select('MAX(`order`)')
                ->from('pathway_step')
                ->where('pathway_id = :id AND status = :status')
                ->bindValues([':id' => $this->id, ':status' => $step->status])
                ->queryScalar();
        }

        $step->order = $end_position + 1;
        return $step->save();
    }

    /**
     * @param PathwayStep $step The step to enqueue.
     * @param int $position The position in the requested steps queue to place the step
     * @return bool True if enqueued successfully; otherwise false.
     * @throws Exception
     */
    public function enqueueAtPosition(PathwayStep $step, int $position): bool
    {
        $start_position = $position;
        $step->order = $start_position;
        foreach ($this->requested_steps as $existing_step) {
            if ($existing_step->order >= $position) {
                $existing_step->order = ++$start_position;
                $existing_step->save();
            }
        }
        return $step->save();
    }

    /**
     * Gets the step at the front of the specified queue if there are any steps for the specified queue.
     * @param int $queue The queue to peek at. This value should match one of the three step statuses.
     * @return PathwayStep|null The step at the front of the queue; otherwise null.
     */
    public function peek(int $queue): ?PathwayStep
    {
        switch ($queue) {
            case PathwayStep::STEP_COMPLETED:
                $prefix = 'completed';
                break;
            case PathwayStep::STEP_STARTED:
                $prefix = 'started';
                break;
            default:
                $prefix = 'requested';
                break;
        }
        return empty(($this->{$prefix . '_steps'})) ? null : ($this->{$prefix . '_steps'})[0];
    }

    /**
     * Gets the string representation of the pathway's current status.
     * @return string The string representation of the pathway status.
     */
    public function getStatusString(): string
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'active';
            case self::STATUS_STUCK:
                return 'stuck';
            case self::STATUS_WAITING:
                return 'waiting';
            case self::STATUS_DELAYED:
                return 'long-wait';
            case self::STATUS_BREAK:
                return 'break';
            case self::STATUS_DISCHARGED:
                return 'discharged';
            case self::STATUS_DONE:
                return 'done';
            default:
                return 'later';
        }
    }

    /**
     * Gets all steps associated with the pathway in a JSON-convertible array.
     * @return array The steps associated with the pathway in JSON array format.
     * @throws Exception
     */
    public function stepsAsJSON(): array
    {
        $json = array();

        foreach ($this->requested_steps as $step) {
            $json['requested_steps'][] = $step->toJSON();
        }
        foreach ($this->started_steps as $step) {
            $json['started_steps'][] = $step->toJSON();
        }
        foreach ($this->completed_steps as $step) {
            $json['completed_steps'][] = $step->toJSON();
        }
        return $json;
    }

    /**
     * Gets the status icon HTML for the pathway.
     * @return string The HTML for the pathway status icon.
     */
    public function getPathwayStatusHTML(): string
    {
        $class = 'oe-i pad js-has-tooltip ';
        switch ($this->status) {
            case self::STATUS_LATER:
                $class .= 'no-permissions small-icon';
                $tooltip_text = 'Pathway not started';
                break;
            case self::STATUS_DISCHARGED:
                $class .= 'save medium-icon js-pathway-complete';
                $tooltip_text = 'Pathway completed';
                break;
            case self::STATUS_DONE:
                // Done.
                $class .= 'undo medium-icon js-pathway-reactivate';
                $tooltip_text = 'Re-activate pathway to add steps';
                break;
            default:
                // Covers all 'active' statuses, including long-wait and break.
                $class .= 'save-blue medium-icon js-pathway-finish';
                $tooltip_text = 'Patient has left<br/>Quick complete pathway';
                break;
        }
        return "<i class=\"$class\" data-tooltip-content=\"$tooltip_text\" data-pathway-id=\"{$this->id}\"></i>";
    }

    /**
     * Removes all requested and started steps associated with the pathway.
     * @return int The number of deleted steps.
     * @throws Exception
     */
    public function removeIncompleteSteps(): int
    {
        $step_ids = Yii::app()->db->createCommand()
            ->select('id')
            ->from('pathway_step')
            ->where('pathway_id = :id AND status != :status')
            ->bindValues([':id' => $this->id, ':status' => PathwayStep::STEP_COMPLETED])
            ->queryColumn();
        return PathwayStep::model()->deleteByPk($step_ids);
    }

    /**
     * Marks all requested and started steps associated with the pathway as completed.
     * @return bool True if all steps were successfully marked as complete; otherwise false.
     * @throws Exception
     */
    public function completeIncompleteSteps(): bool
    {
        foreach ($this->started_steps as $step) {
            $step->status = PathwayStep::STEP_COMPLETED;
            $step->end_time = date('Y-m-d H:i:s');
            $step->completed_user_id = Yii::app()->user->id;
            if (!$this->enqueue($step)) {
                return false;
            }
        }

        foreach ($this->requested_steps as $step) {
            $step->status = PathwayStep::STEP_COMPLETED;
            $step->start_time = date('Y-m-d H:i:s');
            $step->end_time = date('Y-m-d H:i:s');
            $step->started_user_id = Yii::app()->user->id;
            $step->completed_user_id = Yii::app()->user->id;
            if (!$this->enqueue($step)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return int
     */
    public function getAcceptableWaitTime()
    {
        $wait_times = WorklistWaitTime::model();
        $levels = $wait_times->enumerateSupportedLevels();
        rsort($levels);
        foreach ($levels as $level) {
            $wait_values = $wait_times->findAllAtLevel($level);
            if (count($wait_values) > 0) {
                return (int)($wait_values[0]->wait_minutes);
            }
        }
        return 60;
    }

    /**
     * Get the wait time since last completed action
     * The hold step shouldn't be considered as "last action"
     *
     * @return array
     */
    public function getWaitTimeSinceLastAction(): array
    {
        $acceptable_wait_time = $this->getAcceptableWaitTime();
        if ($this->completed_steps) {
            $completed_steps = array_filter($this->completed_steps, function ($step) {
                return $step->type->type !== 'hold';
            });
            $last_completed_step = $completed_steps[array_key_last($completed_steps)];
            $start_time = DateTime::createFromFormat('Y-m-d H:i:s', $last_completed_step->end_time);
        } else {
            $start_time = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $this->start_time
            );
        }
        $end_time = new DateTime();
        $wait_time = floor(($end_time->getTimestamp() - $start_time->getTimestamp()) / 60);

        return array(
            'wait_time' => $wait_time,
            'status_class' => $wait_time > $acceptable_wait_time ? 'delayed-wait' : 'wait',
            'icon_class' => $wait_time > $acceptable_wait_time ? 'i-delayed' : 'i-wait'
        );
    }

    public function getTotalDurationHTML($show_duration = false): string
    {
        $duration_graphic = '';
        if ($this->worklist_patient->when) {
            if ($this->worklist_patient->when instanceof DateTime) {
                $start_time = $this->worklist_patient->when;
            } else {
                $start_time = DateTime::createFromFormat('Y-m-d H:i:s', $this->worklist_patient->when);
            }
            // find the started break steps
            $started_break_steps = array_filter($this->started_steps, static function ($step) {
                return $step->type->short_name === 'break';
            });
            if ($started_break_steps) {
                // get the earliest start time among the break steps
                $break_step_time = min(array_map(static function ($o) {
                    return $o->start_time;
                }, $this->started_steps));
                // use break step start time as the end time for the calculation
                $end_time = DateTime::createFromFormat('Y-m-d H:i:s', $break_step_time);
            } else {
                $end_time = $this->end_time
                    ? DateTime::createFromFormat('Y-m-d H:i:s', $this->end_time)
                    : new DateTime();
            }

            $wait_length = $start_time->diff($end_time);

            // find the completed steps
            $completed_break_steps = array_filter($this->completed_steps, static function ($step) {
                return $step->type->short_name === 'break';
            });

            $total_break_time = 0;
            foreach ($completed_break_steps as $step) {
                $total_break_time += (strtotime($step->end_time) - strtotime($step->start_time));
            }
            // hack way to exclude finished break time
            if ($total_break_time) {
                $date1 = new DateTime();
                $date1->add(new DateInterval("PT{$total_break_time}S"));
                $date2 = new DateTime();
                $date2->add($wait_length);
                $wait_length = $date1->diff($date2);
            }
            if ((int)$this->status !== self::STATUS_DONE) {
                // Get the status color based on wait time
                if ($wait_length->h < 2) {
                    $wait_color = 'green';
                } elseif ($wait_length->h < 3) {
                    $wait_color = 'yellow';
                } elseif ($wait_length->h < 4) {
                    $wait_color = 'orange';
                } else {
                    $wait_color = 'red';
                }
                $duration_graphic = '<svg class="duration-graphic ' . $wait_color . '" viewBox="0 0 48 12" height="12" width="48">
                                    <circle class="c0" cx="6" cy="6" r="6"></circle>
                                    <circle class="c1" cx="18" cy="6" r="6"></circle>
                                    <circle class="c2" cx="30" cy="6" r="6"></circle>
                                    <circle class="c3" cx="42" cy="6" r="6"></circle>
                                </svg>';
            }
            // Show duration of the pathway
            $duration_graphic .= '<div class="mins">';
            if ($show_duration) {
                if ((int)$this->status === self::STATUS_DONE) {
                    $duration_graphic .= $wait_length->format('%h:%I');
                } else {
                    $duration_graphic .= '<small>' . $wait_length->format('%h:%I') . '</small>';
                }
            }
            $duration_graphic .= '</div>';
        }

        return $duration_graphic;
    }

    /**
     * @throws JsonException
     */
    public function getPathwaysJSON($pathways = null)
    {
        if ($pathways === null) {
            $pathways = PathwayType::model()->findAll();
        }
        $pathway_json = json_encode(array_map(
            static function ($item) {
                return ['id' => $item->id, 'name' => 'pathway', 'label' => $item->name];
            },
            $pathways
        ), JSON_THROW_ON_ERROR);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(json_last_error_msg());
        }
        return $pathway_json;
    }

    /**
     * Check if pathway or any of the pathsteps have any comments
     * @return bool
     */
    public function checkForComments(): bool
    {
        if ($this->comment !== null) {
            return true;
        }
        foreach ($this->steps as $step) {
            if ($step->comment !== null) {
                return true;
            }
        }
        return false;
    }
}
