<?php


class PathwayStepTypePresetAssignment extends BaseActiveRecordVersioned
{
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
            [
                'id, custom_pathway_step_id, standard_pathway_step_id, preset_short_name, preset_id, subspecialty_id, firm_id',
                'safe',
            ],
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
        $criteria->compare('subspecialty_id', $this->subspecialty_id);
        $criteria->compare('firm_id', $this->firm_id);
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

        return null;
    }
}
