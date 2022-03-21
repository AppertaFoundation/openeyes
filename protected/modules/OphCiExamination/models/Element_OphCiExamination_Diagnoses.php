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
    use traits\CustomOrdering;
    protected $default_view_order = 10;
    public $no_ophthalmic_diagnoses = false;

    protected $errorExceptions = [
            'OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses_diagnoses' => 'OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses_diagnoses_table'
    ];
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

    public function behaviors()
    {
        return array(
            'PatientLevelElementBehaviour' => 'PatientLevelElementBehaviour',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('diagnoses', 'checkForDuplicates'),
            array('diagnoses', 'disorderIdIsSet', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id', 'safe', 'on' => 'search'),
            array('no_ophthalmic_diagnoses_date', 'safe')
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

    public function checkForDuplicates($attribute, $params)
    {
        $entries_by_disorder_id = [];

        foreach ($this->diagnoses as $diagnosis) {
            $entries_by_disorder_id[$diagnosis->disorder_id][] = ['eye_id' => $diagnosis->eye_id, 'date' => $diagnosis->date];
        }

        foreach ($entries_by_disorder_id as $disorder_id => $disorders) {
            foreach ($disorders as $disorder) {
                $keys = array_keys($disorders, ['eye_id' => $disorder['eye_id'], 'date' => $disorder['date']]);
                if (count($keys) > 1) {
                    $duplicates = [];

                    foreach ($this->diagnoses as $key => $value) {
                        if ($value->disorder_id == $disorder_id && $value->eye_id === $disorder['eye_id'] && $value->date === $disorder['date']) {
                            $duplicates[] = $key;
                        }
                    }

                    foreach ($duplicates as $duplicate) {
                        $this->addError($attribute, "row $duplicate - You have duplicates for " . \Disorder::model()->findByPk($disorder_id)->term . " diagnosis. Each combination of diagnosis, eye side and date must be unique.");
                    }
                    break;
                }
            }
        }
    }

    /**
     * @param $attribute
     * @inheritdoc
     */
    protected function errorAttributeException($attribute, $message)
    {
        if ($attribute === \CHtml::modelName($this) . '_diagnoses') {
            if (preg_match('/(\d+)/', $message, $match) === 1) {
                return $attribute . '_entries_row_' . ($match[1]+1);
            }
        }
        return parent::errorAttributeException($attribute, $message);
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
        $disorder_to_update = [];
        $disorder_to_create = [];
        $added_diagnoses = [];
        $current_diagnoses = OphCiExamination_Diagnosis::model()->findAll('element_diagnoses_id=?', [$this->id]);

        foreach ($update_disorders as $ud) {
            if (isset($ud['id']) && $ud['id'] !== "") {
                $disorder_to_update[$ud['id']] = $ud;
            } else {
                $disorder_to_create[] = $ud;
            }
        }

        //delete and update ophciexamination_diagnosis entries
        foreach ($current_diagnoses as $cd) {
            if (!array_key_exists($cd->id, $disorder_to_update)) {
                if (!$cd->principal) {
                    $secondary_diagnosis = \SecondaryDiagnosis::model()->findByAttributes(['disorder_id' => $cd->disorder_id, 'patient_id' => $this->event->episode->patient->id]);
                    if (!$secondary_diagnosis) {
                        throw new \Exception("Unable to find secondary disorder linked to disorder $cd->disorder_id");
                    }
                    $this->event->episode->patient->removeDiagnosis($secondary_diagnosis->id);
                }
                if (!$cd->delete()) {
                    throw new \Exception('Unable to remove old disorder');
                }
            } else {
                $cd->eye_id = $disorder_to_update[$cd->id]['eye_id'];
                $cd->principal = $disorder_to_update[$cd->id]['principal'];
                $cd->date = $disorder_to_update[$cd->id]['date'];
                if (!$cd->save()) {
                    throw new \Exception('save failed' . print_r($cd->getErrors(), true));
                };
                $added_diagnoses[] = $cd;
            }
        }

        //merge ophciexamination_diagnosis entries if there is the same diagnosis with same date on different eyes otherwise create new one
        foreach ($disorder_to_create as $new_disorder) {
            $related_diagnosis = OphCiExamination_Diagnosis::model()->find('disorder_id=? and element_diagnoses_id=? and date=? and principal=?', [$new_disorder['disorder_id'], $this->id, $new_disorder['date'], $new_disorder['principal']]);
            if ($related_diagnosis) {
                if ($related_diagnosis->eye_id === intval($new_disorder['eye_id']) || $related_diagnosis->eye_id === \Eye::BOTH) {
                    $this->addError('disorder_id', 'Duplicate');
                } else {
                    $related_diagnosis->eye_id = \Eye::BOTH;
                    if (!$related_diagnosis->save()) {
                        throw new \Exception('save failed' . print_r($related_diagnosis->getErrors(), true));
                    };
                    $added_diagnoses[] = $related_diagnosis;
                }
            } else {
                $new_diagnosis = new OphCiExamination_Diagnosis();
                $new_diagnosis->element_diagnoses_id = $this->id;
                $new_diagnosis->disorder_id = $new_disorder['disorder_id'];
                $new_diagnosis->eye_id = $new_disorder['eye_id'];
                $new_diagnosis->date = $new_disorder['date'];
                $new_diagnosis->principal = $new_disorder['principal'];
                if (!$new_diagnosis->save()) {
                    throw new \Exception('Unable to save old secondary disorder');
                }
                $added_diagnoses[] = $new_diagnosis;
            }
        }

        if ($this->isAtTip()) {
            //delete SecondaryDiagnosis entries that are removed in a new examination.
            foreach ($this->event->episode->patient->ophthalmicDiagnoses as $secondary_diagnosis) {
                if (array_search($secondary_diagnosis->disorder_id, array_column(array_merge($disorder_to_update, $disorder_to_create), 'disorder_id')) === false) {
                    $this->event->episode->patient->removeDiagnosis($secondary_diagnosis->id);
                }
            }

            foreach ($added_diagnoses as $diagnosis) {
                if ($diagnosis->principal) {
                    $this->event->episode->setPrincipalDiagnosis($diagnosis->disorder_id, $diagnosis->eye_id, $diagnosis->date);
                } else {
                    $this->event->episode->patient->addDiagnosis(
                        $diagnosis->disorder_id,
                        $diagnosis->eye_id,
                        $diagnosis->date
                    );
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
    public function getLetter_string()
    {
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
                         ->findAll('element_id=?', array($et_findings->id)) as $finding_assignment
            ) {
                $finding = $finding_assignment->finding;
                $table_vals[] = array(
                    'finding_id' => $finding->id,
                    'date' => \Helper::convertDate2NHS($this->event->event_date),
                    'laterality' => '',
                    'term' => $finding->name .
                        (isset($finding_assignment->description) && $finding_assignment->description ?
                            " : " . $finding_assignment->description :
                            "")
                );
                $finding_ids[] = $finding->id;
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
                         ) as $secto_disorder) {
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
                         ) as $secto_disorder) {
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
        } elseif (!isset($this->no_ophthalmic_diagnoses_date) && !$this->diagnoses) {
            $this->addError('no_ophthalmic_diagnoses_date', 'Please confirm patient has no ophthalmic diagnoses.');
        } else {
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

                if (!$have_further_findings && !$this->diagnoses && !isset($this->no_ophthalmic_diagnoses_date)) {
                    $this->addError('diagnoses', 'Please select at least one diagnosis or please confirm patient has no ophthalmic diagnoses.');
                }
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
        $action_list = array('view', 'createImage', 'renderEventImage', 'removed');
        return in_array($action, $action_list) ? 1 : null;
    }
}
