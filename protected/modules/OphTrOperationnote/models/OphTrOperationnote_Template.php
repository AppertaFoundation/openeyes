<?php

/**
 * This is the model class for table "ophtroperationnote_template".
 *
 * The followings are the available columns in table 'ophtroperationnote_template':
 * @property integer $event_template_id
 * @property integer $proc_set_id
 * @property string $template_data
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property ProcedureSet $procedure_set
 * @property EventTemplate $event_template
 */
class OphTrOperationnote_Template extends BaseEventTemplate
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtroperationnote_template';
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BaseEventTemplate the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_template_id, proc_set_id, template_data', 'required'),
            array('event_template_id, proc_set_id', 'numerical', 'integerOnly' => true),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'event_template_id, proc_set_id, template_data, last_modified_user_id, last_modified_date, created_user_id, created_date',
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
            'procedure_set' => array(self::BELONGS_TO, 'ProcedureSet', 'proc_set_id'),
            'event_template' => array(self::BELONGS_TO, 'EventTemplate', 'event_template_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'event_template_id' => 'Event Template',
            'proc_set_id' => 'Procedure Set',
            'template_data' => 'Template Data',
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

        $criteria->compare('event_template_id', $this->event_template_id);
        $criteria->compare('proc_set_id', $this->proc_set_id);
        $criteria->compare('template_data', $this->template_data, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function setupAndSave($event, $event_template, $template_json): bool
    {
        $procedure_list = Element_OphTrOperationnote_ProcedureList::model()->find('event_id = ?', [$event->id]);

        $procedure_set = ProcedureSet::findForProcedureList($procedure_list->id);

        if ($procedure_set === null) {
            $procedure_set = new ProcedureSet();

            if (!$procedure_set->save()) {
                return false;
            }

            foreach ($procedure_list->procedures as $procedure) {
                $assignment = new ProcedureSetAssignment();

                $assignment->proc_set_id = $procedure_set->id;
                $assignment->proc_id = $procedure->id;

                if (!$assignment->save()) {
                    return false;
                }
            }
        }

        $this->event_template_id = $event_template->id;
        $this->proc_set_id = $procedure_set->id;
        $this->template_data = $template_json;

        return $this->save();
    }

    public function getUpdateStatus($event, $old_data, $new_data, $data_has_changed)
    {
        $procedure_list = Element_OphTrOperationnote_ProcedureList::model()->find('event_id = ?', [$event->id]);

        $compare_names = static function($ps, $pl) {
            return strcmp($ps->term, $pl->term);
        };

        $procs_diff = array_udiff($this->procedure_set->procedures, $procedure_list->procedures, $compare_names);

        if (count($this->procedure_set->procedures) !== count($procedure_list->procedures) || count($procs_diff) > 0) {
            return EventTemplate::UPDATE_CREATE_ONLY;
        }

        return $data_has_changed ? EventTemplate::UPDATE_OR_CREATE : EventTemplate::UPDATE_UNNEEDED;
    }
}
