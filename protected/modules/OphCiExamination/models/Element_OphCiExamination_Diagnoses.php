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
 * @property \Event $event
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
     * @return string the static model class
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
                array('diagnoses' ,'disorderIdIsSet', 'required'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id', 'safe', 'on' => 'search'),
        );
    }

    public function disorderIdIsSet($attributeName)
    {
        foreach ($this->$attributeName as $key => $value) {
            if (!$value->disorder_id) {
                $this->addError($attributeName, "Diagnosis cannot be empty");
            }
        }
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
     *      'disorder_id' => integer,
     *      'eye_id' => \Eye::LEFT|\Eye::RIGHT|\Eye::BOTH,
     *      'principal' => boolean
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
            $curr_by_disorder_id[$cd->id] = $cd;
        }
        foreach ($update_disorders as $u_disorder) {
            if (!isset($curr_by_disorder_id[$u_disorder['id']])) {
                $curr = new OphCiExamination_Diagnosis();
                $curr->element_diagnoses_id = $this->id;
                $curr->disorder_id = $u_disorder['disorder_id'];
                $curr->date = $u_disorder['date'];
            } else {
                $curr = @$curr_by_disorder_id[$u_disorder['id']];
                unset($curr_by_disorder_id[$u_disorder['id']]);
            }
            if ($curr->eye_id != $u_disorder['eye_id']
                || $curr->principal != $u_disorder['principal'] || $curr->date != $u_disorder['date']) {
                // need to update & save
                $curr->eye_id = $u_disorder['eye_id'];
                $curr->principal = $u_disorder['principal'];
                $curr->date = $u_disorder['date'];
                if (!$curr->save()) {
                    throw new \Exception('save failed'.print_r($curr->getErrors(), true));
                };
            }
            if ($u_disorder['principal']) {
                $this->event->episode->setPrincipalDiagnosis($u_disorder['disorder_id'], $u_disorder['eye_id'], $u_disorder['date']);
            } else {
                //add a secondary diagnosis
                // Note that this may be creating duplicate diagnoses, but that is okay as the dates on them will differ
                $this->event->episode->patient->addDiagnosis($u_disorder['disorder_id'],
                    $u_disorder['eye_id'], substr($u_disorder['date'], 0, 10));
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
     * @throws \CDbException
     */
    protected function beforeDelete()
    {
        foreach ($this->diagnoses as $diagnosis) {
            $diagnosis->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * @return string html table of daignoses and further findings
     *  if either the diagnosis or the finding has a letter macro text, it will replace the usual term
     */
    public function getLetter_string() {
        $table_vals = array();
        $subspecialty = null;
        if (isset(\Yii::app()->session['selected_firm_id']) && \Yii::app()->session['selected_firm_id'] !== null) {
            $firm = \Firm::model()->findByPk(\Yii::app()->session['selected_firm_id']);
            $subspecialty = $firm->serviceSubspecialtyAssignment->subspecialty;
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('element_diagnoses_id=:ed');
        $criteria->params[':ed'] = $this->id;

        //Get all the diagnoses and their default terminology
        foreach (OphCiExamination_Diagnosis::model()->findAll($criteria) as $diagnosis) {
            if ($diagnosis->disorder) {
                $table_vals[] = array(
                    'disorder_id' => $diagnosis->disorder->id,
                    'principal' => $diagnosis->principal,
                    'date' => \Helper::convertDate2NHS($diagnosis->date),
                    'laterality' => mb_strtoupper($diagnosis->eye->adjective) . ' ',
                    'term' => $diagnosis->disorder->term
                );
            }
        }

        //check disorders for alternate texts
        if ($subspecialty) {
            $disorder_alt_texts = $this->getSecondaryDiagnosisLetterTexts(
                $subspecialty,
                array_column($table_vals, 'disorder_ids')
            );
            //replace text
            foreach ($table_vals as $id => $disorder) {
                //principal diagnoses don't get their text changed
                if ($disorder['principal'] == 1) {
                    continue;
                }
                if (!isset($disorder['disorder_id'])) {
                    continue;
                }

                if (array_key_exists($disorder['disorder_id'], $disorder_alt_texts)) {
                    $table_vals[$id]['term'] = $disorder_alt_texts[$disorder['disorder_id']]['text'];
                }
            }
        }

        //Get further findings from examination
        if ($et_findings = Element_OphCiExamination_FurtherFindings::model()
            ->find('event_id=?', array($this->event_id))
        ) {
            foreach (OphCiExamination_FurtherFindings_Assignment::model()
                         ->findAll('element_id=?', array($et_findings->id)
                         ) as $finding
            ) {
                $table_vals[] = array(
                    'finding_id' => $finding->id,
                    'date' => \Helper::convertDate2NHS($this->event->event_date),
                    'laterality' => '',
                    'term' => $finding->description
                );
                $finding_ids[] = $finding->finding_id;
                $findings[] = $finding;
            }
        }

        //check further findings for alternate texts
        if ($subspecialty) {
            $finding_alt_texts = $this->getSecondaryDiagnosisFindingLetterTexts(
                $subspecialty,
                array_column($table_vals, 'finding_ids')
            );
            //replace text
            foreach ($table_vals as $id => $finding) {
                if (!isset($finding['finding_id'])) {
                    continue;
                }

                if (array_key_exists($finding['finding_id'], $finding_alt_texts)) {
                    $table_vals[$id]['term'] = $finding_alt_texts[$finding['finding_id']]['text'];
                }
            }
        }

        ob_start();
        ?>
        <table class="standard">
            <tbody>
            <?php
            foreach ($table_vals as $val) :?>
                <tr>
                    <td><?= $val['date'] ?></td>
                    <td><?= @$val['principal'] ? 'Principal: ' : '' ?><?= $val['laterality'] . $val['term'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        $text = ob_get_clean();
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
     * Finds alternate letter macro text for secondary disorders depending on subspecialty
     * @param $subspecialty \Subspecialty
     * @param $disorder_ids
     * @return array found alternate texts, indexed by disorder_id
     *
     * @var $secto_disorder \SecondaryToCommonOphthalmicDisorder
     */
    public function getSecondaryDiagnosisLetterTexts($subspecialty, $disorder_ids)
    {
        $result = array();
        foreach ($disorder_ids as $disorder_id) {
            foreach (\SecondaryToCommonOphthalmicDisorder::model()
                         ->with('parent')
                         ->findAll(
                             't.disorder_id=? and parent.subspecialty_id=?',
                             array($disorder_id, $subspecialty->id)
                         ) as $secto_disorder
            ) {
                if ($secto_disorder->letter_macro_text == null || $secto_disorder->letter_macro_text == "") {
                    continue;
                }
                $result[$disorder_id] = $secto_disorder->letter_macro_text;
            }
        }

        return $result;
    }

    /**
     * Finds alternate letter macro text for findings depending on subspecialty
     * @param $subspecialty \Subspecialty
     * @param $finding_ids array(int)
     * @return array found alternate text, indexed by finding_id
     *
     * @var $secto_disorder \SecondaryToCommonOphthalmicDisorder
     */
    public function getSecondaryDiagnosisFindingLetterTexts($subspecialty, $finding_ids)
    {
        $result = array();
        foreach ($finding_ids as $finding_id) {
            foreach (\SecondaryToCommonOphthalmicDisorder::model()
                         ->with('parent')
                         ->findAll(
                             't.finding_id=? and parent.subspecialty_id=?',
                             array($finding_id, $subspecialty->id)
                         ) as $secto_disorder
            ) {
                if ($secto_disorder->letter_macro_text == null || $secto_disorder->letter_macro_text == "") {
                    continue;
                }
                $result[$finding_id] = $secto_disorder->letter_macro_text;
            }
        }

        return $result;
    }

    /**
     * Ensure a principal diagnosis is set for the episode.
     */
    public function afterValidate()
    {
        if (count($this->diagnoses)) {
            $principal = false;

            $validator = new \OEFuzzyDateValidator();

            foreach ($this->diagnoses as $key => $diagnosis) {
                if ($diagnosis->principal) {
                    $principal = true;
                }

                $term = isset($diagnosis->disorder)  ? $diagnosis->disorder->term : "($key)";
                if (!$diagnosis->eye_id) {
                    // without this OE tries to perform a save / or at least run the saveComplexAttributes_Element_OphCiExamination_Diagnoses()
                    // where we need to have an eye_id - probably this need further investigation and refactor
                    $this->addError('diagnoses', $term . ': Eye is required');

                    //this sets the error for the actual model, and checked manually in 'form_Element_OphCiExamination_Diagnoses.php'
                    // to set the proper error highlighting
                    $diagnosis->addError('diagnoses', $term . ': Eye is required');
                }

                $validator->validateAttribute($diagnosis, 'date');

                //dirty hack here to set the correct error for the date
                $_date_error = $diagnosis->getError('date');
                if ($_date_error) {
                    $this->addError('diagnoses', $term . ': ' . $_date_error);
                    $diagnosis->clearErrors('date');
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

            $have_further_findings = false;

            foreach ($controller->getElements() as $element) {
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

    public function getViewTitle()
    {
        return "Eye Diagnoses";
    }

    public function getTileSize($action)
    {
        return $action === 'view' || $action === 'createImage' ? 1 : null;
    }

    public function getDisplayOrder($action)
    {
        return $action == 'view' ? 10 : parent::getDisplayOrder($action);
    }
}
