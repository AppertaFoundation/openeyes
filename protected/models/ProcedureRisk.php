<?php
/**
 * This is the model class for table "procedure_risk".
 *
 * The followings are the available columns in table 'procedure_risk':
 *
 * @property int $id
 * @property string $name
 */
class ProcedureRisk extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Procedurerisk the static model class
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
        return 'procedure_risk';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'risk' => array(self::BELONGS_TO, '\OEModule\OphCiExamination\Models\OphCiExaminationRisk', 'risk_id'),
        );
    }
}
