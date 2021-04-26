<?php


namespace OEModule\OphGeneric\models;


class AssessmentEntry extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return \OEModule\OphGeneric\models\AssessmentEntry the static model class
     */

    public static $MORE = 1;
    public static $LESS = -1;
    public static $SAME = 0;
    public static $NONE = -9;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophgeneric_assessment_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['element_id, last_modified_user_id, created_user_id', 'length', 'max' => 10],
            ['crt,cst,avg_thickness,total_vol,irf,srf,cysts,retinal_thickening,ped,cmo,dmo,heamorrhage,exudates,avg_rnfl,cct,cd_ratio, eye_id, comments, abac_json, md, vfi', 'safe'],
            ['crt,cst,avg_thickness,total_vol,irf,srf,cysts,retinal_thickening,ped,cmo,dmo,heamorrhage,exudates,avg_rnfl,cct,cd_ratio', 'numerical', 'max' => 1000000000],
            ['id, element_id, last_modified_user_id, last_modified_date, created_user_id, created_date, eye_id', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element' => array(self::BELONGS_TO, 'OEModule\OphGeneric\models\Assessment', 'element_id'),
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
        );
    }

    public function attributeLabels()
    {
        return [
            // Medical Retina
            'crt' => 'Crt',
            'cst' => 'CST',
            'avg_thickness' => 'Thickness (mean)',
            'total_vol' => 'Total Vol.',
            'irf' => 'IRF',
            'srf' => 'SRF',
            'cysts' => 'Cysts',
            'retinal_thickening' => 'Retinal thickening',
            'ped' => 'PED',
            'cmo' => 'CMO',
            'dmo' => 'DMO',
            'heamorrhage' => 'Heamorrhage',
            'exudates' => 'Exudates',
            //Glaucoma
            'avg_rnfl' => 'RNFL (mean)',
            'cct' => 'CCT',
            'cd_ratio' => 'CD Ratio',
            'comments' => 'Comments'
        ];
    }

    public function getAssessmentEntryRadioButtonValues()
    {
        return array(
            'red' => AssessmentEntry::$MORE,
            'orange' => AssessmentEntry::$SAME,
            'green' => AssessmentEntry::$LESS,
            'remove' => AssessmentEntry::$NONE
        );
    }

    public function getTooltipText()
    {
        return array(
            AssessmentEntry::$MORE => 'More',
            AssessmentEntry::$SAME => 'Same',
            AssessmentEntry::$LESS => 'Less',
            AssessmentEntry::$NONE => 'None'
        );
    }

    public function getEntryIcon($entry_value)
    {
        switch ($entry_value) {
            case AssessmentEntry::$MORE:
                return '▲';
                break;
            case AssessmentEntry::$SAME:
                return '=';
                break;
            case AssessmentEntry::$LESS:
                return '▼';
                break;
        }
    }

    public function getEntryColours($entry_value)
    {
        switch ($entry_value) {
            case AssessmentEntry::$MORE:
                return 'red';
                break;
            case AssessmentEntry::$SAME:
                return 'orange';
                break;
            case AssessmentEntry::$LESS:
                return 'green';
                break;
        }
    }

    public function getMeasurementUnit($measurement, $is_view = false)
    {
        switch ($measurement) {
            case 'total_vol':
                return ($is_view ? ' ': '') . 'mm3';
                break;
            case 'crt':
            case 'cst':
            case 'avg_thickness':
                return $is_view ? ' μm' : 'μm &nbsp;&nbsp;';
                break;
        }
    }

    public function afterFind()
    {
        foreach (['irf', 'srf', 'cysts', 'retinal_thickening', 'ped', 'cmo', 'dmo', 'heamorrhage', 'exudates'] as $radio_button_field) {
            if (is_null($this->$radio_button_field)) {
                $this->$radio_button_field = static::$NONE;
            }
        }
        parent::afterFind();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('element_id', $this->element_id, true);
        $criteria->compare('eye_id', $this->eye_id, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
}
