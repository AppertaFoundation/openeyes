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

namespace OEModule\OphCiPhasing\models;

use SplitEventTypeElement;
use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "et_ophciphasing_intraocularpressure".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property int $left_instrument_id
 * @property int $right_instrument_id
 * @property bool $left_dilated
 * @property bool $right_dilated
 * @property string $left_comments
 * @property string $right_comments
 */
class Element_OphCiPhasing_IntraocularPressure extends SplitEventTypeElement
{
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciphasing_intraocularpressure';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('eye_id', 'required'),
                array('right_instrument_id, right_dilated, right_readings', 'requiredIfSide', 'side' => 'right'),
                array('left_instrument_id, left_dilated, left_readings', 'requiredIfSide', 'side' => 'left'),
                array('event_id, left_comments, right_comments', 'safe'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id, eye_id, left_comments, right_comments, left_instrument_id,
						right_instrument_id, right_dilated, left_dilated', 'safe', 'on' => 'search'),
        );
    }

    public function sidedFields()
    {
        return array('comments', 'instrument_id', 'dilated');
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
                'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
                'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'readings' => array(self::HAS_MANY, OphCiPhasing_Reading::class, 'element_id'),
                'right_readings' => array(self::HAS_MANY, OphCiPhasing_Reading::class, 'element_id', 'on' => 'right_readings.side = '.OphCiPhasing_Reading::RIGHT),
                'left_readings' => array(self::HAS_MANY, OphCiPhasing_Reading::class, 'element_id', 'on' => 'left_readings.side = '.OphCiPhasing_Reading::LEFT),
                'right_instrument' => array(self::BELONGS_TO, OphCiPhasing_Instrument::class, 'right_instrument_id'),
                'left_instrument' => array(self::BELONGS_TO, OphCiPhasing_Instrument::class, 'left_instrument_id'),
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
                'left_comments' => 'Comments',
                'right_comments' => 'Comments',
                'left_instrument_id' => 'Instrument',
                'right_instrument_id' => 'Instrument',
                'left_dilated' => 'Dilated',
                'right_dilated' => 'Dilated',
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

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Remove readings for this element.
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function beforeDelete()
    {
        foreach ($this->readings as $reading) {
            if (!$reading->delete()) {
                throw new \Exception('Delete reading failed: '.print_r($reading->getErrors(), true));
            }
        }

        return parent::beforeDelete();
    }

    /**
     * Validate readings.
     */
    protected function afterValidate()
    {
        foreach (array('right', 'left') as $side) {
            foreach ($this->{$side.'_readings'} as $i => $reading) {
                if (!$reading->validate()) {
                    foreach ($reading->getErrors() as $fld => $err) {
                        $this->addError($side.'_reading', ucfirst($side).' reading ('.($i + 1).'): '.implode(', ', $err));
                    }
                }
            }
        }

        return parent::afterValidate();
    }

    public function getReadings($side)
    {
        switch ($side) {
            case "right":
                $side = '0';
            break;
            case "left":
                $side = '1';
            break;
            default:
            break;
        }

        $readings = array();

        $criteria = new \CDbCriteria();
        $criteria->addCondition('element_id = :eid');
        $criteria->addCondition('side = :sid');
        $criteria->params = array(':eid' => $this->id, ':sid' => $side);

        foreach (OphCiPhasing_Reading::model()->findAll($criteria) as $reading) {
                    $readings[] = $reading->value;
        }

        return $readings;
    }

    public function updateReadings($side, $data)
    {
        $side_str = ($side == OphCiPhasing_Reading::RIGHT) ? 'right' : 'left';
        $curr_by_id = array();
        $criteria = new \CDbCriteria();
        $criteria->addCondition('element_id = :eid');
        $criteria->addCondition('side = :sid');
        $criteria->params = array(':eid' => $this->id, ':sid' => $side);
        foreach (OphCiPhasing_Reading::model()->findAll($criteria) as $reading) {
            $curr_by_id[$reading->id] = $reading;
        }

        foreach ($data as $item) {
            if (@$item['id']) {
                // this will throw an exception if it doesn't exist, which is probably a good thing
                // as it suggests that this record has been updated from elsewhere.
                $reading = $curr_by_id[$item['id']];
                unset($curr_by_id[$item['id']]);
            } else {
                $reading = new OphCiPhasing_Reading();
                $reading->element_id = $this->id;
                $reading->side = $side;
            }
            $mesTm = $item['measurement_timestamp'];
            if (!strpos($item['measurement_timestamp'], ':')) {
                $mesTm = substr_replace($mesTm, ':', -2, 0);
            }
            $reading->measurement_timestamp = $mesTm;
            $reading->value = $item['value'];
            $reading->save();
        }

        foreach ($curr_by_id as $old_reading) {
            $old_reading->delete();
        }
    }
}
