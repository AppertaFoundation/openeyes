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
 * This is the model class for table "drug".
 *
 * The followings are the available columns in table 'drug':
 *
 * @property int $id
 * @property string $name
 * @property string $tallman
 * @property string $label
 * @property string $aliases
 * @property string $dose_unit
 * @property string $default_dose
 * @property Allergy[] $allergies
 * @property DrugType[] $type
 * @property DrugForm $form
 * @property DrugRoute $default_route
 * @property DrugFrequency $default_frequency
 * @property DrugDuration $default_duration
 * @property Tag[] $tags
 */
class Drug extends BaseActiveRecordVersioned
{
    public $preservative_free;

    protected $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Drug the static model class
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
        return 'drug';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.name');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, tallman', 'required'),
            array('tallman, dose_unit, default_dose, type_id, form_id, default_duration_id, default_frequency_id, '
                .'default_route_id, active, allergies, aliases, national_code, tags', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'allergies' => array(self::MANY_MANY, 'Allergy', 'drug_allergy_assignment(drug_id, allergy_id)'),
            'type' => array(self::MANY_MANY, 'DrugType', 'drug_drug_type(drug_id, drug_type_id)'),
            'form' => array(self::BELONGS_TO, 'DrugForm', 'form_id'),
            'default_duration' => array(self::BELONGS_TO, 'DrugDuration', 'default_duration_id'),
            'default_frequency' => array(self::BELONGS_TO, 'DrugFrequency', 'default_frequency_id'),
            'default_route' => array(self::BELONGS_TO, 'DrugRoute', 'default_route_id'),
            'subspecialtyAssignments' => array(self::HAS_MANY, 'SiteSubspecialtyDrug', 'drug_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'type_id' => 'Type',
            'default_duration_id' => 'Default Duration',
            'default_frequency_id' => 'Default Frequency',
            'default_route_id' => 'Default Route',
        );
    }

    public function behaviors()
    {
        return array(
            'TaggedActiveRecordBehavior' => 'TaggedActiveRecordBehavior',
            'LookupTable' => 'LookupTable',
        );
    }

    public function getLabel()
    {
        if ($this->isPreservativeFree()) {
            return $this->name.' (No Preservative)';
        } else {
            return $this->name;
        }
    }

    public function getTallmanLabel()
    {
        if ($this->isPreservativeFree()) {
            return $this->tallman.' (No Preservative)';
        } else {
            return $this->tallman;
        }
    }

    public function listBySubspecialty($subspecialty_id)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('subspecialty_id', $subspecialty_id);

        return CHtml::listData(self::model()->with('subspecialtyAssignments')->findAll($criteria), 'id', 'label');
    }

    /**
     * @param $subspecialty_id
     * @param $raw - to returns array with more values
     *
     * @return array
     */
    public function listBySubspecialtyWithCommonMedications($subspecialty_id, $raw = false)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('subspecialty_id', $subspecialty_id);
        $drugs = self::model()->with('subspecialtyAssignments')->findAll($criteria);
        $common_medication_drugs = CommonMedications::model()->with('medication_drug')->findAll();

        $return = array();

        //@TODO: should be consistent with protected/controllers/MedicationController.php actionFindDrug()
        foreach ($drugs as $drug) {
            $return[] = array(
                'label' => $drug->name,
                'value' => $drug->name,
                'name' => $drug->tallmanlabel,
                'id' => $drug->id,
                'tags' => array_map(function($t) { return $t->id;}, $drug->tags),
            );
        }

        foreach ($common_medication_drugs as $common_medication_drug) {
            $return[] = array(
                'label' => $common_medication_drug->medication_drug->name,
                'value' => $common_medication_drug->medication_drug->name,
                'name' => $common_medication_drug->medication_drug->name, // these should be handled somehow different...
                'id' => $common_medication_drug->medication_drug->id.'@@M',
                'tags' => array_map(function($t) { return $t->id;}, $common_medication_drug->medication_drug->tags),
            );
        }

        asort($return);

        return $raw ? $return : CHtml::listData($return, 'id', 'label');
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return array(
            'dose' => "{$this->default_dose} {$this->dose_unit}",
            'route_id' => $this->default_route_id,
            'frequency_id' => $this->default_frequency_id,
            'dose_unit' => $this->dose_unit,
            'default_dose' => $this->default_dose
        );
    }

    /**
     * @return bool
     *
     * Returns true if the tag 'Preservative free' is
     * added to this drug
     */

    public function isPreservativeFree()
    {
        return in_array(1, array_map(function($e){ return $e->id; }, $this->tags));
    }

    public function __toString()
    {
        return $this->getLabel();
    }
}
