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
 * This is the model class for table "patient_shortcode".
 *
 * The followings are the available columns in table 'patient_shortcode':
 *
 * @property string $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property Event[] $events
 */
class PatientShortcode extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return PatientShortcode the static model class
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
        return 'patient_shortcode';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('code, default_code, method, description, event_type_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name', 'safe', 'on' => 'search'),
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
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'event_type_id' => 'Event Type',
            'codedoc' => 'Code Documentation'
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function replaceText($text, $patient, $ucfirst = false)
    {
        $code = $ucfirst ? ucfirst($this->code) : $this->code;

        if ($this->eventType) {
            if ($api = Yii::app()->moduleAPI->get($this->eventType->class_name)) {
                if (method_exists($api, $this->method)) {
                    return preg_replace('/\['.$code.'\]/', $api->{$this->method}($patient, !$this->global_scope), $text);
                }
                throw new Exception("Unknown API method in {$this->eventType->class_name}: $this->method");
            }
        } elseif (property_exists($patient, $this->code) || method_exists($patient, 'get'.ucfirst($this->code))) {
            if ($ucfirst) {
                return preg_replace('/\['.$code.'\]/', ucfirst($patient->{$this->code}), $text);
            }

            return preg_replace('/\['.$code.'\]/', $patient->{$this->code}, $text);
        } else {
            $api = new CoreAPI();
            if (method_exists($api, 'get'.ucfirst($this->code))) {
                $result = $api->{'get' . ucfirst($this->code)}($patient);
                if ($ucfirst) {
                    $result = ucfirst($result);
                }
                return preg_replace('/\[' . $code . '\]/', $result, $text);
            }
        }
        return $text;
    }

    /**
     * @param $value
     */
    public static function formatDocComment($value)
    {
        // simple parser to format a docblock to only render the description
        if (preg_match('/\/\*\*([^\/@]*).*/s', $value, $output)) {
            return trim(preg_replace('/\s*\*\s*/s', ' ', $output[1]));
        }

    }


    /**
     * @return string
     */
    public function getcodedoc()
    {
        if ($this->eventType) {
            if ($api = Yii::app()->moduleAPI->get($this->eventType->class_name)) {
                $r = new ReflectionClass($api);
                if ($r->hasMethod($this->method) && $m = $r->getMethod($this->method)) {
                    return static::formatDocComment($m->getDocComment());
                }
            }
        }
    }
}
