<?php

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\widgets\ConvergenceAccommodation as ConvergenceAccommodationWidget;

/**
 * Class ConvergenceAccommodation
 * @package OEModule\OphCiExamination\models
 * @property $correctiontype_id
 * @property CorrectionType $correctiontype
 * @property string $comments
 */
class ConvergenceAccommodation extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    use traits\HasCorrectionType;
    use traits\HasRelationOptions;
    use traits\HasWithHeadPosture;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $correction_type_attributes = ['correctiontype_id'];

    protected $widgetClass = ConvergenceAccommodationWidget::class;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_convergenceaccommodation';
    }

    public function rules()
    {
        return array_merge(
            [
                ['event_id, comments', 'safe'],
                ['comments', 'required'],
                ['comments', 'length', 'min' => 5]
            ],
            $this->rulesForCorrectionType(),
            $this->rulesForWithHeadPosture()
        );
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array_merge(
            [
                'event' => [self::BELONGS_TO, 'Event', 'event_id'],
                'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
                'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            ],
            $this->relationsForCorrectionType()
        );
    }

    public function attributeLabels()
    {
        return [
            'comments' => 'Description',
            'with_head_posture' => 'CHP',
            'correctiontype_id' => 'Correction'
        ];
    }

    public function getLetter_string()
    {
        $result = [];

        if ($this->correctiontype_id) {
            $result[] = $this->correctiontype;
        }
        if ($this->withHeadPostureRecorded()) {
            $result[] = sprintf("%s: %s", $this->getAttributeLabel('with_head_posture'), $this->display_with_head_posture);
        }
        if ($this->comments) {
            $result[] = preg_replace('/[\n\r]+/', ' ', $this->comments);
        }

        return 'Convergence And Accommodation: ' . implode(" ", $result);
    }
}