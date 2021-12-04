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
 * This is the model class for table "ophciexamination_clinicoutcome_status".
 *
 * @property int $id
 * @property string $name
 * @property int $institution_id
 * @property int $display_order
 * @property EpisodeStatus $episode_status
 * @property bool $followup
 * @property bool $patientticket
 */
class OphCiExamination_ClinicOutcome_Status extends \BaseActiveRecordVersioned
{
    private $original_attributes;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_ClinicOutcome_Status the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected $auto_update_relations = true;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_clinicoutcome_status';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }

    /**
     * @return array validation rules for model
     */
    public function rules()
    {
        return [
            ['name, display_order, episode_status_id', 'required'],
            ['followup', 'default', 'setOnEmpty' => true, 'value' => false],
            ['patientticket', 'default', 'setOnEmpty' => true, 'value' => false],
            ['followup, episode_status_id, patientticket', 'lockIfInUse'],
            ['subspecialties', 'safe'],
            ['institution_id', 'default', 'setOnEmpty' => true, 'value' => null],
            ['id, name, institution_id, display_order', 'safe', 'on' => 'search'],
        ];
    }

    public function lockIfInUse($attribute, $params)
    {
        if (!$this->isNewRecord && $this->$attribute != (int)$this->original_attributes[$attribute]) {
            if ($this->inUse()) {
                $this->addError($attribute, "This Clinical Outcome Status is in use and so $attribute cannot be edited");
            }
        }
    }

    public function inUse()
    {
        $noOfStatusUsecases = ClinicOutcomeEntry::model()->count('status_id=:status_id', [ 'status_id' => $this->id ]);
        return $noOfStatusUsecases > 0;
    }

    public function afterFind()
    {
        $this->original_attributes = $this->attributes;
        return parent::afterFind();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'episode_status' => array(self::BELONGS_TO, 'EpisodeStatus', 'episode_status_id'),
            'subspecialties' => array(self::MANY_MANY, 'Subspecialty', 'ophciexamination_clinicoutcome_status_options(clinicoutcome_status_id, subspecialty_id)'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'followup' => 'Show Follow Up Options',
            'patientticket' => 'Generate Patient Ticket',
            'episode_status_id' => 'Episode Status',
            'institution_id' => 'Institution',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('display_order', $this->display_order, true);
        $criteria->compare('institution_id', $this->institution_id, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function bySubspecialty($subspecialty)
    {
        $criteria = array(
            'join' => 'left join ophciexamination_clinicoutcome_status_options on ophciexamination_clinicoutcome_status_options.clinicoutcome_status_id = t.id',
            'condition' => 'ophciexamination_clinicoutcome_status_options.subspecialty_id is null',
        );
        if ($subspecialty) {
            $criteria['condition'] .= ' OR ophciexamination_clinicoutcome_status_options.subspecialty_id = :subspecialty_id';
            $criteria['params'] = array(':subspecialty_id' => $subspecialty->id);
        }
        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    public function byInstitution($institution)
    {
        $criteria = [
            'condition' => 't.institution_id is null',
        ];
        if ($institution) {
            $criteria['condition'] .= ' OR t.institution_id = :institution_id';
            $criteria['params'] = [':institution_id' => $institution->id];
        }
        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    public function getPatientTicketIds() {
        $element_ids = [];
        $elements = $this->findAll('patientticket=:patientticket', [':patientticket' => 1]);

        foreach ($elements as $element) {
            $element_ids[] = $element->id;
        }

        return $element_ids;
    }
}
