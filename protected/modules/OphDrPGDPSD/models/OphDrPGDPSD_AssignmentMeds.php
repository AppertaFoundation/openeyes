<?php
/**
 * This is the model class for table "ophdrpgdpsd_assignment_meds".
 *
 * The followings are the available columns in table 'ophdrpgdpsd_assignment_meds':
 * @property integer $id
 * @property integer $medication_id
 * @property integer $dose
 * @property string $dose_unit_term
 * @property integer $route_id
 * @property integer $pgdpsd_id
 *
 * The followings are the available model relations:
 * @property Medication $medication
 * @property MedicationRoute $route
 * @property OphDrPGDPSD_PGDPSD $pgdpsd
 */
class OphDrPGDPSD_AssignmentMeds extends BaseActiveRecordVersioned
{
    private $save_only_if_dirty = true;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrpgdpsd_assignment_meds';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        $common_rules =  array(
            array('assignment_id, medication_id, dose, dose_unit_term, route_id', 'required'),
            array('assignment_id, medication_id, dose', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            array('assignment_id, medication_id, dose, dose_unit_term, route_id, laterality, pair_key, administered, administered_time, administered_by, administered_id', 'safe'),
            array('id, medication_id, dose, dose_unit_term, route_id, laterality, pair_key, administered, administered_time, administered_by, administered_id', 'safe', 'on'=>'search'),
        );

        return $common_rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'medication' => array(self::BELONGS_TO, 'Medication', 'medication_id'),
            'assignment' => array(self::BELONGS_TO, 'OphDrPGDPSD_Assignment', 'assignment_id'),
            'medicationLaterality' => array(self::BELONGS_TO, 'MedicationLaterality', 'laterality_id'),
            "route" => array(self::BELONGS_TO, 'MedicationRoute', 'route_id'),
            "administered_user" => array(self::BELONGS_TO, 'User', 'administered_by'),
            "event_entry" => array(self::BELONGS_TO, 'Element_DrugAdministration_record', 'administered_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'assignment_id' => 'Assignment',
            'medication_id' => 'Medication',
            'dose' => 'Dose',
            'dose_unit_term' => 'Unit Term',
            'route_id' => 'Route',
            'administered' => 'Administered',
            'administered_time' => 'Administered Time',
            'administered_by' => 'Administered By',
            'administered_id' => 'Administered In'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('medication_id', $this->medication_id, true);
        $criteria->compare('dose', $this->dose, true);
        $criteria->compare('dose_unit_term', $this->dose_unit_term, true);
        $criteria->compare('route_id', $this->route_id, true);
        $criteria->compare('pgdpsd_id', $this->pgdpsd_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PsdDrugSetItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function formatAdministerTime()
    {
        $completed_time = '';
        if ($this->administered_time) {
            $completed_time = date_format(date_create($this->administered_time), 'H:i');
        }
        return $completed_time;
    }
    public function getAdministerDetails(){
        $administered_ts = $this->administered_time ? strtotime($this->administered_time) : null;
        $administered_nhs = \Helper::convertDate2NHS($this->administered_time, ' ');
        $administered_time = $this->administered_time ? date('H:i', strtotime($this->administered_time)) : null;
        $administered_time_ui = $administered_time ? ($this->isNewRecord ? "<input type='time' value='{$administered_time}'>" : $administered_time ): null;
        $administered_user = $this->administered_by ? $this->administered_user->getFullName() : '<small class="fade">Waiting to administer</small>';
        $css = $this->administered ? 'tick' : 'waiting selected';
        return array(
            'administered_ts' => $administered_ts,
            'administered_nhs' => $administered_nhs,
            'administered_time' => $administered_time,
            'administered_time_ui' => $administered_time_ui,
            'administered_user' => $administered_user,
            'css' => $css,
        );
    }
}
