<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class Worklist.
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property string $name
 * @property bool $scheduled
 * @property WorklistAttribute[] $mapping_attributes
 * @property WorklistPatient[] $worklist_patients
 * @property WorklistDefinition $worklist_definition
 */
class Worklist extends BaseActiveRecordVersioned
{
    /**
     * A search attribute to allow searching for worklists that are valid for a particular date.
     *
     * @var DateTime
     */
    public $on;

    /**
     * A search attribute to allow searching for worklists where the given date & time would be valid
     * for the Worklist.
     *
     * @var DateTime
     */
    public $at;

    /**
     * A search attribute to specify if we only want to search for worklists that are automatic or manual.
     *
     * @var bool
     */
    public $automatic;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'worklist';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, start, end, description, scheduled, worklist_definition_id', 'safe'),
            array('name', 'required'),
            array('name', 'length', 'max' => 100),
            array('description', 'length', 'max' => 1000),
            array('start', 'OEDateValidator'),
            array('end', 'OEDateValidator'),
            array('start', 'OEDateCompareValidator', 'compareAttribute' => 'end', 'allowEmpty' => true,
                'operator' => '<=', 'message' => '{attribute} must be on or before {compareAttribute}', ),
            array('scheduled', 'boolean', 'allowEmpty' => false),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, start, end, description, scheduled, worklist_definition_id', 'safe', 'on' => 'search'),
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
            'mapping_attributes' => array(self::HAS_MANY, 'WorklistAttribute', 'worklist_id'),
            'worklist_patients' => array(self::HAS_MANY, 'WorklistPatient', 'worklist_id'),
            'worklist_definition' => array(self::BELONGS_TO, 'WorklistDefinition', 'worklist_definition_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'start' => 'Start Date',
            'end' => 'End Date',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @param $pagination boolean  - whether to paginate the results of the search or not
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($pagination = true)
    {
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('scheduled', $this->scheduled, false);

        if ($this->at) {
            $check_date = $this->at->format('Y-m-d H:i:s');
            $criteria->addCondition(':cd >= start AND :cd < end');
            $criteria->params = array_merge($criteria->params, array(
                ':cd' => $check_date,
            ));
        } elseif ($this->on) {
            $sdate = $this->on->format('Y-m-d') . ' 00:00:00';
            $edate = $this->on->format('Y-m-d') . ' 23:59:59';
            $criteria->addCondition(':sd <= start AND :ed >= end');
            $criteria->params = array_merge($criteria->params, array(
                ':sd' => $sdate,
                ':ed' => $edate,
            ));
        }

        $criteria->order = 'start asc';

        if (!is_null($this->automatic)) {
            if ($this->automatic) {
                $criteria->addCondition('t.worklist_definition_id IS NOT NULL');
                // add the worklist definition to the query to access it's relative display order
                $this->with('worklist_definition');
                $criteria->order .= ', worklist_definition.display_order asc';
            } else {
                $criteria->addCondition('worklist_definition_id IS NULL');
            }
        }

        $args = array('criteria' => $criteria);
        if (!$pagination) {
            $args['pagination'] = false;
        }

        return new CActiveDataProvider(get_class($this), $args);
    }

    /**
     * @return string
     */
    public function getDisplayDate()
    {
        $start = $this->NHSDate('start');
        $end = $this->NHSDate('end');
        if ($start != $end) {
            return "{$start} - {$end}";
        }

        return $start;
    }

    /**
     * @return string
     */
    public function getDisplayShortDate()
    {
        // Short date only uses start date (as it is for situations where string length is an issue)
        return $this->NHSDate('start');
    }

    /**
     * Get array of mapping attributes for this Worklist, indexed by the name value of the attribute.
     *
     * @return array
     */
    public function getMappingAttributeIdsByName()
    {
        $res = array();
        foreach ($this->mapping_attributes as $attr) {
            $res[$attr->name] = $attr->id;
        }

        return $res;
    }

    /**
     * Return worklist attributes with values
     *
     * @return WorklistAttribute[]
     */
    public function getDisplayed_mapping_attributes()
    {
        $criteria = new CDbCriteria();

        $criteria->join = 'JOIN worklist w ON t.worklist_id = w.id JOIN worklist_definition_mapping wdm ON wdm.worklist_definition_id = w.worklist_definition_id AND wdm.key = t.name';

        $criteria->addCondition('t.worklist_id = :worklist_id');
        $criteria->addCondition('wdm.display_order IS NOT NULL');
        $criteria->params = [':worklist_id' => $this->id];
        $criteria->order = 'wdm.display_order ASC';

        return WorklistAttribute::model()->cache(30)->findAll($criteria);
    }
}
