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

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_diagnoses". It's worth noting that this Element was originally
 * designed to provide a shortcut interface to setting patient diagnoses. Recording the specifics in the element as well
 * is almost incidental. It is possible that this will become redundant in a future version of OE.
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 *
 * @property Event $event
 * @property OphCiExamination_Diagnosis[] $diagnoses
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_Diagnoses extends \BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
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
        return 'et_ophciexamination_diagnoses';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                //array('diagnoses', 'required'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id', 'safe', 'on' => 'search'),
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
                'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'diagnoses' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Diagnosis', 'element_diagnoses_id',
                    'order' => 'principal desc',
                ),
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
                'disorder_id' => 'Disorder',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('eye_id', $this->eye_id);
        $criteria->compare('disorder_id', $this->disorder_id);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Update the diagnoses for this element using a hash structure of
     * [{
     * 		'disorder_id' => integer,
     * 		'eye_id' => \Eye::LEFT|\Eye::RIGHT|\Eye::BOTH,
     * 		'principal' => boolean
     * }, ... ].
     *
     * @param $update_disorders
     *
     * @throws \Exception
     */
    public function updateDiagnoses($update_disorders)
    {
        $current_diagnoses = OphCiExamination_Diagnosis::model()->findAll('element_diagnoses_id=?', array($this->id));
        $curr_by_disorder_id = array();
        $secondary_disorder_ids = array();

        foreach ($current_diagnoses as $cd) {
            $curr_by_disorder_id[$cd->disorder_id] = $cd;
        }

        foreach ($update_disorders as $u_disorder) {
            if (!$curr = @$curr_by_disorder_id[$u_disorder['disorder_id']]) {
                $curr = new OphCiExamination_Diagnosis();
                $curr->element_diagnoses_id = $this->id;
                $curr->disorder_id = $u_disorder['disorder_id'];
            } else {
                unset($curr_by_disorder_id[$u_disorder['disorder_id']]);
            }
            if ($curr->eye_id != $u_disorder['eye_id']
                || $curr->principal != $u_disorder['principal']) {
                // need to update & save
                $curr->eye_id = $u_disorder['eye_id'];
                $curr->principal = $u_disorder['principal'];
                if (!$curr->save()) {
                    throw new \Exception('save failed'.print_r($curr->getErrors(), true));
                };
            }
            if ($u_disorder['principal']) {
                $this->event->episode->setPrincipalDiagnosis($u_disorder['disorder_id'], $u_disorder['eye_id']);
            } else {
                //add a secondary diagnosis
                // Note that this may be creating duplicate diagnoses, but that is okay as the dates on them will differ
                $this->event->episode->patient->addDiagnosis($u_disorder['disorder_id'],
                    $u_disorder['eye_id'], substr($this->event->created_date, 0, 10));
                // and track
                $secondary_disorder_ids[] = $u_disorder['disorder_id'];
            }
        }

        // remove any current diagnoses no longer needed
        foreach ($curr_by_disorder_id as $curr) {
            if (!$curr->delete()) {
                throw new \Exception('Unable to remove old disorder');
            };
        }

        // ensure secondary diagnoses are consistent
        // FIXME: ongoing discussion as to whether we should be removing diagnosis from the patient here
        // particularly if this is a save of an older examination record.
        foreach (\SecondaryDiagnosis::model()->findAll('patient_id=?', array($this->event->episode->patient_id)) as $sd) {
            if ($sd->disorder->specialty && $sd->disorder->specialty->code == 130) {
                if (!in_array($sd->disorder_id, $secondary_disorder_ids)) {
                    $this->event->episode->patient->removeDiagnosis($sd->id);
                }
            }
        }
    }

    /**
     * Gets the common ophthalmic disorders for the given firm.
     *
     * @param int $firm_id
     *
     * @return array
     * @throws \CException
     */
    public function getCommonOphthalmicDisorders($firm_id)
    {
        if (empty($firm_id)) {
            throw new \CException('Firm is required');
        }
        $firm = \Firm::model()->findByPk($firm_id);
        if ($firm) {
            return \CommonOphthalmicDisorder::getListByGroupWithSecondaryTo($firm);
        }
    }

    /**
     * Delete the related diagnoses for this element.
     *
     * @return bool
     */
    protected function beforeDelete()
    {
        foreach ($this->diagnoses as $diagnosis) {
            $diagnosis->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * @return string
     */
    public function getLetter_string()
    {
        $text = '';

        $findings = array();
        $finding_ids = array();

        if ($et_findings = Element_OphCiExamination_FurtherFindings::model()->find('event_id=?', array($this->event_id))) {
            foreach (OphCiExamination_FurtherFindings_Assignment::model()->findAll('element_id=?', array($et_findings->id)) as $finding) {
                $finding_ids[] = $finding->finding_id;
                $findings[] = $finding;
            }
        }

        $disorders = array();
        $disorder_ids = array(
            'Left' => array(),
            'Right' => array(),
            'Both' => array(),
        );
        $is_principal = array();

        foreach (OphCiExamination_Diagnosis::model()->findAll('element_diagnoses_id=?', array($this->id)) as $diagnosis) {
            $disorder_ids[$diagnosis->eye->name][] = $diagnosis->disorder_id;
            $disorders[] = $diagnosis;

            $is_principal[$diagnosis->disorder_id] = $diagnosis->principal;
        }

        $secto_strings = array();

        $used_disorder_ids = array();
        $used_finding_ids = array();

        if (isset(\Yii::app()->session['selected_firm_id']) && \Yii::app()->session['selected_firm_id'] !== null) {
            $firm = \Firm::model()->findByPk(\Yii::app()->session['selected_firm_id']);
            $subspecialty = $firm->serviceSubspecialtyAssignment->subspecialty;

            foreach ($disorders as $disorder) {
                foreach (\SecondaryToCommonOphthalmicDisorder::model()->with('parent')->findAll('t.disorder_id=? and parent.subspecialty_id=?', array($disorder->disorder_id, $subspecialty->id)) as $secto_disorder) {
                    if ($secto_disorder->letter_macro_text) {
                        if ($secto_disorder->parent->disorder_id) {
                            if (in_array($secto_disorder->parent->disorder_id, $disorder_ids[$disorder->eye->name]) ||
                                    in_array($secto_disorder->parent->disorder_id, $disorder_ids['Both'])) {
                                $secto_strings[] = (($is_principal[$disorder->disorder_id] || $is_principal[$secto_disorder->parent->disorder_id]) ? '' : 'Secondary diagnosis: ').$disorder->eye->name.' '.$secto_disorder->letter_macro_text;
                                $used_disorder_ids[] = $disorder->disorder_id;
                                $used_disorder_ids[] = $secto_disorder->parent->disorder_id;
                            }
                        } elseif ($secto_disorder->parent->finding_id) {
                            if (in_array($secto_disorder->parent->finding_id, $finding_ids)) {
                                $secto_strings[] = ($is_principal[$disorder->disorder_id] ? '' : 'Secondary diagnosis: ').$disorder->eye->name.' '.$secto_disorder->letter_macro_text;
                                $used_disorder_ids[] = $disorder->disorder_id;
                                $used_finding_ids[] = $secto_disorder->parent->finding_id;
                            }
                        }
                    }
                }
            }

            foreach ($findings as $finding) {
                foreach (\SecondaryToCommonOphthalmicDisorder::model()->with('parent')->findAll('t.finding_id=? and parent.subspecialty_id=?', array($finding->finding_id, $subspecialty->id)) as $secto_disorder) {
                    if ($secto_disorder->letter_macro_text) {
                        if ($secto_disorder->parent->disorder_id) {
                            if ($eye = $this->getEyeForDisorder($secto_disorder->parent->disorder_id, $disorder_ids)) {
                                $secto_strings[] = ($is_principal[$secto_disorder->parent->disorder_id] ? '' : 'Secondary diagnosis: ').$eye.' '.$secto_disorder->letter_macro_text;
                                $used_disorder_ids[] = $secto_disorder->parent->disorder_id;
                                $used_finding_ids[] = $finding->finding_id;
                            }
                        }
                    }
                }
            }
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('element_diagnoses_id=:ed');
        $criteria->params[':ed'] = $this->id;
        $criteria->addCondition('principal=1');

        if (!empty($used_disorder_ids)) {
            $criteria->addNotInCondition('disorder_id', $used_disorder_ids);
        }

        if ($principal = OphCiExamination_Diagnosis::model()->find($criteria)) {
            $text .= 'Principal diagnosis: '.$principal->eye->adjective.' '.$principal->disorder->term."\n";
        }

        if (!empty($secto_strings)) {
            $text .= implode("\n", $secto_strings)."\n";
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('element_diagnoses_id=:ed');
        $criteria->params[':ed'] = $this->id;
        $criteria->addCondition('principal=0');

        if (!empty($used_disorder_ids)) {
            $criteria->addNotInCondition('disorder_id', $used_disorder_ids);
        }

        foreach (OphCiExamination_Diagnosis::model()->findAll($criteria) as $diagnosis) {
            if ($diagnosis->disorder) {
                $text .= 'Secondary diagnosis: '.$diagnosis->eye->adjective.' '.$diagnosis->disorder->term."\n";
            }
        }

        if ($ff = Element_OphCiExamination_FurtherFindings::model()->find('event_id=?', array($this->event_id))) {
            if ($string = $ff->getFurtherFindingsAssignedString($used_finding_ids)) {
                $text .= "Further Findings: $string\n";
            }
        }

        return $text;
    }

    public function getEyeForDisorder($disorder_id, $disorder_ids)
    {
        foreach ($disorder_ids as $eye => $disorder_list) {
            if (in_array($disorder_id, $disorder_list)) {
                return $eye;
            }
        }

        return;
    }

    /**
     * Ensure a principal diagnosis is set for the episode.
     */
    public function afterValidate()
    {
        if (count($this->diagnoses)) {
            $principal = false;

            foreach ($this->diagnoses as $diagnosis) {
                if ($diagnosis->principal) {
                    $principal = true;
                }
            }

            if (!$principal) {
                $this->addError('diagnoses', 'Principal diagnosis required.');
            }
        }

        // This isn't very nice but there isn't a clean alternative at the moment
        $controller = \Yii::app()->getController();

        if ($controller instanceof \BaseEventTypeController) {
            $et_diagnoses = \ElementType::model()->find('class_name=?', array('OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses'));

            $children = $controller->getChildElements($et_diagnoses);

            $have_further_findings = false;

            foreach ($controller->getChildElements($et_diagnoses) as $element) {
                if (\CHtml::modelName($element) == 'OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings') {
                    $have_further_findings = true;
                }
            }

            if (!$have_further_findings && !$this->diagnoses) {
                $this->addError('diagnoses', 'Please select at least one diagnosis.');
            }
        }

        parent::afterValidate();
    }

    public function getPrint_view()
    {
        return 'print_'.$this->getDefaultView();
    }
}
