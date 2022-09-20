<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "disorder".
 *
 * The followings are the available columns in table 'disorder':
 *
 * @property string $id
 * @property string $fully_specified_name
 * @property string $term
 * @property int $systemic
 * @property string $aliases
 * @property int $specialty_id
 *
 * The followings are the available model relations:
 * @property CommonOphthalmicDisorder[] $commonOphthalmicDisorders
 * @property CommonSystemicDisorder[] $commonSystemicDisorder
 * @property Specialty $specialty
 */
class Disorder extends BaseActiveRecordVersioned
{
    use HasFactory;

    const SITE_LEFT = 0;
    const SITE_RIGHT = 1;
    const SITE_BILATERAL = 2;

    // the following constants are defined as convenience values for determining disorders of certain types.
    // prefixed SNOMED to reserve namespace, and be self-describing.
    const SNOMED_DIABETES = 73211009;
    const SNOMED_DIABETES_TYPE_I = 46635009;
    const SNOMED_DIABETES_TYPE_II = 44054006;
    // the sets postfix indicate this is an array of SNOMED concepts that can be used to determine if a disorder
    // is part of the parent SNOMED concept.
    // For example, diabetes is indicated by both the disorder parent and associated disorders
    public static $SNOMED_DIABETES_SET = array(73211009, 74627003);
    public static $SNOMED_DIABETES_TYPE_I_SET = array(46635009, 420868002);
    public static $SNOMED_DIABETES_TYPE_II_SET = array(44054006, 422014003);

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     *
     * @return Disorder the static model class
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
        return 'disorder';
    }

    /**
     * @return string the associated database tree table name
     */
    public function treeTable()
    {
        return 'disorder_tree';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['id, fully_specified_name, term', 'required'],
            ['id, ecds_code', 'length', 'max' => 20],
            ['icd10_code', 'length', 'max' => 10],
            ['id', 'checkDisorderExists'],
            ['id', 'numerical', 'integerOnly' => true],
            ['fully_specified_name, term, aliases, specialty_id, ecds_term, icd10_term', 'length', 'max' => 255],
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            ['id, fully_specified_name, term, systemic, aliases, ecds_code, ecds_term, icd10_code, icd10_term', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'commonOphthalmicDisorders' => array(self::HAS_MANY, 'CommonOphthalmicDisorder', 'disorder_id'),
            'commonSystemicDisorders' => array(self::HAS_MANY, 'CommonSystemicDisorder', 'disorder_id'),
            //'diagnoses' => array(self::HAS_MANY, 'Diagnosis', 'disorder_id'),
            'specialty' => array(self::BELONGS_TO, 'Specialty', 'specialty_id'),
        );
    }

    public function behaviors()
    {
        return array(
            'treeBehavior' => array(
                'class' => 'TreeBehavior',
                'idAttribute' => 'disorder_id',
            ),
            'LookupTable' => 'LookupTable',
        );
    }

    public function canAutocomplete()
    {
        return true;
    }

    public function getAutocompleteField()
    {
        return 'term';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'fully_specified_name' => 'Fully Specified Name',
            'term' => 'Term',
            'systemic' => 'Systemic',
            'aliases' => 'Aliases'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('lower(fully_specified_name)', strtolower($this->fully_specified_name), true);
        $criteria->compare('lower(term)', strtolower($this->term), true);

        return new CActiveDataProvider(get_class($this), array('criteria' => $criteria));
    }

    /**
     * Fetch a list of disorders whose term matches a provided value (with wildcards).
     *
     * @param string $term
     *
     * @return array
     */
    public static function getDisorderOptions($term)
    {
        return Yii::app()->db->createCommand()
            ->select('term')
            ->from('disorder')
            ->where('term LIKE :term and active = 1', array(':term' => "%{$term}%"))
            ->queryColumn();
    }

    /**
     * returns boolean to indicate if the disorder is systemic (true).
     *
     * @return bool
     */
    public function getSystemic()
    {
        if ($this->specialty_id) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->term;
    }

    public function checkDisorderExists($attribute) {
        $query = "SELECT id FROM disorder where id='$this->id'";
        $command = Yii::app()->db->createCommand($query);
        $command->prepare();
        $result = $command->queryColumn();
        if (sizeof($result) > 0 && $this->isNewRecord === true) {
            $this->addError($attribute, 'ID '.$this->id.' already exists. Please choose a unique ID.');
            return true;
        }
            return false;
    }

    public static function getPatientDisorders($patient_id) {
        $criteria = new CDbCriteria();
        $criteria->join = "LEFT JOIN episode ep ON ep.disorder_id = t.id AND ep.patient_id = :patient_id ";
        $criteria->join .= "LEFT JOIN secondary_diagnosis sd ON sd.disorder_id = t.id AND sd.patient_id = :patient_id";
        $criteria->condition = "ep.id IS NOT NULL OR sd.id IS NOT NULL";
        $criteria->params[':patient_id'] = $patient_id;

        return Disorder::model()->findAll($criteria);
    }
}
