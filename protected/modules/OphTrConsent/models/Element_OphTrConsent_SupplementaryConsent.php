
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
 * This is the model class for table "et_ophtrconsent_sup_consent_element".
 *
 * The followings are the available columns in table 'et_ophtrconsent_sup_consent_element':
 * @property int    $id
 * @property int    $event_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property Event $event
 */
class Element_OphTrConsent_SupplementaryConsent extends BaseEventTypeElement
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtrconsent_sup_consent_element';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'element_question' => array(self::HAS_MANY, 'Ophtrconsent_SupplementaryConsentElementQuestion', 'element_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
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

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


    public function eventScopeValidation(array $elements)
    {
        $ele_qs = $_POST['Element_OphTrConsent_SupplementaryConsent']['element_question'];

        $form_id = $_POST['Element_OphTrConsent_Type']['type_id'] ?? ($this->form_id??null);
        $institution_id = $this->event->episode->firm->institution_id ?? null;
        $site_id = $this->event->site_id ?? null;
        $subspecialty_id = isset($this->event->episode->firm) ? $this->event->episode->firm->getSubspecialtyID() : null;
        //find this element's questions
        $my_questions = Ophtrconsent_SupplementaryConsentQuestion::model()->findAllMyQuestionsAsgn(
            $institution_id,
            $site_id,
            $subspecialty_id,
            $form_id
        );

        //for each element question id posted
        foreach ($ele_qs as $ele_q_id => $ele_q_data) {
            $element_question = Ophtrconsent_SupplementaryConsentQuestionAssignment::model()->find("id = :q_id", [":q_id" => $ele_q_id]);
            $min = $element_question->minimum_selected;
            $max = $element_question->maximum_selected;
            $q_tex = $element_question->question_text;
            $e_q_name = 'element_question[' . $element_question->id . ']';
            foreach ($ele_q_data as $data_type => $ele_a_data) {
                // check each type of value posted to figure out how to handle them
                if ($data_type === 'text' || $data_type === 'textarea') {
                    //validate text length is between the min and max
                    $length = strlen($ele_a_data);
                    if ($min !== null && $length < $min) {
                        $this->addError(
                            $e_q_name,
                            "Answer for question '". $q_tex."' is too short" . "(" . $length . " characters), min character length is " . $min . "."
                        );
                    }
                    if ($max !== null && $length > $max) {
                        $this->addError(
                            $e_q_name,
                            "Answer for question '".$q_tex."' is too long" . "(" . $length . " characters), max character length is ".$max ."."
                        );
                    }
                } elseif ($data_type === 'check') {
                    $count = count($ele_a_data);
                    if ($min !== null && $count < $min) {
                        $this->addError(
                            $e_q_name,
                            "Not enough answers for question '" . $q_tex."'(" . $count . " results received), min " . $min . " results expected."
                        );
                    }
                    if ($max !== null && $count > $max) {
                        $this->addError(
                            $e_q_name,
                            "Too many answers for question '" . $q_tex."'(" . $count . " results received), max " . $max . " results expected."
                        );
                    }
                }
            }
        }

        foreach ($my_questions as $question_asgn) {
            if (!array_key_exists($question_asgn->id, $ele_qs) && $question_asgn->required === true) {
                $this->addError(
                    "$e_q_name",
                    "Did not recive answer for required question '" . $question_asgn->question_text. "'"
                );
            }
        }
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_OphTrConsent_Supplementaryconsent the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
