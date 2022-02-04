<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
/**
 * This is the model class for table "ophtrconsent_sup_consent_question_assignment".
 *
 * The followings are the available columns in table 'ophtrconsent_sup_consent_question_assignment':
 *
 * @property int    $id
 * @property int    $question_id
 * @property string $question_text
 * @property string $question_info
 * @property string $question_output
 * @property string $default_option_text
 * @property int    $default_option_selection
 * @property int    $minimum_selected
 * @property int    $maximum_selected
 * @property int    $required
 * @property int    $institution_id
 * @property int    $site_id
 * @property int    $subspecialty_id
 * @property int    $form_id
 * @property int $last_modified_user_id
 * @property string $last_modified_date
 * @property int $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Ophtrconsent_SupplementaryConsentQuestion  $question
 * @property Institution                                $institution
 * @property Site                                       $site
 * @property Subspecialty                               $subspec
 * @property OphTrConsent_Type_Type                     $formType
 * @property User                                       $createdUser
 * @property User                                       $lastModifiedUser
 */
class Ophtrconsent_SupplementaryConsentQuestionAssignment extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtrconsent_sup_consent_question_assignment';
    }

    /**
     * @return array validation rules for model attributes
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['question_text, question_info, question_output, default_option_text', 'length', 'max' => 500],
            ['question_text, question_info, question_output, default_option_text,default_option_selection, minimum_selected, maximum_selected, required, active, institution_id, site_id, subspecialty_id, form_id', 'safe'],
        ];
    }

    /**
     * @return array relational rules
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'question' => [self::BELONGS_TO, 'Ophtrconsent_SupplementaryConsentQuestion', 'question_id'],
            'answers' => [self::HAS_MANY, 'Ophtrconsent_SupplementaryConsentQuestionAnswer', 'question_assignment_id'],
            'site' => [self::BELONGS_TO, 'Site', 'site_id'],
            'institution' => [self::BELONGS_TO, 'Institution', 'institution_id'],
            'subspecialty' => [self::BELONGS_TO, 'Subspecialty', 'subspec_id'],
            'formType' => [self::BELONGS_TO, 'OphTrConsent_Type_Type', 'form_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_text' => 'Question Text',
            'question_info' => 'Info [optional] (this is displayed as a second line on the edit form)',
            'question_output' => 'Question printed Text  [Optional]',
            'default_option_text' => 'Default answer text',
            'default_option_selection' => 'Default answer selection',
            'maximum_selected' => 'Max number or length of responses for this question?',
            'minimum_selected' => 'Min number or length of responses for this question?',

            'institution_id' => 'Institution',
            'site_id' => 'Site',
            'subspecialty_id' => 'Subspeciality',
            'form_id' => 'Consent Form Type',

            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
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
     *                             based on the search/filter conditions
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('question_type', $this->question_type, true);
        $criteria->compare('question_text', $this->question_text, true);
        $criteria->compare('question_info', $this->question_info, true);
        $criteria->compare('question_output', $this->question_output, true);

        $criteria->compare('institution_id', $this->institution_id, true);
        $criteria->compare('site_id', $this->site_id, true);
        $criteria->compare('subspecialty_id', $this->subspecialty_id, true);
        $criteria->compare('form_id', $this->form_id, true);

        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }


    public function getSettingLevel()
    {
        if ($this->site_id) {
            return "Site";
        } else {
            if ($this->subspecialty_id) {
                return "Subspecialty";
            } else {
                if ($this->institution_id) {
                    return "Institution";
                } else {
                    return "Installation";
                }
            }
        }
    }
    public function getFormLevelID()
    {
        if ($this->form_id) {
            return $this->formType->id;
        } else {
            return null;
        }
    }

    public function getFormLevelName()
    {
        if ($this->form_id) {
            return $this->formType->name;
        } else {
            return "All";
        }
    }


    public function getQuestionAnswerList($my_question_id)
    {
        if (empty($my_question_id)) {
            $my_question_id = $this->id;
        }

        $my_answer_list = CHtml::listData(
            Ophtrconsent_SupplementaryConsentQuestionAnswer::model()->findAllByAttributes(array("question_assignment_id" => $my_question_id)),
            'id',
            function ($obj) {
                if (!empty($obj->display)) {
                    return $obj->display;
                } else {
                    return $obj->name;
                }
            },
        );

        return $my_answer_list;
    }

    public function getAnswerElementSelectionList($element_id, $my_question_id)
    {
        if (empty($my_question_id)) {
            $my_question_id = $this->id;
        }
        $my_answer_list =[];

        if (isset($_POST['Element_OphTrConsent_SupplementaryConsent']['element_question'])) {
            $ele_qs = $_POST['Element_OphTrConsent_SupplementaryConsent']['element_question'];
            if (isset($ele_qs[$my_question_id])) {
                if (array_key_first($ele_qs[$my_question_id])=="check") {
                    return reset($ele_qs[$my_question_id]);
                }
                return  $ele_qs[$my_question_id];
            }
        }
        $my_question_element = Ophtrconsent_SupplementaryConsentElementQuestion::model()->findByAttributes(array("question_id" => $my_question_id, "element_id"=> $element_id));
        if (!empty($my_question_element)) {
            $my_answer_list = CHtml::listData(
                Ophtrconsent_SupplementaryConsentElementQuestionAnswer::model()->findAllByAttributes(array("element_question_id" => $my_question_element->id)),
                function ($obj) {
                    if (!empty($obj->answer->id)) {
                        return $obj->answer->id;
                    }
                },
                function ($obj) {
                    if (!empty($obj->answer->id)) {
                        return $obj->answer->id;
                    }
                },
            );
        }
        return $my_answer_list;
    }

    public function getAnswerElementSelectionText($element_id, $my_question_id)
    {
        if (empty($my_question_id)) {
            $my_question_id = $this->id;
        }
        $my_answer_text = '';

        if (isset($_POST['Element_OphTrConsent_SupplementaryConsent']['element_question'])) {
            $ele_qs = $_POST['Element_OphTrConsent_SupplementaryConsent']['element_question'];
            if (isset($ele_qs[$my_question_id])) {
                return  reset($ele_qs[$my_question_id]);
            }
        }

        $my_question_element = Ophtrconsent_SupplementaryConsentElementQuestion::model()->findByAttributes(array("question_id" => $my_question_id, "element_id" => $element_id));
        if (!empty($my_question_element)) {
            $ele_answers = Ophtrconsent_SupplementaryConsentElementQuestionAnswer::model()->findAllByAttributes(array("element_question_id" => $my_question_element->id));

            foreach ($ele_answers as $ele_ans) {
                if (!empty($ele_ans->answer_text)) {
                    if ($my_answer_text!== '') {
                        $my_answer_text = $my_answer_text." \n";
                    }
                    $my_answer_text = $my_answer_text . $ele_ans->answer_text;
                }
            }
        }
        $purifier = new CHtmlPurifier();
        $my_answer_text=  $purifier->purify($my_answer_text);
        return $my_answer_text;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className active record class name
     *
     * @return Ophtrconsent_SupplementaryConsentQuestionAssignment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
