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
 * This is the model class for table "firm".
 *
 * The followings are the available columns in table 'firm':
 *
 * @property int $id
 * @property int $service_subspecialty_assignment_id
 * @property string $pas_code
 * @property string $name
 *
 * The followings are the available model relations:
 * @property ServiceSubspecialtyAssignment $serviceSubspecialtyAssignment
 * @property FirmUserAssignment[] $firmUserAssignments
 * @property User[] $members
 * @property User $consultant
 */
class Firm extends BaseActiveRecordVersioned
{
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
            array('name', 'required'),
            array('service_subspecialty_assignment_id', 'length', 'max' => 10),
            array('pas_code', 'length', 'max' => 20),
            array('name', 'length', 'max' => 40),
            array('name, pas_code, subspecialty_id, consultant_id, active, runtime_selectable, can_own_an_episode', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, service_subspecialty_assignment_id, pas_code, name', 'safe', 'on' => 'search'),
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
            'name' => 'Name',
            'serviceSubspecialtyAssignment.subspecialty.name' => 'Subspeciality Name',
            'active' => 'Active',
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

    /**
     * @return string
     */
    public static function contextLabel()
    {
        return ucwords(strtolower(Yii::app()->params['context_firm_label']));
    }

    /**
     * @return string
     */
    public static function serviceLabel()
    {
        return ucwords(strtolower(Yii::app()->params['service_firm_label']));
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

    /**
     * Fetch an array of firm IDs and names.
     *
     * @return array
     */
    public function getList($subspecialty_id = null, $include_id = null)
    {
        $cmd = Yii::app()->db->createCommand()
            ->select('f.id, f.name')
            ->from('firm f')
            ->where('f.active = 1'.($include_id ? ' or f.id = :include_id' : ''));

        if ($subspecialty_id) {
            $cmd->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
                ->andWhere('ssa.subspecialty_id = :subspecialty_id')
                ->bindValue(':subspecialty_id', $subspecialty_id);
        }

        if ($include_id) {
            $cmd->bindValue(':include_id', $include_id);
        }

        $result = array();
        foreach ($cmd->queryAll() as $firm) {
            $result[$firm['id']] = $firm['name'];
        }

        natcasesort($result);

        return $result;
    }

    /**
     * @param $include_non_subspecialty boolean defaults to false
     *
     * @return array
     */
    public function getListWithSpecialties($include_non_subspecialty = false, $subspecialty_id = null)
    {
        $join_method = $include_non_subspecialty ? 'leftJoin' : 'join';

        $command = Yii::app()->db->createCommand()
            ->select('f.id, f.name, s.name AS subspecialty')
            ->from('firm f')
            ->$join_method('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
            ->$join_method('subspecialty s', 'ssa.subspecialty_id = s.id')
            ->where('f.active = 1');

        if($subspecialty_id){
            $command->andWhere('s.id = :id', array(':id' => $subspecialty_id));
        }

        $firms = $command->order('f.name, s.name')->queryAll();

        $data = array();
        foreach ($firms as $firm) {
            $display = $firm['name'];
            if ($firm['subspecialty']) {
                $display .= ' ('.$firm['subspecialty'].')';
            }
            $data[$firm['id']] = $display;
        }
        natcasesort($data);

        return $data;
    }

    /**
     * @return array
     */
    public function getListWithSpecialtiesAndEmergency()
    {
        $list = array('NULL' => 'Emergency');
        foreach ($this->getListWithSpecialties() as $firm_id => $name) {
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
            return $consultant->contact->title.' '.$consultant->contact->first_name.' '.$consultant->contact->last_name;
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
            return $this->name.' ('.$this->serviceSubspecialtyAssignment->subspecialty->name.')';
        } else {
            return $this->name;
        }
    }

    /**
     * @return string
     */
    public function getNameAndSubspecialtyCode()
    {
        if ($this->serviceSubspecialtyAssignment) {
            return $this->name.' ('.$this->serviceSubspecialtyAssignment->subspecialty->ref_spec.')';
        } else {
            return $this->name;
        }
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
            return;
        } else {
            return Specialty::model()->findByPk($result['id']);
        }
    }

    /**
     * @return bool
     */
    public function beforeSave()
    {
        if ($this->subspecialty_id) {
            $this->service_subspecialty_assignment_id = ServiceSubspecialtyAssignment::model()->find('subspecialty_id=?', array($this->subspecialty_id))->id;
        }

        return parent::beforeSave();
    }

    /**
     * @return string
     */
    public function getTreeName()
    {
        return $this->name.' '.$this->serviceSubspecialtyAssignment->subspecialty->ref_spec;
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
}
