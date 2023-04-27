<?php


class PathwayStepTypePresetAssignment extends BaseActiveRecordVersioned
{
    public static array $duration_period = [1 => 'days', 2 => 'weeks', 3 => 'months', 4 => 'years'];
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pathway_step_type_preset_assignment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['id, custom_pathway_step_id, standard_pathway_step_id, preset_short_name, preset_id, site_id, subspecialty_id, firm_id', 'safe'],
            ['preset_id, subspecialty_id, firm_id', 'requiredIfExaminationPreset'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'custom_pathway_step_type' => [self::BELONGS_TO, 'PathwayStepType', 'custom_pathway_step_type_id'],
            'standard_pathway_step_type' => [self::BELONGS_TO, 'PathwayStepType', 'standard_pathway_step_type_id'],
            'site' => [self::BELONGS_TO, 'Site', 'site_id'],
            'subspecialty' => [self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'],
            'firm' => [self::BELONGS_TO, 'Firm', 'firm_id'],
        ];
    }

    public function search()
    {
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('custom_pathway_step_type_id', $this->custom_pathway_step_type_id);
        $criteria->compare('standard_pathway_step_type_id', $this->standard_pathway_step_type_id);
        $criteria->compare('preset_short_name', $this->preset_short_name);
        $criteria->compare('preset_id', $this->preset_id);
        $criteria->compare('site_id', $this->site_id);
        $criteria->compare('subspecialty_id', $this->subspecialty_id);
        $criteria->compare('firm_id', $this->firm_id);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'preset_id' => 'Preset',
            'standard_pathway_step_type_id' => 'Standard Pathway step type',
            'site_id' => 'Site',
            'subspecialty_id' => 'Subspecialty',
            'firm_id' => 'Context',
        ];
    }

    public function beforeSave()
    {
        $attributes = ['preset_id', 'site_id', 'site_id', 'subspecialty_id', 'firm_id'];
        foreach ($attributes as $attribute) {
            if ($this->{$attribute} === '') {
                $this->{$attribute} = null;
            }
        }

        return parent::beforeSave();
    }

    public function requiredIfExaminationPreset($attribute)
    {
        if ($this->preset_short_name === 'Exam' && ($this->{$attribute} === null || $this->{$attribute} === '')) {
            $this->addError($attribute, $this->getAttributeLabel($attribute).' is required when Examination step type is selected');
        }
    }

    public function getStateDataTemplate()
    {
        if ($this->standard_pathway_step_type->state_data_template !== null) {
            $preset_state_template = json_decode($this->standard_pathway_step_type->state_data_template, true, 512,
                JSON_THROW_ON_ERROR);
            switch ($this->preset_short_name) {
                case 'Exam':
                    $preset_state_template['workflow_step_id'] = $this->preset_id;
                    $preset_state_template['subspecialty_id'] = $this->subspecialty_id;
                    $preset_state_template['firm_id'] = $this->firm_id;
                    break;
                case 'Letter':
                    $preset_state_template['macro_id'] = $this->preset_id;
                    break;
                case 'drug admin':
                    $preset_state_template['preset_id'] = $this->preset_id;
                    break;
            }
            return json_encode($preset_state_template, JSON_THROW_ON_ERROR);
        }

        if ($this->preset_short_name === 'Book Apt.') {
            $preset_state_template = [];
            $preset_state_template['site_id'] = $this->site_id;
            $preset_state_template['service_id'] = $this->subspecialty_id;
            $preset_state_template['firm_id'] = $this->firm_id;
            $preset_state_template['duration_value'] = $this->preset_id ? ($this->preset_id)%100 : null;
            $preset_state_template['duration_period'] = $this->preset_id ? self::$duration_period[$this->preset_id/100] : null;
            return json_encode($preset_state_template, JSON_THROW_ON_ERROR);
        } elseif ($this->preset_short_name === 'Fields') {
            $preset_state_template = [];
            [$preset_state_template['preset_id'], $preset_state_template['laterality']] = $this->getVisualFieldsPresetIdAndLaterality($this->preset_id);
            return json_encode($preset_state_template, JSON_THROW_ON_ERROR);
        }

        return null;
    }

    public static function getVisualFieldsPresetIdAndLaterality(int $number) : array {
        $last_digit = $number % 10;
        $not_last_digit = ($number - $last_digit) / 10;
        $preset_id = $number ? $not_last_digit : null;
        $laterality = $number ? $last_digit : null;

        return [$preset_id, $laterality];
    }
}
