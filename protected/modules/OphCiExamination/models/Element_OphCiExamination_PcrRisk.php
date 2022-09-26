<?php

namespace OEModule\OphCiExamination\models;
use OE\factories\models\traits\HasFactory;

/**
 * Class Element_OphCiExamination_PcrRisk
 */
class Element_OphCiExamination_PcrRisk extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    use HasFactory;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_PcrRisk static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function __toString()
    {
        $result = [];
        if ($this->hasRight()) {
            $result[] = sprintf("R: %.2f%%", $this->right_pcr_risk);
        }
        if ($this->hasLeft()) {
            $result[] = "L: {$this->left_pcr_risk}%";
        }

        return implode(", ", $result);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_pcr_risk';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array(
                'event_id, eye_id,
                    left_glaucoma, left_pxf, left_diabetic, left_pupil_size, left_no_fundal_view, left_axial_length_group,
                    left_brunescent_white_cataract, left_alpha_receptor_blocker, left_doctor_grade_id, left_can_lie_flat, left_pcr_risk, left_excess_risk,
                    right_glaucoma, right_pxf, right_diabetic, right_pupil_size, right_no_fundal_view, right_axial_length_group,
                    right_brunescent_white_cataract, right_alpha_receptor_blocker, right_doctor_grade_id, right_can_lie_flat, right_pcr_risk, right_excess_risk',
                'safe',
            )
        );
    }

    /**
     * @return array
     */
    public function sidedFields()
    {
        return array(
            'glaucoma',
            'pxf',
            'diabetic',
            'pupil_size',
            'no_fundal_view',
            'axial_length_group',
            'brunescent_white_cataract',
            'alpha_receptor_blocker',
            'doctor_grade_id',
            'can_lie_flat',
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'right_doctor' => array(self::BELONGS_TO, 'DoctorGrade', 'right_doctor_grade_id'),
            'left_doctor' => array(self::BELONGS_TO, 'DoctorGrade', 'left_doctor_grade_id'),
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
            'left_glaucoma' => 'Glaucoma',
            'left_pxf' => 'PXF/Phacodonesis',
            'left_diabetic' => 'Diabetic',
            'left_pupil_size' => 'Pupil Size',
            'left_no_fundal_view' => 'Fundus obscured',
            'left_axial_length_group' => 'Axial Length (mm)',
            'left_brunescent_white_cataract' => 'Brunescent/White Cataract',
            'left_alpha_receptor_blocker' => 'Alpha receptor blocker',
            'left_doctor_grade_id' => 'Surgeon Grade',
            'left_can_lie_flat' => 'Can lie flat',
            'right_glaucoma' => 'Glaucoma',
            'right_pxf' => 'PXF/Phacodonesis',
            'right_diabetic' => 'Diabetic',
            'right_pupil_size' => 'Pupil Size',
            'right_no_fundal_view' => 'Fundus obscured',
            'right_axial_length_group' => 'Axial Length (mm)',
            'right_brunescent_white_cataract' => 'Brunescent/White Cataract',
            'right_alpha_receptor_blocker' => 'Alpha receptor blocker',
            'right_doctor_grade_id' => 'Surgeon Grade',
            'right_can_lie_flat' => 'Can lie flat',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Set the risk to be null if it's empty string to stop it being stored as 0.00
     *
     * @return bool
     */
    public function beforeSave()
    {
        if ($this->left_pcr_risk === '') {
            $this->left_pcr_risk = null;
        }

        if ($this->right_pcr_risk === '') {
            $this->right_pcr_risk = null;
        }

        $pcr = new \PcrRisk();
        foreach (array('left', 'right') as $side) {
            $data = array(
                'glaucoma' => $this->{$side . '_glaucoma'},
                'pxf_phako' => $this->{$side . '_pxf'},
                'diabetic' => $this->{$side . '_diabetic'},
                'pupil_size' => $this->{$side . '_pupil_size'},
                'no_fundal_view' => $this->{$side . '_no_fundal_view'},
                'axial_length' => $this->{$side . '_axial_length_group'},
                'brunescent_white_cataract' => $this->{$side . '_brunescent_white_cataract'},
                'arb' => $this->{$side . '_alpha_receptor_blocker'},
                'doctor_grade_id' => $this->{$side . '_doctor_grade_id'},
                'abletolieflat' => $this->{$side . '_can_lie_flat'},
            );
            $pcr->persist($side, $this->event->episode->patient, $data);
        }

        return parent::beforeSave();
    }

    /**
     * Set the data from other parts of the system if it's available.
     */
    public function afterConstruct()
    {
        if ($this->getIsNewRecord() && \Yii::app()->request->getQuery('patient_id', false)) {
            $pcr = new \PcrRisk();
            foreach (array('left', 'right') as $side) {
                $data = $pcr->getPCRData(\Yii::app()->request->getQuery('patient_id'), $side, $this);
                $this->{$side . '_glaucoma'} = $data['glaucoma'];
                $this->{$side . '_diabetic'} = $data['diabetic'];
                $this->{$side . '_can_lie_flat'} = $data['lie_flat'];
                $this->{$side . '_no_fundal_view'} = $data['noview'];
                $this->{$side . '_pxf'} = $data['anteriorsegment']['pxf_phako'];
                $this->{$side . '_pupil_size'} = $data['anteriorsegment']['pupil_size'];
                $this->{$side . '_brunescent_white_cataract'} = $data['anteriorsegment']['brunescent_white_cataract'];
                $this->{$side . '_doctor_grade_id'} = $data['doctor_grade_id'];
                $this->{$side . '_axial_length_group'} = $data['axial_length_group'];
                $this->{$side . '_alpha_receptor_blocker'} = $data['arb'];
            }
        }

        parent::afterConstruct();
    }

    public function getPrint_view()
    {
        return 'print_' . $this->getDefaultView();
    }
}
