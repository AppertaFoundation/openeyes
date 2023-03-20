<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "et_ophtroperationnote_procedurelist".
 *
 * The followings are the available columns in table 'et_ophtroperationnote_procedurelist':
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property int $booking_event_id
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property Eye $eye
 * @property Procedure[] $procedures
 */
class Element_OphTrOperationnote_ProcedureList extends Element_OpNote
{
    use HasFactory;

    public $service;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphTrOperationnote_ProcedureList the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtroperationnote_procedurelist';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, booking_event_id', 'safe'),
            array('eye_id, procedures', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='" . get_class($this) . "'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'procedures' => array(self::MANY_MANY, 'Procedure', 'ophtroperationnote_procedurelist_procedure_assignment(procedurelist_id, proc_id)', 'order' => 'display_order ASC'),
            'procedure_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_ProcedureListProcedureAssignment', 'procedurelist_id', 'order' => 'display_order ASC'),
            'bookingEvent' => array(self::BELONGS_TO, 'Event', 'booking_event_id'),
        );
    }

    protected function beforeSave()
    {
        if (!$this->booking_event_id) {
            $this->booking_event_id = null;
        }

        return parent::beforeSave();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'eye_id' => 'Eye',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('eye_id', $this->eye_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getPrefillableAttributeSet()
    {
        return [
            'eye_id',
            'procedures' => 'id',
        ];
    }

    /**
     * Update the procedures for this element with the given ids.
     *
     * @param $procedure_ids
     *
     * @throws Exception
     */
    public function updateProcedures($procedure_ids)
    {
        $current_procedures = array();
        foreach ($this->procedure_assignments as $pa) {
            $current_procedures[$pa->proc_id] = $pa;
        }

        foreach ($procedure_ids as $i => $proc_id) {
            $display_order = $i + 1;
            if (isset($current_procedures[$proc_id])) {
                $procedure_assignment = $current_procedures[$proc_id];
                if ($procedure_assignment->display_order != $display_order) {
                    $procedure_assignment->display_order = $display_order;
                    if (!$procedure_assignment->save()) {
                        throw new Exception('Unable to save procedure assignment');
                    }
                }
                unset($current_procedures[$proc_id]);
            } else {
                $procedure_assignment = new OphTrOperationnote_ProcedureListProcedureAssignment();
                $procedure_assignment->procedurelist_id = $this->id;
                $procedure_assignment->proc_id = $proc_id;
                $procedure_assignment->display_order = $display_order;
                if (!$procedure_assignment->save()) {
                    throw new Exception('Unable to save procedure assignment');
                }
            }
        }

        // delete remaining current procedures
        foreach ($current_procedures as $pa) {
            if (!$pa->delete()) {
                throw new Exception('Unable to delete procedure assignment: ' . print_r($pa->getErrors(), true));
            }
        }
    }

    /**
     * For new records, set the episode status and (if relevant) the operation status.
     *
     * @FIXME: abstraction of the episode and operation statuses
     *
     * @throws Exception
     */
    protected function afterSave()
    {
        if ($this->getIsNewRecord()) {
            $this->event->episode->episode_status_id = 4;

            if (!$this->event->episode->save()) {
                throw new Exception('Unable to change episode status for episode ' . $this->event->episode->id);
            }

            if ($this->booking_event_id && $api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
                $api->setOperationStatus($this->booking_event_id, 'Completed');
            }
        }

        return parent::afterSave();
    }

    /**
     * Customises eye options for this element.
     *
     * @FIXME: Shouldn't be touching the session stuff here if we can help it. Can we operate of the episode firm?
     *
     * @param $table
     *
     * @return array
     */
    public function getEyeOptions()
    {
        $event_type = EventType::model()->find('class_name=?', array('OphTrOperationnote'));
        $element_type = ElementType::model()->find('event_type_id=? and class_name=?', array($event_type->id, 'Element_OphTrOperationnote_ProcedureList'));

        $criteria = new CDbCriteria();
        $criteria->order = 't.display_order asc';

        if (SettingMetadata::model()->getSetting('opbooking_disable_both_eyes') == 'on') {
            $criteria->addCondition('t.id != :three');
            $criteria->params[':three'] = 3;
        }

        return CHtml::listData(Eye::model()->findAll($criteria), 'id', 'name');
    }

    public function getContainer_form_view()
    {
        return false;
    }

    protected function applyComplexData($data, $index): void
    {
        $procs = array();
        if (isset($data['Procedures_procs'])) {
            foreach ($data['Procedures_procs'] as $proc_id) {
                $procs[] = Procedure::model()->findByPk($proc_id);
            }
        } elseif (isset($data[$this->elementType->class_name]['procedures'])) {
            foreach ($data[$this->elementType->class_name]['procedures'] as $proc_id) {
                $procs[] = Procedure::model()->findByPk($proc_id);
            }
        }
        $this->procedures = $procs;
    }
}
