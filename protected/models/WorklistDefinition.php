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

use OE\factories\models\traits\HasFactory;
use RRule\RRule;

/**
 * Class WorklistDefinition.
 *
 * The followings are the available columns in table:
 *
 * @property int                                $id
 * @property int                                $patient_identifier_type_id
 * @property string                             $name
 * @property string                             $rrule
 * @property string                             $description
 * @property string                             $worklist_name
 * @property string                             $start_time
 * @property string                             $end_time
 * @property DateTime                           $active_from
 * @property DateTime                           $active_until
 * @property int                                $pathway_type_id
 * @property Worklist[]                         $worklists
 * @property WorklistDefinitionMapping[]        $mappings
 * @property WorklistDefinitionMappingp[]       $displayed_mappings
 * @property WorklistDefinitionMappingp[]       $hidden_mappings
 * @property WorklistDefinitionDisplayContext[] $display_contexts
 * @property PatientIdentifierType $patient_identifier_type
 * @property PathwayType $pathway_type
 */
class WorklistDefinition extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'worklist_definition';
    }

    public function scopes()
    {
        return [
            // returning all worklist EXCEPT those have UNBOOKED keys
            'withoutUnbooked' => [
                'with' => [
                    'mappings' => [
                        'condition' => 'mappings.key != "UNBOOKED" OR mappings.key IS NULL'
                    ]
                ],
            ],
            'displayOrder' => ['order' => 'display_order ASC']
        ];
    }

    public function beforeValidate()
    {
        $purifier = new CHtmlPurifier();

        $unclean_name = $this->name;
        $clean_name = $purifier->purify($this->name);
        if ($unclean_name != $clean_name) {
            $this->addErrors(array("Worklist name contains forbidden characters"));
        } else {
            $this->name = $clean_name;
        }

        if ( preg_match('/^(\d{2}):(\d{2})$/', $this->start_time)) {
            // the format is 00:00, we need to append :00
            $this->start_time .= ':00';
        }

        if ( preg_match('/^(\d{2}):(\d{2})$/', $this->end_time)) {
            // the format is 00:00, we need to append :00
            $this->end_time .= ':00';
        }

        return parent::beforeValidate();
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, rrule, worklist_name, start_time, end_time, description, patient_identifier_type_id', 'safe'),
            array('rrule', 'validateRrule'),
            array('name, rrule, start_time, end_time, patient_identifier_type_id, pathway_type_id', 'required'),
            array('name', 'length', 'max' => 100),
            array('description', 'length', 'max' => 1000),
            array('start_time, end_time', 'type', 'type'=>'time', 'timeFormat'=>'hh:mm:ss', 'except' => 'sortDisplayOrder'),
            array('active_from, active_until', 'OEDateValidator'),
            array(
                'active_from',
                'OEDateCompareValidator',
                'compareAttribute' => 'active_until',
                'allowEmpty' => true,
                'allowCompareEmpty' => true,
                'operator' => '<=',
                'message' => '{attribute} must be on or before {compareAttribute}',
            ),
            array('active_from', 'default', 'setOnEmpty' => true, 'value' => date("Y-m-d H:i:s", time())),
            array('active_until', 'default', 'setOnEmpty' => true, 'value' => null),
            array('scheduled', 'boolean', 'allowEmpty' => false),
            array('display_order', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'id, name, rrule, worklist_name, start_time, end_time, description, scheduled, patient_identifier_type_id',
                'safe',
                'on' => 'search',
            ),
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
            'worklists' => array(self::HAS_MANY, 'Worklist', 'worklist_definition_id'),
            'mappings' => array(self::HAS_MANY, 'WorklistDefinitionMapping', 'worklist_definition_id'),
            'displayed_mappings' => array(
                self::HAS_MANY,
                'WorklistDefinitionMapping',
                'worklist_definition_id',
                'on' => 'display_order is NOT NULL',
                'order' => 'display_order ASC',
            ),
            'hidden_mappings' => array(
                self::HAS_MANY,
                'WorklistDefinitionMapping',
                'worklist_definition_id',
                'on' => 'display_order is NULL',
            ),
            'display_contexts' => array(self::HAS_MANY, 'WorklistDefinitionDisplayContext', 'worklist_definition_id'),
            'patient_identifier_type' => array(self::BELONGS_TO, 'PatientIdentifierType', 'patient_identifier_type_id'),
            'pathway_type' => array(self::BELONGS_TO, 'PathwayType', 'pathway_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'rrule' => 'Frequency',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('worklist_name', $this->worklist_name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('scheduled', $this->scheduled, true);

        // TODO: proper support for date/time search

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function afterFind()
    {
        // strip out the seconds from the time field
        foreach (array('start_time', 'end_time') as $time_attr) {
            $this->$time_attr = substr($this->$time_attr, 0, 5);
        }

        parent::afterFind();
    }

    /**
     * Simple wrapper around RRule construction to validate the string definition.
     *
     * @param $attribute
     */
    public function validateRrule($attribute)
    {
        if (empty($this->$attribute)) {
            return;
        }

        $valid = true;
        try {
            if (strpos($this->$attribute, '=') === false) {
                // rrule instantiation falls over if no equals is found during parsing
                // so this is a bit of a dirty hack to deal with that.
                $valid = false;
            } else {
                $rrule = new RRule($this->$attribute);
            }
        } catch (Exception $e) {
            $valid = false;
        }
        if (!$valid) {
            $this->addError($attribute, $this->getAttributeLabel($attribute) . ' is not valid');
        }
    }

    /**
     * Check whether the given key would be unique on the Definition
     * (optional id indicates the current mapping the key is from so it is not checked against itself).
     *
     * @param     $key
     * @param int $id
     *
     * @return bool
     */
    public function validateMappingKey($key, $id = null)
    {
        foreach ($this->mappings as $m) {
            if ($m->id == $id) {
                continue;
            }
            if ($m->key == $key) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets the next display order for a new mapping.
     *
     * @return int
     */
    public function getNextDisplayOrder()
    {
        $display_order = 1;
        foreach ($this->mappings as $m) {
            if ($m->display_order >= $display_order) {
                $display_order = $m->display_order + 1;
            }
        }

        return $display_order;
    }

    /**
     * Simple wrapper function to convert the RRule into its human readable form.
     *
     * @return string
     */
    public function getRruleHumanReadable()
    {
        if ($this->rrule) {
            $rrule_str = $this->rrule;
            // ensure rrule string has a start date if not defined so that it will be part of the
            // formatted output
            $dtstart = null;
            if (!$this->isNewRecord && !strpos($rrule_str, 'DTSTART=')) {
                $created_date = new DateTime($this->created_date);
                $dtstart = $created_date->format('Y-m-d');
            }

            $final_rrule = new RRule($rrule_str, $dtstart);

            return $final_rrule->humanReadable(array(
                'date_formatter' => function ($d) {
                    return $d->format(Helper::NHS_DATE_FORMAT);
                },
            ));
        }
    }

    /**
     * @return CDbDataReader|mixed|string
     */
    public function getWorklistCount()
    {
        $sql = 'SELECT COUNT(id) FROM worklist where worklist_definition_id = ' . $this->id;

        return $this->getDbConnection()->createCommand($sql)->queryScalar();
    }

    /**
     * @return CDbDataReader|mixed|string
     */
    public function getMappingCount()
    {
        $sql = 'SELECT COUNT(id) FROM worklist_definition_mapping where worklist_definition_id = ' . $this->id;

        return $this->getDbConnection()->createCommand($sql)->queryScalar();
    }

    /**
     * @return CDbDataReader|mixed|string
     */
    public function getdisplayContextCount()
    {
        $sql = 'SELECT COUNT(id) FROM worklist_definition_display_context where worklist_definition_id = ' . $this->id;

        return $this->getDbConnection()->createCommand($sql)->queryScalar();
    }
}
