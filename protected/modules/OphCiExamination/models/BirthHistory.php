<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\widgets\BirthHistory as BirthHistoryWidget;

/**
 * Class BirthHistory
 *
 * This uses a couple of psuedo properties for input weight so that all weight values can be
 * set on the instance from a single input value assigment. The lbs/ozs input is expected to
 * be a single number [lbs].[ozs] ... so the maximum decimal places value .16 This value is
 * split in display.
 *
 * @package OEModule\OphCiExamination\models
 * @property integer $id
 * @property integer $event_id
 * @property integer $created_user_id
 * @property integer $last_modified_user_id
 * @property integer $birth_history_delivery_type_id
 * @property string $weight_recorded_units
 * @property integer $weight_grams
 * @property integer $weight_ozs
 * @property integer $gestation_weeks
 * @property integer $had_neonatal_specialist_care
 * @property string $display_had_neonatal_specialist_care
 * @property integer $was_multiple_birth
 * @property string display_was_multiple_birth
 * @property string $comments
 *
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property BirthHistory_DeliveryType $delivery_type
 */
class BirthHistory extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    use traits\HasRelationOptions {
        __get as __relationOptionsGet;
    }

    protected $widgetClass = BirthHistoryWidget::class;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $after_validation_disabled = false;

    public static $WEIGHT_GRAMS = 'g';
    public static $WEIGHT_OZS = 'oz';

    public static $YES = 1;
    public static $NO = 0;
    public static $NOT_RECORDED = -1;

    private $external_errors;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_birthhistory';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            [
                'event_id, birth_history_delivery_type_id, weight_grams, weight_ozs, gestation_weeks, ' .
                'had_neonatal_specialist_care, was_multiple_birth, comments', 'safe'
            ],
            [
                'birth_history_delivery_type_id, weight_grams, weight_ozs, gestation_weeks, ' .
                'had_neonatal_specialist_care, was_multiple_birth', \OEAtLeastOneRequiredValidator::class],
            [
                'birth_history_delivery_type_id', 'exist', 'allowEmpty' => true, 'attributeName' => 'id',
                'className' => BirthHistory_DeliveryType::class,
                'message' => '{attribute} is invalid'
            ],
            ['weight_grams', 'numerical', 'integerOnly' => true, 'min' => '225', 'max' => '10000'],
            ['weight_ozs', 'numerical', 'integerOnly' => true, 'min' => '8', 'max' => '354'],
            ['gestation_weeks', 'numerical', 'integerOnly' => true, 'min' => '20', 'max' => '42'],
            // integer rule to ensure random data is not accceptable
            [
                'had_neonatal_specialist_care, was_multiple_birth', 'numerical', 'integerOnly' => true,
                'message' => '{attribute} is invalid'
            ],
            // actual valid values
            [
                'had_neonatal_specialist_care, was_multiple_birth', 'in',
                'range' => [static::$YES, static::$NO, static::$NOT_RECORDED],
                'message' => '{attribute} is invalid'
            ],
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            [
                'id, event_id, birth_history_delivery_type_id, weight_recorded_units, weight_grams, weight_ozs, ' .
                'gestation_weeks, had_neonatal_specialist_care, was_multiple_birth',  'safe', 'on' => 'search']
        ];
    }

    public function afterValidate()
    {
        if ($this->weight_recorded_units === static::$WEIGHT_GRAMS) {
            $this->clearErrors('weight_ozs');
        }
        if ($this->weight_recorded_units === static::$WEIGHT_OZS) {
            $this->clearErrors('weight_grams');
        }

        parent::afterValidate();
    }

    /**
     * @return array
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'delivery_type' => [self::BELONGS_TO, BirthHistory_DeliveryType::class, 'birth_history_delivery_type_id']
        ];
    }

    public function attributeLabels()
    {
        return [
            'weight_grams' => 'Weight (g)',
            'weight_ozs' => 'Weight (ozs)',
            'birth_history_delivery_type_id' => 'Delivery',
            'gestation_weeks' => 'Gestation (wks)',
            'had_neonatal_specialist_care' => 'SCBU/NSCU',
            'was_multiple_birth' => 'Multiple birth'
        ];
    }

    /**
     * @param null $attribute
     * @return array|mixed
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        $errors = parent::getErrors($attribute);

        if (!$this->external_errors) {
            return $errors;
        }

        // support for externally added errors
        if ($attribute === null) {
            foreach ($this->external_errors as $attr => $external_errors) {
                if (!isset($errors[$attr])) {
                    $errors[$attr] = [];
                }
                $errors[$attr] = array_merge($errors[$attr], $external_errors);
            }
            return $errors;
        }
        if (array_key_exists($attribute, $this->external_errors)) {
            return array_merge($errors, $this->external_errors[$attribute]);
        }
    }

    /**
     * @param null $attribute
     * @return mixed|null
     * @inheritDoc
     */
    public function getError($attribute = null)
    {
        $errors = parent::getError($attribute);

        // support for externally added errors
        if (!$errors && $attribute !== null and ($this->external_errors[$attribute] ?? false)) {
            $errors = $this->external_errors[$attribute][0];
        }
        return $errors;
    }


    /**
     * Here for the widget to set errors on the model, because OpenEyes validation
     * relies purely on model validation responses.
     *
     * @param $attribute
     * @param $error
     */
    public function addExternalError($attribute, $error)
    {
        if (!$this->external_errors) {
            $this->external_errors = [];
        }
        if (!isset($this->external_errors[$attribute])) {
            $this->external_errors[$attribute] = [];
        }
        $this->external_errors[$attribute][] = $error;
    }

    /**
     * @param null $attributes
     * @param bool $clearErrors
     * @return bool
     * @inheritDoc
     */
    public function validate($attributes = null, $clearErrors = true)
    {
        // overridden to provide support for externally added validation errors
        return parent::validate($attributes, $clearErrors) && !$this->external_errors;
    }

    public function getLetter_string()
    {
        $result = $this->getElementTypeName() . ":";
        $result .= $this->display_weight ? " {$this->display_weight}" : "";
        $result .= $this->delivery_type ? " {$this->delivery_type}" : "";
        $result .= $this->gestation_weeks ? " {$this->display_gestation_weeks}" : "";
        $result .= $this->display_had_neonatal_specialist_care ?
            " {$this->display_labelled_had_neonatal_specialist_care}"
            : "";
        $result .= $this->display_was_multiple_birth ?
            " {$this->display_labelled_was_multiple_birth}"
            : "";
        $result .= $this->comments ? ". {$this->comments}" : "";

        return $result;
    }

    public function getnr_boolean_options()
    {
        return [
            ['id' => static::$YES, 'name' => 'Yes'],
            ['id' => static::$NO, 'name' => 'No'],
            ['id' => static::$NOT_RECORDED, 'name' => 'Not Recorded'],
        ];
    }

    /**
     * Convenience accessor for displaying the weight in the units they were recorded
     *
     * @return string|null
     */
    public function getDisplay_weight()
    {
        if (!$this->weight_recorded_units) {
            return null;
        }

        return $this->weight_recorded_units === static::$WEIGHT_GRAMS ?
            $this->display_weight_kgs() :
            $this->display_weight_lbs_ozs();
    }

    public function getDisplay_gestation_weeks()
    {
        return $this->gestation_weeks . ' wks';
    }

    public function getDisplay_had_neonatal_specialist_care()
    {
        return $this->get_display_nr_boolean($this->had_neonatal_specialist_care);
    }

    /** Accessor for pro view display of data */
    public function getDisplay_labelled_had_neonatal_specialist_care()
    {
        return $this->getAttributeLabel('had_neonatal_specialist_care') . ": " .$this->display_had_neonatal_specialist_care;
    }

    public function getDisplay_was_multiple_birth()
    {
        return $this->get_display_nr_boolean($this->was_multiple_birth);
    }

    /** Accessor for pro view display of data */
    public function getDisplay_labelled_was_multiple_birth()
    {
        return $this->getAttributeLabel('was_multiple_birth') . ": " . $this->display_was_multiple_birth;
    }

    public function calc_input_lbs()
    {
        return floor($this->weight_ozs / 16);
    }

    public function calc_input_ozs()
    {
        return $this->weight_ozs % 16;
    }

    protected function get_display_nr_boolean($value)
    {
        $value = (string) $value;
        if ($value ===  (string) static::$YES) {
            return 'Yes';
        }
        if ($value === (string) static::$NO) {
            return 'No';
        }
        if ($value === (string) static::$NOT_RECORDED) {
            return 'Not Recorded';
        }
    }

    protected function display_weight_kgs()
    {
        return ($this->weight_grams !== null ? number_format($this->weight_grams / 1000, 3) : '0') . 'kg';
    }

    protected function display_weight_lbs_ozs()
    {
        if (!$this->weight_ozs) {
            return '0oz';
        }

        return $this->calc_input_lbs() . "lb " . $this->calc_input_ozs() . "oz";
    }
}
