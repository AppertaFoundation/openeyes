<?php

namespace OEModule\OphCiExamination\models;


/**
 * Class StereoAcuity_Entry
 * @package OEModule\OphCiExamination\models
 * @property $correctiontype_id
 * @property CorrectionType $correctiontype
 * @property $method_id
 * @property StereoAcuity_Method $method
 * @property string $result
 * @property int $inconclusive
 */
class StereoAcuity_Entry extends \BaseElement
{
    use traits\HasCorrectionType;
    use traits\HasRelationOptions;
    use traits\HasWithHeadPosture;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $correction_type_attributes = ['correctiontype_id'];

    public const INCONCLUSIVE = '1';
    public const DISPLAY_INCONCLUSIVE = 'Inconclusive';
    public const NOT_INCONCLUSIVE = '0';
    public const DISPLAY_NOT_INCONCLUSIVE = 'Record value';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_stereoacuity_entry';
    }

    public function rules()
    {
        return array_merge(
            [
                ['element_id, method_id, result, inconclusive', 'safe'],
                [
                    'method_id', 'exist', 'allowEmpty' => true,
                    'attributeName' => 'id',
                    'className' => StereoAcuity_Method::class,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'inconclusive', 'in',
                    'range' => [self::INCONCLUSIVE, self::NOT_INCONCLUSIVE],
                    'message' => '{attribute} is invalid'
                ],
                [
                    'result', 'RequiredIfFieldValidator',
                    'field' => 'inconclusive',
                    'value' => self::NOT_INCONCLUSIVE,
                    'message' => '{attribute} is required'
                ],
                [
                    'result', 'emptyIfValue',
                    'field' => 'inconclusive',
                    'value' => self::INCONCLUSIVE,
                ],
                ['result', 'length', 'max' => 31]
            ],
            $this->rulesForCorrectionType(),
            $this->rulesForWithHeadPosture()
        );
    }

    public function emptyIfValue($attribute, $params)
    {
        if ($this->$attribute === '' || $this->$attribute === null) {
            return;
        }

        $fld = $params['field'];
        if ($this->$fld === $params['value']) {
            if (!isset($params['message'])) {
                $params['message'] = ucfirst(' {attribute} cannot be set when {field} is {field_value}.');
            }

            $params['{attribute}'] = $this->getAttributeLabel($attribute);
            $params['{field}'] = $this->getAttributeLabel($fld);
            $params['{field_value}'] = $this->$fld;

            $this->addError($attribute, strtr($params['message'], $params));
        }
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array_merge(
            [
                'element' => [self::BELONGS_TO, StereoAcuity::class, 'element_id'],
                'method' => [self::BELONGS_TO, StereoAcuity_Method::class, 'method_id'],
                'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
                'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            ],
            $this->relationsForCorrectionType()
        );
    }

    public function attributeLabels()
    {
        return [
            'method_id' => 'Method',
            'inconclusive' => 'Has Value',
            'result' => 'Result',
            'with_head_posture' => 'CHP',
            'correctiontype_id' => 'Correction'
        ];
    }

    public function __toString()
    {
        $result = [];

        $result[] = $this->method . " -";

        if ($this->inconclusive === self::NOT_INCONCLUSIVE) {
            $result[] = $this->result;
        } else {
            $result[] = $this->convertInconclusiveToDisplay($this->inconclusive);
        }

        if ($this->correctiontype_id || $this->withHeadPostureRecorded()) {
            $append = [];
            if ($this->correctiontype_id) {
                $append[] = $this->correctiontype;
            }
            if ($this->withHeadPostureRecorded()) {
                $append[] = sprintf(
                    "%s %s",
                    $this->getAttributeLabel('with_head_posture'),
                    $this->convertWithHeadPostureRecordToDisplay($this->with_head_posture)
                );
            }
            $result[] = sprintf("(%s)", implode(", ", $append));
        }

        return implode(" ", $result);
    }

    protected function convertInconclusiveToDisplay($value)
    {
        return [
                self::INCONCLUSIVE => self::DISPLAY_INCONCLUSIVE,
                self::NOT_INCONCLUSIVE => self::DISPLAY_NOT_INCONCLUSIVE
            ][$value] ?? null;
    }

    public function getDisplay_result()
    {
        return $this->inconclusive ? $this->convertInconclusiveToDisplay($this->inconclusive) : ($this->result ?? '-');
    }

    public function getInconclusive_options()
    {
        return [
            ['id' => self::INCONCLUSIVE, 'name' => self::DISPLAY_INCONCLUSIVE],
            ['id' => self::NOT_INCONCLUSIVE, 'name' => self::DISPLAY_NOT_INCONCLUSIVE]
        ];
    }
}
