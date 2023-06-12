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
 * This is the model class for table "firm".
 *
 * The followings are the available columns in table 'firm':
 *
 * @property int $id
 * @property int $service_subspecialty_assignment_id
 * @property string $pas_code
 * @property string $cost_code
 * @property int $institution_id
 * @property string $name
 * @property string $service_email
 * @property string $context_email
 * @property bool $can_own_an_episode
 *
 * The followings are the available model relations:
 * @property ServiceSubspecialtyAssignment $serviceSubspecialtyAssignment
 * @property FirmUserAssignment[] $firmUserAssignments
 * @property User[] $members
 * @property Institution $institution
 * @property User $consultant
 */
class Firm extends BaseActiveRecordVersioned
{
    use HasFactory;
    use OwnedByReferenceData;

    const SELECTION_ORDER = 'name';

    public $subspecialty_id;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Firm the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function getSupportedLevelMask(): int
    {
        return ReferenceData::LEVEL_INSTALLATION | ReferenceData::LEVEL_INSTITUTION;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'firm';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, subspecialty_id', 'required'),
            array('service_subspecialty_assignment_id', 'length', 'max' => 10),
            array('pas_code', 'length', 'max' => 20),
            array('cost_code', 'length', 'max' => 5),
            array('name', 'length', 'max' => 40),
            array('service_email, context_email', 'length', 'max' => 255),
            array('service_email, context_email','email'),
            array('name', 'filter', 'filter' => 'htmlspecialchars'),
            array('name, service_email, pas_code, cost_code, subspecialty_id, consultant_id, active, runtime_selectable, can_own_an_episode', 'safe'),
            array('name', 'filter', 'filter' => 'htmlspecialchars'),
            array('name, pas_code, cost_code, subspecialty_id, consultant_id, active, runtime_selectable, can_own_an_episode, institution_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, service_subspecialty_assignment_id, pas_code, name, service_email, context_email', 'safe', 'on' => 'search'),
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
            'serviceSubspecialtyAssignment' => array(self::BELONGS_TO, 'ServiceSubspecialtyAssignment', 'service_subspecialty_assignment_id'),
            'firmUserAssignments' => array(self::HAS_MANY, 'FirmUserAssignment', 'firm_id'),
            //'letterPhrases' => array(self::HAS_MANY, 'LetterPhrase', 'firm_id'),
            'userFirmRights' => array(self::HAS_MANY, 'UserFirmRights', 'firm_id'),
            'members' => array(self::MANY_MANY, 'User', 'firm_user_assignment(firm_id, user_id)'),
            'consultant' => array(self::BELONGS_TO, 'User', 'consultant_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'service_subspecialty_assignment_id' => 'Service Subspecialty Assignment',
            'pas_code' => 'Pas Code',
            'cost_code' => 'Cost Code',
            'name' => 'Name',
            'serviceSubspecialtyAssignment.subspecialty.name' => 'Subspeciality Name',
            'active' => 'Active',
            'service_email' => 'Email',
            'context_email' => 'Email',
        );
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    public function scopes()
    {
        return array(
            'runtime' => array(
                'condition' => 'runtime_selectable = 1'
            ),
            'episodeOwner' => array(
                'condition' => 'can_own_an_episode = 1'
            )
        );
    }

    private static function getLabelSettings($key)
    {
        $institution_id = \Yii::app()->session['selected_institution_id'];
        return \SettingMetadata::model()->getSetting($key, null, false, ['SettingInstitution', 'SettingInstallation'], $institution_id);
    }
    /**
     * @return string
     */
    public static function contextLabel()
    {
        $label = SettingMetadata::model()->getSetting('context_firm_label') ? : self::getLabelSettings('context_firm_label');
        return ucwords(strtolower($label));
    }

    /**
     * @return string
     */
    public static function serviceLabel()
    {
        $label = SettingMetadata::model()->getSetting('service_firm_label') ? : self::getLabelSettings('service_firm_label');
        return ucwords(strtolower($label));
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
        $criteria->compare('service_subspecialty_assignment_id', $this->service_subspecialty_assignment_id, true);
        $criteria->compare('pas_code', $this->pas_code, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getServiceText()
    {
        return $this->serviceSubspecialtyAssignment->service->name;
    }

    /**
     * retrieve a label for the sub specialty assignment for this firm.
     *
     * @return string
     */
    public function getSubspecialtyText()
    {
        return $this->serviceSubspecialtyAssignment ? $this->serviceSubspecialtyAssignment->subspecialty->name : 'Support services';
    }

    public static function getFirmsForInstitution($institution_id)
    {
        return self::model()->findAll('institution_id = :institution_id OR institution_id IS NULL', [':institution_id' => $institution_id]);
    }

    /**
     * Fetch an array of firm IDs and names.
     * @param null $subspecialty_id
     * @param null $include_id
     * @param null $runtime_selectable
     * @param null $can_own_an_episode
     * @param $include_subspecialty_name Will return name as "<context name> (<subspecialty name)"
     * @return array
     * @throws CException
     */
    public function getList($institution_id = null, $subspecialty_id = null, $include_id = null, $runtime_selectable = null, $can_own_an_episode = null, $include_subspecialty_name = null)
    {
        $bindValues = array();
        /**
         * @var CDbCommand $cmd
         */
        $cmd = Yii::app()->db->createCommand()
            ->select('f.id, f.name, s.name as subspecialty')
            ->from('firm f')
            ->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
            ->join('subspecialty s', 's.id = ssa.subspecialty_id')
            ->where('f.active = 1 ' .
                ($runtime_selectable ? 'and f.runtime_selectable = 1' : '') .
                ($can_own_an_episode ? 'and f.can_own_an_episode = 1' : '') .
                ($include_id ? ' or f.id = :include_id' : ''));

        if ($institution_id) {
            $cmd = $cmd->andWhere('f.institution_id = :institution_id OR f.institution_id IS NULL');
            $bindValues[':institution_id'] = $institution_id;
        }

        if ($subspecialty_id) {
            $cmd = $cmd->andWhere('ssa.subspecialty_id = :subspecialty_id');
            $bindValues[':subspecialty_id'] = $subspecialty_id;
        }

        if ($include_id) {
            $bindValues[':include_id'] = $include_id;
        }

        if (!empty($bindValues)) {
            $cmd = $cmd->bindValues($bindValues);
        }

        $result = array();
        foreach ($cmd->queryAll() as $firm) {
            $result[$firm['id']] = ($include_subspecialty_name ? $firm['name'] . ' (' . $firm['subspecialty'] . ')' : $firm['name']);
        }

        natcasesort($result);

        return $result;
    }

    /**
     * @param $include_non_subspecialty boolean defaults to false
     * @param $only_used_firms boolean defaults to false used to determine if return only firms that been used already
     *
     * @return array
     */
    public function getListWithSpecialties($institution_id = null, $include_non_subspecialty = false, $subspecialty_id = null, $only_with_consultant = false, $only_service_firms = false)
    {

        $join_method = $include_non_subspecialty ? 'leftJoin' : 'join';

        $command = Yii::app()->db->createCommand()
            ->select('f.id, f.name, s.name AS subspecialty')
            ->from('firm f')
            ->$join_method('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
            ->$join_method('subspecialty s', 'ssa.subspecialty_id = s.id')
            ->where('f.active = 1');

        if ($only_with_consultant) {
            $command->andWhere('consultant_id IS NOT NULL');
        }

        if ($subspecialty_id) {
            $command->andWhere('s.id = :id', array(':id' => $subspecialty_id));
        }

        if ($institution_id) {
            $command->andWhere('f.institution_id = :institution_id OR f.institution_id IS NULL', array(':institution_id' => $institution_id));
        }

        if ($only_service_firms) {
            $command->andWhere('f.can_own_an_episode = 1');
        }

        $firms = $command->order('f.name, s.name')->queryAll();

        $data = array();
        foreach ($firms as $firm) {
            $display = $firm['name'];
            if ($firm['subspecialty']) {
                $display .= ' (' . $firm['subspecialty'] . ')';
            }
            $data[$firm['id']] = $display;
        }
        natcasesort($data);

        return $data;
    }

    /**
     * @return array
     */
    public function getListWithSpecialtiesAndEmergency($institution_id = null)
    {
        $list = array('NULL' => 'Emergency');
        foreach ($this->getListWithSpecialties($institution_id) as $firm_id => $name) {
            $list[$firm_id] = $name;
        }

        return $list;
    }

    /**
     * @return string
     */
    public function getConsultantName()
    {
        if ($consultant = $this->consultant) {
            return $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
        }

        return 'NO CONSULTANT';
    }

    /**
     * @param $institution_id
     * @return string
     */
    public function getConsultantNameAndUsername($institution_id, bool $reversed = true, string $username_prefix = '', string $separator = ' '): string
    {
        if ($consultant = $this->consultant) {
            $user_auth_id = Yii::app()->db->createCommand()
                ->select('ua.id')
                ->from('institution_authentication ia')
                ->join('user_authentication ua', 'ua.institution_authentication_id = ia.id')
                ->where('(ia.institution_id = :institution_id OR ia.institution_id IS NULL) AND ua.user_id = :user_id')
                ->limit(1)
                ->bindValues([':institution_id' => $institution_id, ':user_id' => $consultant->id])
                ->queryScalar();

            // Assuming that, despite multiple institution authentications,
            // a user's username is identical for all of them.
            $user_auth = UserAuthentication::model()->findByPk($user_auth_id);

            if ($consultant->registration_code) {
                return ($reversed ? $consultant->getReversedFullNameAndTitle($separator) : $consultant->getFullNameAndTitle($separator)) . " ({$username_prefix}{$consultant->registration_code})";
            } elseif ($user_auth) {
                return ($reversed ? $consultant->getReversedFullNameAndTitle($separator) : $consultant->getFullNameAndTitle($separator)) . " ({$username_prefix}{$user_auth->username})";
            }
            return $reversed ? $consultant->getReversedFullNameAndTitle($separator) : $consultant->getFullNameAndTitle($separator);
        }

        return 'NO CONSULTANT';
    }

    /**
     * @return string
     */
    public function getReportDisplay()
    {
        return $this->getNameAndSubspecialty();
    }

    /**
     * @return string
     */
    public function getNameAndSubspecialty()
    {
        if ($this->serviceSubspecialtyAssignment) {
            return $this->name . ' (' . $this->serviceSubspecialtyAssignment->subspecialty->name . ')';
        }

        return $this->name;
    }

    /**
     * @return string
     */
    public function getNameAndSubspecialtyCode()
    {
        if ($this->serviceSubspecialtyAssignment) {
            return $this->name . ' (' . $this->serviceSubspecialtyAssignment->subspecialty->ref_spec . ')';
        }

        return $this->name;
    }

    /**
     * Get the Specialty of the Firm.
     *
     * @return Specialty|null
     */
    public function getSpecialty()
    {
        $result = Yii::app()->db->createCommand()
            ->select('su.specialty_id as id')
            ->from('subspecialty su')
            ->join('service_subspecialty_assignment svc_ass', 'svc_ass.subspecialty_id = su.id')
            ->join('firm f', 'f.service_subspecialty_assignment_id = svc_ass.id')
            ->where('f.id = :fid', array(':fid' => $this->id))
            ->queryRow();

        if (empty($result)) {
            return null;
        }

        return Specialty::model()->findByPk($result['id']);
    }

    /**
     * @return bool
     */
    public function beforeSave()
    {
        if ($this->subspecialty_id) {
            $this->service_subspecialty_assignment_id = ServiceSubspecialtyAssignment::model()->find('subspecialty_id=?', array($this->subspecialty_id))->id;
        }

        if ($this->service_email === "") {
            $this->service_email = null;
        }

        if ($this->context_email === "") {
            $this->context_email = null;
        }

        return parent::beforeSave();
    }

    /**
     * @return string
     */
    public function getTreeName()
    {
        return $this->name . ' ' . $this->serviceSubspecialtyAssignment->subspecialty->ref_spec;
    }

    /**
     * get the subspecialty for the firm - null if one not set (support service firm).
     *
     * @return Subspecialty|null
     */
    public function getSubspecialty()
    {
        return $this->serviceSubspecialtyAssignment ? $this->serviceSubspecialtyAssignment->subspecialty : null;
    }

    /**
     * get the id for the subspecialty for the firm - null if one not set (support service firm).
     *
     * @return int|null
     */
    public function getSubspecialtyID()
    {
        return $this->serviceSubspecialtyAssignment ? $this->serviceSubspecialtyAssignment->subspecialty_id : null;
    }

    /**
     * Check whether this is a support services firm.
     *
     * @return bool
     */
    public function isSupportServicesFirm()
    {
        return is_null($this->serviceSubspecialtyAssignment);
    }

    public function beforeValidate()
    {
        // get the service_subspeciality_assignment_id from the service_id
        $serviceSubspecialityAssignmentId = ServiceSubspecialtyAssignment::model()->find('subspecialty_id = ?', array($this->subspecialty_id));
        if ($this->can_own_an_episode && $this->service_email != '') {
            // check if there is an email already existing for this subspeciality
            $criteria = new CDbCriteria();
            $criteria->addCondition('service_subspecialty_assignment_id = :service_subspecialty_assignment_id and service_email IS NOT NULL');
            $criteria->params[':service_subspecialty_assignment_id'] = $serviceSubspecialityAssignmentId->id;
            if (!$this->isNewRecord) {
                $criteria->addCondition('id != :id');
                $criteria->params[':id'] = $this->id;
            }
            $firm = $this->findAll($criteria);
            if (count($firm) >= 1) {
                $this->addError('service_email', 'Email already set for another service of this specialty.');
            }
        }
        $criteria = new CDbCriteria();
        $criteria->addCondition('name = :name AND service_subspecialty_assignment_id = :service_subspecialty_assignment_id');
        $criteria->params[':name'] = $this->name;
        $criteria->params[':service_subspecialty_assignment_id'] = $serviceSubspecialityAssignmentId->id;

        if (!$this->isNewRecord) {
            $criteria->addCondition("id != :id");
            $criteria->params[":id"] = $this->id;
        }

        if (isset($this->institution)) {
            $firm = $this->findAllAtLevels(ReferenceData::LEVEL_ALL, $criteria, $this->institution);
        } else {
            $firm = $this->findAll($criteria);
        }

        if (count($firm) >= 1) {
            $this->addError('name', 'A firm set with the name ' . $this->name . ' already exists with the following settings: ' . ($firm[0]->institution_id ? $firm[0]->institution->name . ', ' : 'All Institutions, ') . ($serviceSubspecialityAssignmentId ? $serviceSubspecialityAssignmentId->service->name . ', ' . $serviceSubspecialityAssignmentId->subspecialty->name : ''));
        }
        return parent::beforeValidate();
    }

    public function getContextEmail()
    {
        $contextEmail = $this->context_email;
        return $contextEmail ?? null;
    }

    public function getDefaultServiceFirm(?Institution $institution = null): ?Firm
    {
        $subspecialty = $this->getSubspecialty();
        if (!$subspecialty) {
            return null;
        }
        return static::getDefaultServiceFirmForSubspecialty($subspecialty, $institution);
    }

    /**
     * @param Subspecialty|int $subspecialty
     * @param ?Institution $institution = null
     * @return Firm
     */
    public static function getDefaultServiceFirmForSubspecialty($subspecialty, ?Institution $institution = null): ?Firm
    {
        $subspecialty_id = $subspecialty instanceof Subspecialty ? $subspecialty->id : $subspecialty;
        if ($institution === null) {
            $institution = Institution::model()->getCurrent();
        }

        return self::model()->with('serviceSubspecialtyAssignment')->find(
            'can_own_an_episode = 1 AND subspecialty_id = :subspecialty_id AND (institution_id = :institution_id OR institution_id IS NULL)',
            [':subspecialty_id' => $subspecialty_id, ':institution_id' => $institution->id]
        );
    }
}
