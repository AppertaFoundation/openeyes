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

/**
 * This is the model class for table "genetics_study".
 *
 * The followings are the available columns in table 'genetics_study':
 *
 * @property int $id
 *
 * The followings are the available model relations:
 */
class GeneticsStudy extends BaseActiveRecordVersioned
{
    use Study;

    protected $auto_update_relations = true;

    protected $pivot = 'genetics_study_subject';
    
    public $formatted_end_date = null;
    
    protected $pivot_model = 'GeneticsStudySubject';

    /**
     * Returns the static model of the specified AR class.
     *
     * @return GeneticsStudy Issue the static model class
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
        return 'genetics_study';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, criteria, end_date, patients, proposers', 'safe'),
            array('name', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'proposers' => array(self::MANY_MANY, 'User', 'genetics_study_proposer(study_id, user_id)'),
            'subjects' => array(self::MANY_MANY, 'GeneticsPatient', 'genetics_study_subject(study_id, subject_id)'),
            'dna_sample_events' => array(
                self::MANY_MANY,
                'Element_OphInDnasample_Sample',
                'et_ophindnasample_sample_genetics_studies(genetics_study_id,et_ophindnasample_sample_id)',
            ),
        );
    }

    public function attributeLabels()
    {
        return array(
            'getProposerNames' => 'Investigators',
            'proposers.first_name' => 'Investigator first name',
            'proposers.last_name' => 'Investigator last name',
        );
    }

    /**
     * format date after search
     * @return string
     */
    public function afterFind()
    {
        $this->end_date = Helper::convertMySQL2NHS($this->end_date, null);
    }

    /**
     * @return bool
     */
    public function beforeSave()
    {
        $date = DateTime::createFromFormat('Y-m-d', $this->end_date);

        if (!$this->end_date || !$date) {
            $this->end_date = null;
        }
        return parent::beforeSave();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isUserProposer(User $user)
    {
        $is_proposer = false;
        foreach ($this->proposers as $proposer) {
            if ($proposer->id === $user->id) {
                $is_proposer = true;
                continue;
            }
        }

        return $is_proposer;
    }

    /**
     * @return string
     */

    public function getProposerNames()
    {
        $p = [];
        foreach($this->proposers as $proposer)
        {
            $p[] = $proposer->getFullName();
        }

        return implode(', ', $p);
    }

}
