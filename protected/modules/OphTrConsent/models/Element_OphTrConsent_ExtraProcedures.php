<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
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

/**
 * This is the model class for table "et_ophtrconsent_procedure".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property int $anaesthetic_type_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property Eye $eye
 * @property EtOphtrconsentProcedureProceduresProcedures $procedures
 * @property AnaestheticType[] $anaesthetic_type
 * @property EtOphtrconsentProcedureAddProcsAddProcs $add_procss
 */
class Element_OphTrConsent_ExtraProcedures extends BaseEventTypeElement
{
    public $service;
    protected $errorExceptions = array(
    'Element_OphTrConsent_Extraprocedures_procedures' => 'typeExtraprocedures',
    );

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'et_ophtrconsent_extraprocedure';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, anaesthetic_type_id, booking_event_id', 'safe'),
            array('eye_id, anaesthetic_type_id, ', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id, anaesthetic_type_id, ', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),

            //Element_OphTrConsent_Procedure
            'anaesthetic_type_assignments' => array(self::HAS_MANY, 'OphTrConsent_Procedure_AnaestheticType', 'et_ophtrconsent_procedure_id'),
            'anaesthetic_type' => array(self::HAS_MANY, 'AnaestheticType', 'anaesthetic_type_id',
                'through' => 'anaesthetic_type_assignments', ),


            'procedure_assignments' => array(self::HAS_MANY, 'EtOphtrconsentProcedureProceduresProcedures', 'element_id'),
            'procedures' => array(self::HAS_MANY, 'Procedure', 'proc_id',
                'through' => 'procedure_assignments', ),
            'additionalprocedure_assignments' => array(self::HAS_MANY, 'EtOphtrconsentProcedureAddProcsAddProcs', 'element_id'),
            'additional_procedures' => array(self::HAS_MANY, 'Procedure', 'proc_id',
                'through' => 'additionalprocedure_assignments', ),
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
            'eye_id' => 'Eye',
            'procedures' => 'Procedures',
            'anaesthetic_type_id' => 'Anaesthetic type',
            'otherproc' => 'Additional procedures',
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
        $criteria->compare('eye_id', $this->eye_id);
        $criteria->compare('procedures', $this->procedures);
        $criteria->compare('anaesthetic_type_id', $this->anaesthetic_type_id);
        $criteria->compare('add_procs', $this->add_procs);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
    protected function afterValidate()
    {
        if (empty($_POST['Procedures_procedures'])) {
            $this->addError('procedures', 'At least one procedure must be entered');
        }
    }
    //TODO: get POST handling out of here.
    protected function afterSave()
    {
        foreach ($_POST['Procedures_procedures'] as $procedure_id) {
            if (!EtOphtrconsentProcedureProceduresProcedures::model()->find('element_id=? and proc_id=?', array($this->id, $procedure_id))) {
                $p = new EtOphtrconsentProcedureProceduresProcedures();
                $p->element_id = $this->id;
                $p->proc_id = $procedure_id;
                if (!$p->save()) {
                    throw new Exception('Unable to save procedure item: '.print_r($p->getErrors(), true));
                }
            }
        }

        foreach (EtOphtrconsentProcedureProceduresProcedures::model()->findAll('element_id=?', array($this->id)) as $p) {
            if (!in_array($p->proc_id, $_POST['Procedures_procedures'])) {
                if (!$p->delete()) {
                    throw new Exception('Unable to delete procedure item: '.print_r($p->getErrors(), true));
                }
            }
        }

        if (isset($_POST['Procedures_additional'])) {
            foreach ($_POST['Procedures_additional'] as $procedure_id) {
                if (!EtOphtrconsentProcedureAddProcsAddProcs::model()->find('element_id=? and proc_id=?', array($this->id, $procedure_id))) {
                    $p = new EtOphtrconsentProcedureAddProcsAddProcs();
                    $p->element_id = $this->id;
                    $p->proc_id = $procedure_id;
                    if (!$p->save()) {
                        throw new Exception('Unable to save additional procedure item: '.print_r($p->getErrors(), true));
                    }
                }
            }

            foreach (EtOphtrconsentProcedureAddProcsAddProcs::model()->findAll('element_id=?', array($this->id)) as $p) {
                if (!in_array($p->proc_id, $_POST['Procedures_additional'])) {
                    if (!$p->delete()) {
                        throw new Exception('Unable to delete additional procedure item: '.print_r($p->getErrors(), true));
                    }
                }
            }
        } else {
            foreach (EtOphtrconsentProcedureAddProcsAddProcs::model()->findAll('element_id=?', array($this->id)) as $p) {
                if (!$p->delete()) {
                    throw new Exception('Unable to delete additional procedure item: '.print_r($p->getErrors(), true));
                }
            }
        }

        return parent::afterSave();
    }

    /**
     * @param string|array $code
     * @return bool
     */
    public function hasAnaestheticTypeByCode($code)
    {
        if (!is_array($code)) {
            $code = array($code);
        }
        return count(array_filter(
            $this->anaesthetic_type,
            function ($a_type) use ($code) {
                return in_array((string)$a_type, $code);
            }
        )) > 0;
    }
}
