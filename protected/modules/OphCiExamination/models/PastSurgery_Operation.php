<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;


/**
 * Class PastSurgery_Operation
 * @package OEModule\OphCiExamination\models
 *
 * @property int $element_id
 * @property int $side_id
 * @property string $operation
 * @property string $date
 * @property string $had_operation
 *
 * @property \Eye $side
 * @property PastSurgery $element
 */
class PastSurgery_Operation extends \BaseEventTypeElement
{

    public static $PRESENT = 1;
    public static $NOT_PRESENT = 0;
    public static $NOT_CHECKED = -9;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return static
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
        return 'ophciexamination_pastsurgery_op';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('operation', 'required'),
            array('date, side_id, operation, had_operation', 'safe'),
            array('date', 'OEFuzzyDateValidatorNotFuture'),
            array('had_operation', 'required', 'message' => 'Checked Status cannot be blank'),
            array('side_id', 'sideValidator'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, date, operation, had_operation', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\PastSurgery', 'element_id'),
            'side' => array(self::BELONGS_TO, 'Eye', 'side_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'operation' => 'Operation',
            'date' => 'Date',
            'had_operation' => 'Had operation'
        );
    }

    /**
     * Checking whether side_id is not null
     * @param $attribute
     * @param $params
     */
    public function sideValidator($attribute, $params)
    {
        if (!$this->side_id) {
            $this->addError($attribute, "Eye must be selected");
        }
    }

    /**
     * @inheritdoc
     * @return bool
     */
    public function beforeSave()
    {
        //-9 is the N/A option but we do not save it, if null is posted that means
        //the user did not checked any checkbox so we return error in the validation part
        if ($this->side_id == -9) {
            $this->side_id = null;
        }
        return parent::beforeSave();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('operation', $this->operation, true);
        $criteria->compare('date', $this->date);
        $criteria->compare('had_operation', $this->had_operation, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return mixed
     */
    public function getDisplayDate()
    {
        return \Helper::formatFuzzyDate($this->date);
    }

    public function getDisplayHasOperation()
    {
        if ($this->had_operation === (string) static::$PRESENT) {
            return 'Present';
        } elseif ($this->had_operation === (string) static::$NOT_PRESENT) {
            return 'Not present';
        }
        return 'Not checked';
    }

    /**
     * @return string
     */
    public function getDisplayOperation($present_prefix = true)
    {
        $display_has_operation = $present_prefix ? ('<strong>' . $this->getDisplayHasOperation() . ':</strong> ') : '';
        return  $display_has_operation . ($this->side ? $this->side->adjective  . ' ' : '') . $this->operation;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDisplayDate() . ' ' . $this->getDisplayOperation();
    }
}