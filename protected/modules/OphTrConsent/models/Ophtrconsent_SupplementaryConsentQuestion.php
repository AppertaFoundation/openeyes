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

use Symfony\Component\Console\Question\Question;

/**
 * This is the model class for table "ophtrconsent_sup_consent_question".
 *
 * The followings are the available columns in table 'ophtrconsent_sup_consent_question':
 *
 * @property int    $id
 * @property int $question_type_id
 * @property string $name
 * @property string $description
 * @property int $last_modified_user_id
 * @property string $last_modified_date
 * @property int $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Ophtrconsent_SupplementaryConsentQuestionType $questionType
 * @property User                               $createdUser
 * @property User                               $lastModifiedUser
 */
class Ophtrconsent_SupplementaryConsentQuestion extends BaseActiveRecordVersioned
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtrconsent_sup_consent_question';
    }

    /**
     * @return array validation rules for model attributes
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['name, description', 'length', 'max' => 500],
            ['name, description, last_modified_date, created_date', 'safe'],
            // The following rule is used by search().
            ['name, description', 'safe', 'on' => 'search'],
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
            'question_assignment' => array(self::HAS_MANY, 'Ophtrconsent_SupplementaryConsentQuestionAssignment', 'question_id'),
            'question_type' => [self::BELONGS_TO, 'Ophtrconsent_SupplementaryConsentQuestionType', 'question_type_id'],
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Question name',
            'description' => 'Question description (e.,g the actual question to ask)',
            'question_type'=>'Type of question input style',
            'question_assignment' => 'Question wording assignment',
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
        $criteria = new CDbCriteria();

        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }
    /**
     * Returns all of the relevant Ophtrconsent_SupplementaryConsentQuestionAssignment for your context
     *
     * @param string $className active record class name
     *
     * @return array(Ophtrconsent_SupplementaryConsentQuestionAssignment)
     */
    public function findAllMyQuestionsAsgn($institution_id = null, $site_id = null, $subspecialty_id = null, $form_id = null)
    {
        $rtn_array =[];
        $all_questions = Ophtrconsent_SupplementaryConsentQuestion::model()->findAll();
        foreach ($all_questions as $question) {
            $best_asgn = null;
            $best_site_id = false;
            $best_subspecialty_id = false;
            $best_institution_id = false;
            $best_form_id = false;

            //find the best question assignment
            foreach($question->question_assignment as $ke=> $q_asgn){
                //if this is a better assignment than $best_asgn, override it
                // if we have one that doesn't match throw it out
                if (
                    ($q_asgn->institution_id !== null && !($q_asgn->institution_id === $institution_id))
                    || ($q_asgn->subspecialty_id !== null && !($q_asgn->subspecialty_id === $subspecialty_id))
                    || ($q_asgn->site_id !== null && !($q_asgn->site_id === $site_id))
                    || ($q_asgn->form_id !== null && !($q_asgn->form_id === $form_id))
                ){
                    continue;
                }
                //if we have a site_id to check
                if ($site_id === $q_asgn->site_id) {
                    //if we have a form_id to check against
                    if (!($best_site_id === true && $best_form_id === true) && ($q_asgn->form_id === $form_id)) {
                        $best_asgn = $q_asgn;
                        $best_institution_id = ($institution_id === $q_asgn->institution_id);
                        $best_subspecialty_id = ($subspecialty_id === $q_asgn->subspecialty_id);
                        $best_site_id = ($site_id === $q_asgn->site_id);
                        $best_form_id = ($form_id === $q_asgn->form_id);
                        continue;
                    }
                    elseif ($best_site_id === false){ // if we dont have a best site value.
                        $best_asgn = $q_asgn;
                        $best_institution_id = ($institution_id === $q_asgn->institution_id);
                        $best_subspecialty_id = ($subspecialty_id === $q_asgn->subspecialty_id);
                        $best_site_id = ($site_id === $q_asgn->site_id);
                        $best_form_id = ($form_id === $q_asgn->form_id);
                        continue;
                    }
                    //we had the site id, but were still beaten by the previous values
                    continue;
                }
                //if we have a subspecialty_id to check
                if ($subspecialty_id === $q_asgn->subspecialty_id) {
                    //if we have a form_id to check against
                    if (!($best_subspecialty_id === true && $best_form_id === true) && ($q_asgn->form_id === $form_id)) {
                        $best_asgn = $q_asgn;
                        $best_institution_id = ($institution_id === $q_asgn->institution_id);
                        $best_subspecialty_id = ($subspecialty_id === $q_asgn->subspecialty_id);
                        $best_site_id = ($site_id === $q_asgn->site_id);
                        $best_form_id = ($form_id === $q_asgn->form_id);
                        continue;
                    } elseif ($best_subspecialty_id === false) { // if we dont have a best_subspecialty_id value.
                        $best_asgn = $q_asgn;
                        $best_institution_id = ($institution_id === $q_asgn->institution_id);
                        $best_subspecialty_id = ($subspecialty_id === $q_asgn->subspecialty_id);
                        $best_site_id = ($site_id === $q_asgn->site_id);
                        $best_form_id = ($form_id === $q_asgn->form_id);
                        continue;
                    }
                    //we had the subspecialty_id, but were still beaten by the previous values
                    continue;
                }

                //if we have a institution_id to check
                if ($institution_id === $q_asgn->institution_id) {
                    //if we have a form_id to check against
                    if (!($best_institution_id === true && $best_form_id === true) && ($q_asgn->form_id === $form_id)) {
                        $best_asgn = $q_asgn;
                        $best_institution_id = ($institution_id === $q_asgn->institution_id);
                        $best_subspecialty_id = ($subspecialty_id === $q_asgn->subspecialty_id);
                        $best_site_id = ($site_id === $q_asgn->site_id);
                        $best_form_id = ($form_id === $q_asgn->form_id);
                        continue;
                    } elseif ($best_institution_id === false) { // if we dont have a best_institution_id value.
                        $best_asgn = $q_asgn;
                        $best_institution_id = ($institution_id === $q_asgn->institution_id);
                        $best_subspecialty_id = ($subspecialty_id === $q_asgn->subspecialty_id);
                        $best_site_id = ($site_id === $q_asgn->site_id);
                        $best_form_id = ($form_id === $q_asgn->form_id);
                        continue;
                    }
                    //we had the institution_id, but were still beaten by the previous values
                    continue;
                }

                //if this our first value save it
                if($q_asgn === null) {
                    $best_asgn = $q_asgn;
                    $best_institution_id = false;
                    $best_subspecialty_id = false;
                    $best_site_id = false;
                    $best_form_id = false;
                    continue;
                }

                // test against installation(all nulls) level is a better match
                if(($best_institution_id === false && $best_subspecialty_id === false && $best_site_id === false) && ($q_asgn->institution_id === null && $q_asgn->subspecialty_id === null && $q_asgn->site_id === null)){
                    // if form_id is a better match beat in tier
                    if($best_form_id === false && $q_asgn->form_id === $form_id){
                        $best_asgn = $q_asgn;
                        $best_institution_id = ($institution_id === $q_asgn->institution_id);
                        $best_subspecialty_id = ($subspecialty_id === $q_asgn->subspecialty_id);
                        $best_site_id = ($site_id === $q_asgn->site_id);
                        $best_form_id = ($form_id === $q_asgn->form_id);
                    } // if we don't have a current best assignment set this as it.
                    elseif (empty($best_asgn)){
                        $best_asgn = $q_asgn;
                        $best_institution_id = ($institution_id === $q_asgn->institution_id);
                        $best_subspecialty_id = ($subspecialty_id === $q_asgn->subspecialty_id);
                        $best_site_id = ($site_id === $q_asgn->site_id);
                        $best_form_id = ($form_id === $q_asgn->form_id);
                    }
                }
            }
            //if we have a value to return add it to the list to return
            if (!empty($best_asgn)) {
                array_push($rtn_array, $best_asgn);
            }
        }
        return $rtn_array;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className active record class name
     *
     * @return Ophtrconsent_SupplementaryConsentQuestion the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
