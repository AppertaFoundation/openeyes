<?php

namespace OEModule\OphCiExamination\models;


/**
 * Class PrismReflex_Entry
 *
 * @package OEModule\OphCiExamination\models
 * @property $correctiontype_id
 * @property CorrectionType $correctiontype
 * @property $method_id
 * @property $finding_id
 * @property $prismdioptre_id
 * @property $prismbase_id
 */
class PrismReflex_Entry extends \BaseElement
{
    use traits\HasCorrectionType;
    use traits\HasRelationOptions;
    use traits\HasWithHeadPosture;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $correction_type_attributes = ['correctiontype_id'];


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_prismreflex_entry';
    }

    public function rules()
    {
        return array_merge(
            [
                ['element_id, prismbase_id, prismdioptre_id, finding_id', 'safe'],
                ['prismbase_id, prismdioptre_id, finding_id', 'required'],
                [
                    'prismbase_id', 'exist', 'allowEmpty' => true,
                    'attributeName' => 'id',
                    'className' => PrismReflex_PrismBase::class,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'prismdioptre_id', 'exist', 'allowEmpty' => true,
                    'attributeName' => 'id',
                    'className' => PrismReflex_PrismDioptre::class,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'finding_id', 'exist', 'allowEmpty' => true,
                    'attributeName' => 'id',
                    'className' => PrismReflex_Finding::class,
                    'message' => '{attribute} is invalid'
                ],
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
                'element' => [self::BELONGS_TO, PrismReflex::class, 'element_id'],
                'prismbase' => [self::BELONGS_TO, PrismReflex_PrismBase::class, 'prismbase_id'],
                'prismdioptre' => [self::BELONGS_TO, PrismReflex_PrismDioptre::class, 'prismdioptre_id'],
                'finding' => [self::BELONGS_TO, PrismReflex_Finding::class, 'finding_id'],
                'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
                'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            ],
            $this->relationsForCorrectionType(),
        );
    }

    public function attributeLabels()
    {
        return [
            'prismbase_id' => 'Prism',
            'prismdioptre_id' => 'Dioptres',
            'finding_id' => 'Finding',
            'with_head_posture' => 'CHP',
            'correctiontype_id' => 'Correction',
            'comments' => 'Comments'
        ];
    }

    public function __toString()
    {
        $result = [];

        if ($this->correctiontype_id) {
            $result[] = $this->correctiontype;
        }

        if ($this->withHeadPostureRecorded()) {
            $result[] = sprintf("CHP: %s", $this->display_with_head_posture);
        }

        $result[] = sprintf("%s - %s", $this->prismbase, $this->finding);

        return sprintf("%s: %s", $this->prismdioptre, implode(", " , $result));
    }
}