<?php

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\widgets\ContrastSensitivity as ContrastSensitivityWidget;

class ContrastSensitivity extends \BaseEventTypeElement
{
    use traits\CustomOrdering;

    public $widgetClass = ContrastSensitivityWidget::class;
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    /**
     * @return string of the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_contrastsensitivity';
    }

    public function rules()
    {
        return [
            ['event_id, results, comments', 'safe'],
            ['results', \OERequiredIfOtherAttributesEmptyValidator::class,
                'other_attributes' => ['comments'],
                'message' => '{attribute} cannot be blank without comment'],
            ['results', 'limitResultsToOneValuePerLateralityPerType',
                'message' => 'Only one result for a test type can be recorded for a laterality'],
            ['comments', 'length', 'min' => 5]
        ];
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'results' => [self::HAS_MANY, ContrastSensitivity_Result::class, 'element_id']
        ];
    }

    public function attributeLabels()
    {
        return [
            'comments' => 'Comments',
        ];
    }

    /**
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function getLetter_string()
    {
        return \Yii::app()->getController()->renderPartial(
            'application.modules.OphCiExamination.views.default.letter.ContrastSensitivity',
            [
                'element' => $this,
            ],
            true
        );
    }

    /**
     * @return array
     */
    public function getResultsGroupedByTestType()
    {
        $grouped_results = [];

        foreach ($this->results as $result) {
            if (!array_key_exists($result->contrastsensitivity_type->name, $grouped_results)) {
                $grouped_results[$result->contrastsensitivity_type->name] = [];
            }

            $grouped_results[$result->contrastsensitivity_type->name][$result->eye_id] = $result;
        }

        return $grouped_results;
    }

    /**
     * @return boolean
     */
    public function hasTestLateralityResultDuplication()
    {
        $results_exist = [];

        foreach ($this->results as $result) {
            if (!array_key_exists($result->contrastsensitivity_type_id, $results_exist)) {
                $results_exist[$result->contrastsensitivity_type_id] = [];
            }

            // if the laterality key already exists within this test then there is a duplication
            if (array_key_exists($result->eye_id, $results_exist[$result->contrastsensitivity_type_id])) {
                return true;
            } else {
                $results_exist[$result->contrastsensitivity_type_id][$result->eye_id] = true;
            }
        }

        return false;
    }

    public function limitResultsToOneValuePerLateralityPerType($attribute, $params)
    {
        if ($this->hasTestLateralityResultDuplication()) {
            if (!@$params['message']) {
                $params['message'] = '{attribute}: Only one permitted per laterality per test';
            }
            $params['{attribute}'] = $this->getAttributeLabel($attribute);

            $this->addError($attribute, strtr($params['message'], $params));
        }
    }
}
