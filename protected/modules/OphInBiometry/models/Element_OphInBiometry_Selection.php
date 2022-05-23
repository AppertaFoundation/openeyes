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
 * This is the model class for table "et_ophinbiometry_selection".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property string $iol_power
 * @property string $predicted_refraction
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 */
class Element_OphInBiometry_Selection extends SplitEventTypeElement
{
    public $service;


    /**
     * set defaults
     */
    public function init()
    {
        $this->iol_power_left = null;
        $this->iol_power_right = null;
        $this->predicted_refraction_left = null;
        $this->predicted_refraction_right = null;
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'et_ophinbiometry_selection';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, predicted_refraction_left, predicted_refraction_right, lens_id_left, lens_id_right, formula_id_left, formula_id_right', 'safe'),
            array('iol_power_left', 'requiredIfLensSelected' , 'side' => 'left'),
            array('iol_power_right', 'requiredIfLensSelected' ,'side' => 'right'),
            array('manually_overriden_left, manually_overriden_right', 'required'),
            // The following rule is used by search().
            array('iol_power_left, predicted_refraction_left, iol_power_right, predicted_refraction_right', 'match', 'pattern' => '/([0-9]*?)(\.[0-9]{0,2})?/'),
            array('iol_power_left', 'checkNumericRangeIfSide', 'side' => 'left', 'max' => 40, 'min' => -10),
            array('iol_power_right', 'checkNumericRangeIfSide', 'side' => 'right', 'max' => 40, 'min' => -10),
            array('predicted_refraction_left', 'checkNumericRangeIfSide', 'side' => 'left', 'max' => 10, 'min' => -10),
            array('predicted_refraction_right', 'checkNumericRangeIfSide', 'side' => 'right', 'max' => 10, 'min' => -10),
            array('lens_id_left', 'checkSelectedLensFormula', 'selectData' => 'left_lens'),
            array('formula_id_left', 'checkSelectedLensFormula', 'selectData' => 'left_formula'),
            array('lens_id_right', 'checkSelectedLensFormula', 'selectData' => 'right_lens'),
            array('formula_id_right', 'checkSelectedLensFormula', 'selectData' => 'right_formula'),
            array('lens_id_left, lens_id_right, formula_id_left, formula_id_right', 'default', 'setOnEmpty' => true, 'value' => 0),

            // Please remove those attributes that should not be searched.
            array('id, event_id ', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function checkSelectedLensFormula($attribute, $params)
    {
        if (count(OphInBiometry_Imported_Events::model()->findAllByAttributes(array('event_id' => $this->event_id))) > 0) {
            $lensCount = count(Element_OphInBiometry_IolRefValues::model()->findAll(array('select' => 'lens_id', 'condition' => 'event_id=' . $this->event_id, 'distinct' => 'true')));
            $formulaCount = count(Element_OphInBiometry_IolRefValues::model()->findAll(array('select' => 'formula_id', 'condition' => 'event_id=' . $this->event_id, 'distinct' => 'true')));

            if ($params['selectData'] == 'left_lens' && $formulaCount > 1 && $lensCount > 1) {
                if (
                    ((empty($this->formula_id_left)) && ((!empty($this->lens_id_left))))
                    || ((!empty($this->formula_id_left)) && ((empty($this->lens_id_left))))
                ) {
                    if (empty($this->lens_id_left)) {
                        $this->addError('lens_id_left', 'Lens and Formula must be selected or left blank (Left side).');
                    }
                }
            } elseif ($params['selectData'] == 'left_formula' && $lensCount > 1 &&  $formulaCount > 1) {
                if (
                    ((empty($this->formula_id_left)) && ((!empty($this->lens_id_left))))
                    || ((!empty($this->formula_id_left)) && ((empty($this->lens_id_left))))
                ) {
                    if (empty($this->formula_id_left) && !$this->manually_overriden_left) {
                        $this->addError('formula_id_left', 'Lens and Formula must be selected or left blank (Left side).');
                    }
                }
            } elseif ($params['selectData'] == 'right_lens' && $formulaCount > 1 && $lensCount > 1) {
                if (
                    ((empty($this->formula_id_right)) && ((!empty($this->lens_id_right))))
                    || ((!empty($this->formula_id_right)) && ((empty($this->lens_id_right))))
                ) {
                    if (empty($this->lens_id_right)) {
                        $this->addError('lens_id_right', 'Lens and Formula must be selected or left blank (Right side).');
                    }
                }
            } elseif ($params['selectData'] == 'right_formula' && $lensCount > 1 &&  $formulaCount > 1) {
                if (
                    ((empty($this->formula_id_right)) && ((!empty($this->lens_id_right))))
                    || ((!empty($this->formula_id_right)) && ((empty($this->lens_id_right))))
                ) {
                    if (empty($this->formula_id_right)  && !$this->manually_overriden_right) {
                        $this->addError('formula_id_right', 'Lens and Formula must be selected or left blank (Right side).');
                    }
                }
            }
        }
    }

    public function requiredIfLensSelected($attribute, $params)
    {
        if (
            !(isset($this->$attribute) && $this->$attribute) &&
            isset($this->{'lens_id_' . $params['side']}) && $this->{'lens_id_' . $params['side']}
        ) {
            $this->addError($attribute, $this->getAttributeLabel($attribute) . ' cannot be blank (' . $params['side'] . ' side).');
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='" . get_class($this) . "'"),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'lens_left' => array(self::BELONGS_TO, 'OphInBiometry_LensType_Lens', 'lens_id_left'),
            'lens_right' => array(self::BELONGS_TO, 'OphInBiometry_LensType_Lens', 'lens_id_right'),
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
            'iol_power_left' => 'IOL Power',
            'predicted_refraction_left' => 'Predicted Refraction',
            'iol_power_right' => 'IOL Power',
            'predicted_refraction_right' => 'Predicted Refraction',
            'lens_id' => 'Lens',
            'lens_id_right' => 'Lens',
            'lens_id_left' => 'Lens',
            'formula_id_right' => 'Formula',
            'formula_id_left' => 'Formula',

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
        $criteria->compare('event_id', $this->event_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function isRequiredInUI()
    {
        return true;
    }

    public function beforeSave()
    {
        // We want to delete the formula if the side is manually overridden as you cannot set formula id
        // When it's in manual override
        foreach (['left', 'right'] as $eye_side) {
            if ($this->{"manually_overriden_" . $eye_side}) {
                $this->{"formula_id_" . $eye_side} = null;
            }
        }
        return parent::beforeSave();
    }
}
