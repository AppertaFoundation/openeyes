<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\models;

use BaseActiveRecordVersioned;
use CActiveDataProvider;
use CDbCriteria;
use Institution;
use MappedReferenceData;
use ReferenceData;

class TicketAssignOutcomeOption extends BaseActiveRecordVersioned
{
    use MappedReferenceData;
    use \FindOrNewModel;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'outcome_option_id';
    }
    /**
     * Returns the static model of the specified AR class.
     *
     * @return TicketAssignOutcomeOption the static model class
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
        return 'patientticketing_ticketassignoutcomeoption';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, display_order, queueset_id', 'required'),
            array('episode_status_id, followup, display_order', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'episode_status' => array(self::BELONGS_TO, 'EpisodeStatus', 'episode_status_id'),
            'outcome_option_institutions' => array(self::HAS_MANY, TicketAssignOutcomeOption_Institution::class, 'outcome_option_id'),
            'institutions' => array(self::MANY_MANY, Institution::class, 'patientticketing_ticketassignoutcomeoption_institution(outcome_option_id, institution_id)'),
            'queue_set' => array(self::BELONGS_TO, 'QueueSet', 'queueset_id'),
        );
    }

    public function beforeDelete()
    {
        foreach ($this->outcome_option_institutions as $outcome_option_institution) {
            $outcome_option_institution->delete();
        }
        return parent::beforeDelete();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'episode_status_id' => 'Episode Status',
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Assign Option to an institution
     *
     * @param int $institution_id
     * @return bool
     * @throws \Exception
     */
    public function addToInstitution(int $institution_id): bool
    {
        $exist = TicketAssignOutcomeOption_Institution::model()->findByAttributes([
            'outcome_option_id' => $this->id,
            'institution_id' => $institution_id
        ]);

        if (!$exist) {
            $model = new TicketAssignOutcomeOption_Institution();
            $model->outcome_option_id = $this->id;
            $model->institution_id = $institution_id;
            if (!$model->save()) {
                return false;
            }
        }

        return true;
    }
}
