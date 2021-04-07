<?php

/**
 * This is the model class for table "patient_identifier_type".
 *
 * The followings are the available columns in table 'patient_identifier_type':
 * @property integer $id
 * @property integer $usage_type
 * @property string $short_title
 * @property string $long_title
 * @property integer $institution_id
 * @property integer $site_id
 * @property string $validate_regex
 * @property string $value_display_prefix
 * @property string $value_display_suffix
 * @property string $unique_row_str
 * @property string $pad
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property string $spacing_rule
 *
 * The followings are the available model relations:
 * @property Institution $institution
 * @property Site $site
 * @property User $lastModifiedUser
 * @property User $createdUser
 */
class PatientIdentifierType extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'patient_identifier_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id, usage_type, short_title, long_title, institution_id, site_id, validate_regex,
             value_display_prefix, value_display_suffix, unique_row_str,pad, spacing_rule,pas_api',
                'safe'),
            array('short_title, institution_id, validate_regex', 'required'),
            array('usage_type', 'validateUsageType'),
            array('value_display_suffix, value_display_prefix, pad, spacing_rule',
                'default', 'setOnEmpty' => true, 'value' => null),
            array('id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'patientIdentifier' => array(self::HAS_MANY, 'PatientIdentifier', 'patient_identifier_type_id'),
            'typeDisplayOrder' => array(self::HAS_MANY, 'PatientIdentifierTypeDisplayOrder', 'patient_identifier_type_id'),
            'patientIdentifierStatuses' => array(self::HAS_MANY, 'PatientIdentifierStatus', 'patient_identifier_type_id'),
        );
    }

    /**
     * This is necessary to NOT include unique_row_str as it is auto generated
     *
     * @param bool $runValidation
     * @param null $attributes
     * @param bool $allow_overriding
     * @return bool
     * @throws Exception
     */
    public function save($runValidation = true, $attributes = null, $allow_overriding = false)
    {
        if (!$attributes) {
            $attributes = array_values(array_filter(array_keys($this->getAttributes()), fn ($m) => $m != 'unique_row_str'));
        }

        return parent::save($runValidation, $attributes, $allow_overriding);
    }



    /**
     * Relation added manually as two foreign keys on single column causes Yii to
     * be unable to correctly resolve the relation
     */
    public function getInstitution()
    {
        return Institution::model()->findByPk($this->institution_id);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'usage_type' => 'Usage Type',
            'short_title' => 'Title',
            'long_title' => 'Long Title',
            'institution_id' => 'Institution',
            'site_id' => 'Site',
            'validate_regex' => 'Validate Regex',
            'value_display_prefix' => 'Prefix',
            'value_display_suffix' => 'Suffix',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'pad' => 'Padding',
            'spacing_rule' => 'Spacing Rule',
            'pas_api' => 'PAS'
        );
    }

    public function validateUsageType()
    {
        $existing_identifier = PatientIdentifierType::model()->find([
            'condition' => 'unique_row_str = :unique_row_str',
            'params' => [':unique_row_str' => $this->generateUniqueRowStringIdentifier()]
        ]);
        if ($existing_identifier && $existing_identifier->id !== $this->id) {
            $this->addError('usage_type', 'There is already a ' . strtolower($this->usage_type) . ' usage type for the chosen site');
        }
    }

    public function generateUniqueRowStringIdentifier()
    {
        return $this->usage_type . '-' . $this->institution_id . '-' . ($this->site_id ? $this->site_id : '0');
    }

    public function getDisplayIdentifierPrefix()
    {
        return $this->value_display_prefix ?? '';
    }

    public function getDisplayIdentifierSuffix()
    {
        return $this->value_display_suffix ?? '';
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PatientIdentifierType the static model class
     */
    public static function model($className = __CLASS__): CActiveRecord
    {
        return parent::model($className);
    }

    public function getSearchProtocols(): array
    {
        $protocols = [];
        foreach ($this->typeDisplayOrder as $order) {
            if ($order->search_protocol_prefix) {
                $protocols[] = $order->search_protocol_prefix;
            }
        }

        return $protocols;
    }

    /**
     * Validates a search term based on the type regexp and padding
     *
     * @param string $term
     * @return bool
     */
    public function validateTerm(string $term, bool $allow_blank = false) : bool
    {
        if ($this->validate_regex) {
            $padded = sprintf($this->pad ?:  '%s', $term);
            preg_match($this->validate_regex, $padded, $matches);

            $match = $matches[0] ?? null;

            if ($match) {
                return true;
            }
        }

        if ($term == "" && $allow_blank) {
            return true;
        }

        return false;
    }

    /**
     * Returns the long title with institution name
     *
     * @return string
     */
    public function getTitleWithInstitution() : string
    {
        return $this->long_title . ' (' . $this->institution->name . ')';
    }

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        $this->pas_api = $this->pas_api ? json_decode($this->pas_api, true) : [];
        parent::afterFind();
    }

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        if ($this->pas_api) {
            if (is_array($this->pas_api)) {
                $this->pas_api = json_encode($this->pas_api);
            }

            if (!$this->isValidJson($this->pas_api)) {
                $this->addError('pas_api', 'Invalid array to JSON string conversion.');
                return false;
            }
        }

        return parent::beforeSave();
    }

    /**
     * Checking if JSON string is valid
     *
     * @param $string
     * @return bool
     */
    public function isValidJson($string) : bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
