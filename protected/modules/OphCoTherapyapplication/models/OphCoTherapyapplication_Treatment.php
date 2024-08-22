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

/**
 * This is the model class for table "ophcotherapya_treatment".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property string $name
 * @property bool $available
 * @property bool $contraindications_required
 * @property int $decisiontree_id
 * @property string $template_code
 * @property string $intervention_name
 * @property string $dose_and_frequency
 * @property string $administration_route
 * @property int $cost
 * @property int $cost_type_id
 * @property int $monitoring_frequency
 * @property int $monitoring_frequency_period_id
 * @property string $duration
 * @property string $toxicity
 *
 * The followings are the available model relations:
 * @property OphCoTherapyapplication_DecisionTree $decisiontree
 * @property OphCoTherapyapplication_Treatment_CostType $cost_type
 * @property Period $monitoring_frequency_period
 * @property User $user
 * @property User $usermodified
 */
class OphCoTherapyapplication_Treatment extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'ophcotherapya_treatment';
    }

    /**
     * scope to only get treatments where the drug is available.
     */
    public function availableOrPk($id)
    {
        $alias = $this->getTableAlias(true);

        $criteria = new CDbCriteria();
        $criteria->compare('drug.active', true);
        $criteria->compare("${alias}.active", true);
        $criteria->addCondition("{$alias}.decisiontree_id is not null");
        $criteria->compare("${alias}.".$this->metadata->tableSchema->primaryKey, $id, false, 'OR');

        $this->dbCriteria->mergeWith($criteria);

        return $this;
    }

    /**
     * set the ordering based on the drug for the treatment.
     *
     * (non-PHPdoc)
     *
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array('with' => array('drug'));
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('drug_id, available, contraindications_required, decisiontree_id, template_code, intervention_name,
					dose_and_frequency, administration_route, cost, cost_type_id, monitoring_frequency, monitoring_frequency_period_id,
					duration, toxicity', 'safe'),
            array('drug_id, contraindications_required, intervention_name, dose_and_frequency,
					administration_route, cost, cost_type_id, monitoring_frequency, monitoring_frequency_period_id,
					duration, toxicity', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, drug_id, available, contraindications_required', 'safe', 'on' => 'search'),
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
            'decisiontree' => array(self::BELONGS_TO, 'OphCoTherapyapplication_DecisionTree', 'decisiontree_id'),
            'drug' => array(self::BELONGS_TO, 'OphTrIntravitrealinjection_Treatment_Drug', 'drug_id'),
            'cost_type' => array(self::BELONGS_TO, 'OphCoTherapyapplication_Treatment_CostType', 'cost_type_id'),
            'monitoring_frequency_period' => array(self::BELONGS_TO, 'Period', 'monitoring_frequency_period_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'drug_id' => 'Drug',
            'decisiontree_id' => 'Default Decision Tree',
            'available' => 'Available',
            'contraindications_required' => 'Needs Contraindications Element',
            'cost_type_id' => 'Cost',
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
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    public function getName()
    {
        if ($this->drug) {
            return $this->drug->name;
        }

        return $this->intervention_name;
    }

    /**
     * return a list of treatment drugs for use in admin.
     *
     * @return OphTrIntravitrealinjection_Treatment_Drug[]
     */
    public function getTreatmentDrugs()
    {
        $drugs = OphTrIntravitrealinjection_Treatment_Drug::model()->findAll();
        if ($this->drug_id) {
            $drug_array = array();
            foreach ($drugs as $drug) {
                if ($drug->id == $this->drug_id) {
                    return $drugs;
                }
                $drug_array[] = $drug;
            }

            $drugs[] = $this->drug;
        }

        return $drugs;
    }

    public function getDisplayCost()
    {
        return $this->cost.' per '.$this->cost_type->name;
    }

    public function getDisplayMonitoringFrequency()
    {
        return 'Every '.$this->monitoring_frequency.' '.$this->monitoring_frequency_period->name;
    }
}
