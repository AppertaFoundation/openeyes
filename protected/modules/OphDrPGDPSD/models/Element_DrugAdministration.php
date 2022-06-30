<?php

use OEModule\OphCiExamination\widgets\DrugAdministration;
use OEModule\OphCiExamination\models\Element_OphCiExamination_DrugAdministration;

/**
 * This is the model class for table "et_drug_administration".
 *
 * The followings are the available columns in table 'et_drug_administration':
 * @property integer $id
 * @property int $event_id
 *
 * The followings are the available model relations:
 * @property Event $event
 */
use OEModule\OphCiExamination\models\BaseMedicationElement;
use OEModule\OphCiExamination\models\traits\CustomOrdering;

class Element_DrugAdministration extends BaseMedicationElement
{
    use CustomOrdering;
    public $do_not_save_entries = true;
    protected $auto_update_relations = true;
    public $check_for_duplicate_entries = false;
    public $patient;

    public $widgetClass = DrugAdministration::class;

    public static $entry_class = Element_DrugAdministration_record::class;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_drug_administration';
    }

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            array(
                "AllergicDrugEntriesBehavior" => array(
                    "class" => "application.behaviors.AllergicDrugEntriesBehavior",
                ),
            )
        );
    }

    public function getContainer_form_view()
    {
        return false;
    }
    public function getContainer_update_view()
    {
        return '//patient/element_container_form';
    }

    public function getContainer_create_view()
    {
        return '//patient/element_container_form';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('assignments', 'required', 'message' => 'At least one assignment must be recorded, or the element should be removed.'),
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date, type, event_id, assignments', 'safe'),
            // The following rule is used by search().
            array('id, event_id', 'safe', 'on'=>'search'),
            array('type', 'default', 'value' => $this::getType(), 'on' => 'insert'),
        );
    }

    public static function getType()
    {
        return 'pgdpsd';
    }

    protected function instantiate($attributes)
    {
        if (strtolower($attributes['type']) === 'exam') {
            $class = Element_OphCiExamination_DrugAdministration::class;
        } else {
            $class = get_class($this);
        }
        return new $class(null);
    }

    public function getAssignmentRelations()
    {
        return array(
            'assignments' => array(self::MANY_MANY, 'OphDrPGDPSD_Assignment', 'et_drug_administration_assignments(element_id, assignment_id)'),
        );
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function save($runValidation = true, $attributes = null, $allow_overriding = false)
    {
        $original = $this->assignments;
        foreach ($this->assignments as $key => $assignment) {
            if(!$assignment->active){
                continue;
            }
            if ($assignment->create_wp) {
                $site_id = Yii::app()->session->get('selected_site_id');
                $firm_id = Yii::app()->session->get('selected_firm_id');
                $firm = Firm::model()->findByPk($firm_id);
                $subspecialty = $firm->subspecialty ?: null;
                $unbooked_worklist_manager = new UnbookedWorklist();
                $unbooked_worklist = $unbooked_worklist_manager->createWorklist(new DateTime(), $site_id, $subspecialty->id);
                $wl_manager = new WorklistManager();
                $worklist_patient = $wl_manager->getWorklistPatient($unbooked_worklist, $this->event->episode->patient);

                if (!$worklist_patient) {
                    $worklist_patient = $wl_manager->addPatientToWorklist($this->event->episode->patient, $unbooked_worklist, new DateTime());
                }
                if (!$worklist_patient->pathway) {
                    $worklist_patient->worklist->worklist_definition->pathway_type->instancePathway($worklist_patient);
                    $worklist_patient->refresh();
                }
                $assignment->visit_id = $worklist_patient->id;
            }
            $assignment->save();
            // If a new psd is attached to a worklist patient, a new pathway step will be needed as well
            if ($assignment->worklist_patient && $assignment->worklist_patient->pathway) {
                // find out all existing drug admin pathway step associated with corresponding pathway
                $da_steps = PathwayStep::model()->findAll(
                    'pathway_id = :pathway_id AND short_name = "drug admin"',
                    array(':pathway_id' => $assignment->worklist_patient->pathway->id)
                );
                // try to find the pathway steps related to current psd
                $matched_da_steps = array_filter($da_steps, static function ($da_step) use ($assignment) {
                    return (int)$da_step->getState('assignment_id') === (int)$assignment->id;
                });
                // if no matched found, create a new pathway step for current psd
                if (!$matched_da_steps) {
                    Yii::app()->event->dispatch('psd_created', array(
                        'step_type' => PathwayStepType::model()->find('short_name = \'drug admin\''),
                        'worklist_patient_id' => $assignment->worklist_patient->id,
                        'initial_state' => [
                            'preset_id' => $assignment->pgdpsd ? $assignment->pgdpsd->id : null,
                            'assignment_id' => $assignment->id,
                        ],
                        'raise_event' => false
                    ));
                }
            }
        }
        $this->assignments = $original;
        return parent::save($runValidation, $attributes, $allow_overriding);
    }

    public function getEntryRelations()
    {
        $rules = array(
            'entries' => array(
                self::HAS_MANY,
                Element_DrugAdministration_record::class,
                array('id' => 'event_id'),
                'through' => 'event',
                'on' => "usage_type = '" . Element_DrugAdministration_record::getUsageType() . "' AND usage_subtype = '" . Element_DrugAdministration_record::getUsageSubtype() . "' ",
                'order' => 'entries.start_date DESC, entries.end_date DESC, entries.last_modified_date'
            ),
            'visible_entries' => array(
                self::HAS_MANY,
                Element_DrugAdministration_record::class,
                array('id' => 'event_id'),
                'through' => 'event',
                'on' => "hidden = 0 AND usage_type = '" . Element_DrugAdministration_record::getUsageType() . "' AND usage_subtype = '" . Element_DrugAdministration_record::getUsageSubtype() . "' ",
                'order' => 'visible_entries.start_date DESC, visible_entries.end_date DESC, visible_entries.last_modified_date'
            ),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
        );
        return array_merge($rules, $this->getAssignmentRelations());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'type' => 'Type',
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
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('type', $this->type, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_DrugAdministration the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function afterSave()
    {
        $pgdpsd_api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        foreach ($this->assignments as $assignment) {
            foreach ($assignment->assigned_meds as $med) {
                if ($med->administered && !$med->event_entry) {
                    $med = $pgdpsd_api->setMedEventEntry($med, $this);
                } elseif (!$med->administered && $med->event_entry) {
                    $event_entry = $med->event_entry;
                    $med->administered_id = null;
                    $med->administered = 0;
                    $med->administered_time = null;
                    $med->administered_by = null;
                }
                $med->save();
            }
        }
        parent::afterSave();
    }

    public function updateAssignmentList($assignment)
    {
        $existing_assignments = $this->assignments;
        foreach ($existing_assignments as $existing_assignment) {
            if ($existing_assignment->id === $assignment->id) {
                return;
            }
        }
        $existing_assignments[] = $assignment;
        $this->assignments = $existing_assignments;
    }

    public function loadFromExisting($element)
    {
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function softDelete()
    {
        $new_assignments = array();
        foreach ($this->assignments as $assignment) {
            $duplicate = new OphDrPGDPSD_Assignment();
            $assignment_attrs = $assignment->attributes;
            unset($assignment_attrs['id'], $assignment_attrs['comment_id']);
            $duplicate->attributes = $assignment_attrs;
            $duplicate->assigned_meds = array_map(static function ($med) {
                $med_attrs = $med->attributes;
                unset($med_attrs['id']);
                return $med_attrs;
            }, $assignment->assigned_meds);
            $duplicate->setConfirmed(true);
            $duplicate->active = 0;
            $duplicate->save();
            if ($assignment->comment) {
                $duplicate_comment = new OphDrPGDPSD_Assignment_Comment();
                $duplicate_comment->attributes = $assignment->comment->attributes;
                $duplicate->saveComment($duplicate_comment);
            }
            $new_assignments[] = $duplicate;
        }
        $this->assignments = $new_assignments;
        $this->save();
    }

    // in the function setAndValidateElementsFromData in BaseEventTypeController
    // the element will be validate and this action will clear all the errors
    // the override below will retain the validation error from lower level, like assignments, assignments medications etc.
    public function validate($attributes = null, $clearErrors = true)
    {
        $errors = $this->getErrors();
        parent::validate();
        $this->addErrors($errors);
        return !$this->hasErrors();
    }
}
