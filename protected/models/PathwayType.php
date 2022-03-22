<?php

/**
 * This is the model class for table "pathway_type".
 *
 * The followings are the available columns in table 'pathway_type':
 * @property int $id
 * @property string $name
 * @property int $default_owner_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Pathway[] $pathways
 * @property PathwayTypeStep[] $default_steps
 * @property User $default_owner
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property WorklistDefinition[] $worklist_definitions
 */
class PathwayType extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pathway_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('name', 'length', 'max' => 255),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array(
                'id, name',
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
            'pathways' => array(self::HAS_MANY, 'Pathway', 'pathway_type_id'),
            'default_steps' => array(self::HAS_MANY, 'PathwayTypeStep', 'pathway_type_id', 'order' => '`order`'),
            'default_owner' => array(self::BELONGS_TO, 'User', 'default_owner_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'worklist_definitions' => array(self::HAS_MANY, 'WorklistDefinition', 'pathway_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
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
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);

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
     * @return PathwayType the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $worklist_patient_id int
     * @param $start_time string
     * @return bool
     * @throws Exception
     */
    public function createNewPathway(int $worklist_patient_id): bool
    {
        $pathway = new Pathway();
        $pathway->worklist_patient_id = $worklist_patient_id;
        $pathway->pathway_type_id = $this->id;

        if (!$pathway->save()) {
            return false;
        }
        return true;
    }

    /**
     * @param int $pathway_id
     * @return PathwayStep[]
     * @throws JsonException
     */
    public function instancePathway(int $pathway_id): array
    {
        $new_steps = array();
        $pathway = Pathway::model()->findByPk($pathway_id);

        if ($pathway) {
            if (count($pathway->steps) > 0) {
                return $new_steps;
            }
            foreach ($this->default_steps as $step) {
                $new_step = $step->cloneStepForPathway($pathway_id, array());
                if (!$new_step) {
                    return $new_steps;
                }
                $new_steps[$step->id] = $new_step;
            }
        }
        return $new_steps;
    }

    /**
     * @param $pathway_id int
     * @param int $start_position
     * @return array|false
     * @throws Exception
     */
    public function duplicateStepsForPathway(int $pathway_id, int $start_position = 0)
    {
        $step_json = array();
        foreach ($this->default_steps as $step) {
            $new_step = $step->cloneStepForPathway($pathway_id, array(), true, $start_position);
            if (!$new_step) {
                return false;
            }
            $step_json[] = $new_step->toJSON();
            if ($start_position !== 0) {
                $start_position++;
            }
        }
        return $step_json;
    }

    /**
     * @throws JsonException
     */
    public function duplicatePathwayTypeSteps(int $pathway_id)
    {
        $step_json = array();
        foreach ($this->default_steps as $step) {
            $new_step = $step->cloneStepForPathwayType($pathway_id);
            if (!$new_step) {
                return false;
            }
            $step_json[] = $new_step->toJSON();
        }
        return $step_json;
    }

    /**
     * Add a step to a queue relevant to its current status.
     * @param PathwayTypeStep $step The step to add
     * @return bool True if the step was successfully enqueued; otherwise false.
     * @throws Exception
     */
    public function enqueue(PathwayTypeStep $step): bool
    {
        $end_position = Yii::app()->db->createCommand()
            ->select('MAX(`order`)')
            ->from('pathway_type_step')
            ->where('pathway_type_id = :id')
            ->bindValues([':id' => $this->id])
            ->queryScalar();
        $step->order = $end_position + 1;
        return $step->save();
    }

    /**
     * @param PathwayTypeStep $step The step to enqueue.
     * @param int $position The position in the requested steps queue to place the step
     * @return bool True if enqueued successfully; otherwise false.
     * @throws Exception
     */
    public function enqueueAtPosition(PathwayTypeStep $step, int $position): bool
    {
        $start_position = $position;
        $step->order = $start_position;
        foreach ($this->default_steps as $existing_step) {
            if ($existing_step->order >= $position) {
                $existing_step->order = ++$start_position;
                $existing_step->save();
            }
        }
        return $step->save();
    }
}
