<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\OphCiExaminationAllergy;

/**
 * This is the model class for table "medication".
 *
 * The followings are the available columns in table 'medication':
 * @property integer $id
 * @property string $source_type
 * @property string $source_subtype
 * @property string $preferred_term
 * @property string $short_term
 * @property string $preferred_code
 * @property string $vtm_term
 * @property string $vtm_code
 * @property string $vmp_term
 * @property string $vmp_code
 * @property string $amp_term
 * @property string $amp_code
 * @property int $source_old_id
 * @property string $deleted_date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property int $default_form_id
 * @property int $default_route_id
 * @property string $default_dose_unit_term
 * @property string $default_dose
 *
 * The followings are the available model relations:
 * @property EventMedicationUse[] $eventMedicationUses
 * @property User $lastModifiedUser
 * @property User $createdUser
 * @property MedicationSet[] $medicationSets
 * @property MedicationSetItem[] $medicationSetItems
 * @property MedicationSearchIndex[] $medicationSearchIndexes
 * @property MedicationAttributeOption[] $medicationAttributeOptions
 * @property MedicationAttributeAssignment[] $medicationAttributeAssignments
 * @property OphCiExaminationAllergy[] $allergies
 * @property MedicationRoute $defaultRoute
 * @property MedicationForm $defaultForm
 */
class Medication extends BaseActiveRecordVersioned
{
    const ATTR_PRESERVATIVE_FREE = "PRESERVATIVE_FREE";

    const SOURCE_TYPE_LEGACY = "LEGACY";
    const SOURCE_TYPE_LOCAL = "LOCAL";
    const SOURCE_TYPE_DMD = "DM+D";

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication';
    }

    public function scopes()
    {
        return [
            'prescribable' => [
                'condition' => 't.is_prescribable = 1'
            ],
        ];
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['source_type, preferred_term', 'required'],
            ['source_type, last_modified_user_id, created_user_id', 'length', 'max'=>10],
            ['source_subtype', 'length', 'max' => 45],
            ['preferred_term, short_term, preferred_code, vtm_term, vtm_code, vmp_term, vmp_code, amp_term, amp_code', 'length', 'max'=>255],
            ['id, deleted_date, last_modified_date, created_date, medicationSearchIndexes, medicationAttributeAssignments, medicationSetItems, default_route_id, default_form_id, default_dose, default_dose_unit_term, source_old_id, is_prescribable', 'safe'],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['id, source_type, source_subtype, preferred_term, preferred_code, vtm_term, vtm_code, vmp_term, vmp_code, amp_term, amp_code, 
					deleted_date, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'eventMedicationUses' => [self::HAS_MANY, EventMedicationUse::class, 'medication_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],

            'medicationSets' => array(self::MANY_MANY, MedicationSet::class, 'medication_set_item(medication_id, medication_set_id)'),

            'medicationSetItems' => [self::HAS_MANY, MedicationSetItem::class, 'medication_id'],
            // We need to set up a duplicate relation to be used with allergies, otherwise BaseActiveRecord::afterSave wont auto-save the medicationSetItems
            'medicationSetItems2' => [self::HAS_MANY, MedicationSetItem::class, 'medication_id'],
            'medicationSearchIndexes' => [self::HAS_MANY, MedicationSearchIndex::class, 'medication_id'],
            'medicationAttributeAssignments' => [self::HAS_MANY, MedicationAttributeAssignment::class, 'medication_id'],

            'medicationAttributeOptions' => array(self::MANY_MANY, MedicationAttributeOption::class, 'medication_attribute_assignment(medication_id,medication_attribute_option_id)'),

            'allergies' => [self::HAS_MANY, OphCiExaminationAllergy::class, ['medication_set_id' => "medication_set_id"], "through" => "medicationSetItems2"],
            "defaultForm" => [self::BELONGS_TO, MedicationForm::class, 'default_form_id'],
            "defaultRoute" => [self::BELONGS_TO, MedicationRoute::class, 'default_route_id'],

            'medicationSetAutoRuleMedication' => [self::HAS_MANY, MedicationSetAutoRuleMedication::class, 'medication_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source_type' => 'Source Type',
            'source_subtype' => 'Source Subtype',
            'preferred_term' => 'Preferred Term',
            'preferred_code' => 'Preferred Code',
            'vtm_term' => 'VTM Term',
            'vtm_code' => 'VTM Code',
            'vmp_term' => 'VMP Term',
            'vmp_code' => 'VMP Code',
            'amp_term' => 'AMP Term',
            'amp_code' => 'AMP Code',
            'deleted_date' => 'Deleted Date',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'will_copy' => 'Will copy',
            'default_form_id' => 'Default form',
            'default_route_id' => 'Default route',
            'default_dose' => 'Default dose',
            'default_dose_unit_term' => 'Default dose unit'
        ];
    }

    public function isVTM()
    {
        return $this->vtm_term != '' && $this->vmp_term == '' && $this->amp_term == '';
    }

    public function isVMP()
    {
        return $this->vmp_term != '' && $this->amp_term == '';
    }

    public function isAMP()
    {
        return $this->amp_term != '';
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
        $criteria->compare('source_type', $this->source_type, true);
        $criteria->compare('source_subtype', $this->source_subtype, true);
        $criteria->compare('preferred_term', $this->preferred_term, true);
        $criteria->compare('preferred_code', $this->preferred_code, true);
        $criteria->compare('vtm_term', $this->vtm_term, true);
        $criteria->compare('vtm_code', $this->vtm_code, true);
        $criteria->compare('vmp_term', $this->vmp_term, true);
        $criteria->compare('vmp_code', $this->vmp_code, true);
        $criteria->compare('amp_term', $this->amp_term, true);
        $criteria->compare('amp_code', $this->amp_code, true);
        $criteria->compare('deleted_date', $this->deleted_date, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, [
            'criteria'=>$criteria,
        ]);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Medication the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return bool
     */

    public function getToBeCopiedIntoMedicationManagement()
    {
        foreach ($this->medicationSets as $medSet) {
            if ($medSet->name == "medication_management") {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $site_id
     * @param $subspecialty_id
     * @return Medication[]
     */

    public function getSiteSubspecialtyMedications($site_id, $subspecialty_id)
    {
        $common_oph_id = Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_OPH'])->queryScalar();
        $criteria = new CDbCriteria();
        $criteria->condition = "id IN (SELECT medication_id FROM medication_set_item WHERE medication_set_id IN 
                                        (SELECT medication_set_id FROM medication_set_rule WHERE usage_code_id = :usage_code_id 
                                            AND site_id=:site_id AND subspecialty_id=:subspecialty_id))";
        $criteria->params = [":site_id" => $site_id, "subspecialty_id" => $subspecialty_id, ':usage_code_id' => $common_oph_id];
        $criteria->order = 'preferred_term';
        return $this->findAll($criteria);
    }

    /**
     * @param string $usage_code
     * @return array|null MedicationSet
     * @throws CException
     */
    public function getSetsByUsageCode($usage_code)
    {
        $usage_code_id = Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => $usage_code])->queryScalar();
        $criteria = new CDbCriteria();
        $criteria->condition = "id IN (SELECT medication_set_id FROM medication_set_item WHERE medication_id = :medication_id 
                                            AND medication_set_id IN (SELECT medication_set_id FROM medication_set_rule WHERE usage_code_id = :usage_code_id))";
        $criteria->params = [":medication_id" => $this->id, ':usage_code_id' => $usage_code_id];
        $criteria->order = 'name';
        return MedicationSet::model()->findAll($criteria);
    }

    /**
     * @return bool
     */

    public function isPreservativeFree()
    {
        return !empty($this->getAttrs(self::ATTR_PRESERVATIVE_FREE));
    }

    /**
     * @return string
     */

    public function getLabel($short = false)
    {
        $name =  $short ? ($this->short_term != "" ? $this->short_term : $this->preferred_term) : $this->preferred_term;

        if ($this->isAMP() && $this->vtm_term) {
            $name .= " (" . $this->vtm_term . ")";
        }

        return $name;
    }

    /**
     * @return string
     */

    public function __toString()
    {
        return $this->getLabel();
    }

    /**
     * @param bool $exclude_short This will remove the short_term from the list
     * @return string
     */

    public function alternativeTerms(bool $exclude_short = false): string
    {
        $terms = [];
        foreach ($this->medicationSearchIndexes as $idx) {
            if ($idx->alternative_term != $this->getLabel(true)) {
                if ($idx->alternative_term !== $this->preferred_term || $idx->alternative_term === $this->preferred_term && !$exclude_short) {
                    $terms[] = $idx->alternative_term;
                }
            }
        }

        return implode(", ", $terms);
    }

    private function listByUsageCode($usage_code, $subspecialty_id = null, $raw = false, $site_id = null, $prescribable_filter = false)
    {

        $criteria = new CDbCriteria();
        $criteria->with = ['medicationSetRules.usageCode'];
        $criteria->together = true;
        $criteria->compare('usageCode.usage_code', $usage_code);
        if (!is_null($subspecialty_id)) {
            $criteria->addCondition('medicationSetRules.subspecialty_id = :subspecialty_id OR medicationSetRules.subspecialty_id IS NULL');
            $criteria->params[':subspecialty_id'] = $subspecialty_id;
        } else {
            $criteria->addCondition('medicationSetRules.subspecialty_id IS NULL');
        }
        if (!is_null($site_id)) {
            $criteria->addCondition('medicationSetRules.site_id = :site_id OR medicationSetRules.site_id IS NULL');
            $criteria->params[':site_id'] = $site_id;
        } else {
            $criteria->addCondition('medicationSetRules.site_id IS NULL');
        }
        $sets = MedicationSet::model()->findAll($criteria);

        $return = [];
        $ids = [];

        /** @var MedicationSet[] $sets */

        foreach ($sets as $set) {
            foreach ($set->items as $item) {
                if (in_array($item->medication->id, $ids)) {
                    continue;
                }
                if ($prescribable_filter) {
                    $prescribable_sets = \MedicationSet::model()->findByUsageCode('PRESCRIBABLE_DRUGS', $site_id, $subspecialty_id);
                    $prescribable_set_ids = array_map(fn($set) => $set->id, $prescribable_sets);
                    $item_medication_set_ids = array_map(fn($set) => $set->id, $item->medication->medicationSets);
                    if (empty(array_intersect($item_medication_set_ids, $prescribable_set_ids))) {
                        continue;
                    }
                }
                $return[] = [
                    'label' => $item->medication->getLabel(true),
                    'value' => $item->medication->getLabel(true),
                    'name' => $item->medication->getLabel(true),
                    'id' => $item->medication->id,
                    'dose_unit_term' => $item->default_dose_unit_term != "" ? $item->default_dose_unit_term : $item->medication->default_dose_unit_term,
                    'dose' => $item->default_dose ? $item->default_dose : $item->medication->default_dose,
                    'default_form' => $item->default_form_id ? $item->default_form_id : $item->medication->default_form_id,
                    'frequency_id' => $item->default_frequency_id,
                    'route_id' => $item->default_route_id ? $item->default_route_id : $item->medication->default_route_id,
                    'source_subtype' => $item->medication ? $item->medication->source_subtype : "",
                    'will_copy' => $item->medication->getToBeCopiedIntoMedicationManagement(),
                    'set_ids' =>  array_map(function ($e) {
                        return $e->id;
                    }, $item->medication->getMedicationSetsForCurrentSubspecialty()),
                    'allergy_ids' => array_map(function ($e) {
                        return $e->id;
                    }, $item->medication->allergies),
                ];
                $ids[] = $item->medication->id;
            }
        }

        usort($return, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $raw ? $return : CHtml::listData($return, 'id', 'label');
    }

    public function listBySubspecialtyWithCommonMedications($subspecialty_id, $raw = false, $site_id = null, $prescribable_filter = false)
    {
        return $this->listByUsageCode("COMMON_OPH", $subspecialty_id, $raw, $site_id, $prescribable_filter);
    }

    public function listCommonSystemicMedications($raw = false, $prescribable_filter = false)
    {
        return $this->listByUsageCode("COMMON_SYSTEMIC", null, $raw, null, $prescribable_filter);
    }

    public function listOphthalmicMedicationIds()
    {
        $ophthalmic_medication_set = MedicationSet::model()->find('name = ?', ['Ophthalmic']);
        $ids = [];

        if ($ophthalmic_medication_set) {
            foreach ($ophthalmic_medication_set->items as $item) {
                if (!in_array($item->medication->id, $ids)) {
                    $ids[] = $item->medication->id;
                }
            }
        }

        return $ids;
    }

    public function getMedicationSetsForCurrentSubspecialty()
    {
        $firm_id = $this->getApp()->session->get('selected_firm_id');
        $site_id = $this->getApp()->session->get('selected_site_id');
        /** @var Firm $firm */
        $firm = $firm_id ? Firm::model()->findByPk($firm_id) : null;
        if ($firm) {
            $sets = [];
            foreach ($this->medicationSets as $set) {
                $relevant = false;
                if (empty($set->medicationSetRules)) {
                    $relevant = true;
                } else {
                    foreach ($set->medicationSetRules as $rule) {
                        if ($rule->subspecialty_id === null && $rule->site_id === null) {
                            $relevant = true;
                        }

                        if ($rule->subspecialty_id == $firm->subspecialty_id && $rule->site_id == $site_id) {
                            $relevant = true;
                        }
                    }
                }

                if ($relevant) {
                    $sets[] = $set;
                }
            }

            return $sets;
        } else {
            return $this->medicationSets;
        }
    }

    /**
     * Returns whether the medication belongs to a set
     * marked with $usage_code in the current context
     *
     * @param $usage_code
     * @return bool
     */

    public function isMemberOf($usage_code)
    {
        foreach ($this->getMedicationSetsForCurrentSubspecialty() as $medicationSet) {
            foreach ($medicationSet->medicationSetRules as $rule) {
                if ($rule->usageCode && $rule->usageCode->usage_code == $usage_code) {
                    return true;
                }
            }
        }

        return false;
    }

    public function addDefaultSearchIndex()
    {
        $searchIndex = new MedicationSearchIndex();
        $searchIndex->medication_id = $this->id;
        $searchIndex->alternative_term = $this->preferred_term;
        $searchIndex->save();
    }

    /**
     * Returns all attributes as [['attr_name'=> $attr_name, 'value'=> $value, 'description' => $description], [...]]
     *
     * @param string $attr_name     If set, the result will be filtered to this attribute
     * @return array
     */

    public function getAttrs($attr_name = null)
    {
        $ret = [];
        foreach ($this->medicationAttributeAssignments as $attr_assignment) {
            $aname = isset($attr_assignment->medicationAttributeOption->medicationAttribute) ? $attr_assignment->medicationAttributeOption->medicationAttribute->name : null;
            if (is_null($attr_name) || $aname == $attr_name) {
                $ret[] = [
                    'attr_name' => $aname,
                    'value' => $attr_assignment->medicationAttributeOption->value,
                    'description' => $attr_assignment->medicationAttributeOption->description,
                ];
            }
        }

        return $ret;
    }

    /**
     * Returns the next preferred_code for local, unmapped medication
     * @return string
     */
    public static function getNextUnmappedPreferredCode(): string
    {
        $unmapped_string = EventMedicationUse::USER_MEDICATION_SOURCE_SUBTYPE;
        $unmapped_string_length = strlen($unmapped_string);
        $start_at_position = $unmapped_string_length + 1;

        $sql = "SELECT MAX(CAST(SUBSTRING(`preferred_code`, :start_at_position, LENGTH(`preferred_code`)-:unmapped_string_length) AS UNSIGNED))
                FROM `medication`
                WHERE preferred_code LIKE :unmapped_string";

        $number = \Yii::app()->db->createCommand($sql)
            ->bindValue(':start_at_position', $start_at_position)
            ->bindValue(':unmapped_string_length', $unmapped_string_length)
            ->bindValue(':unmapped_string', "%{$unmapped_string}%")
            ->queryScalar();

        $number = !$number && !is_numeric($number) ? 0 : ++$number;

        return "{$unmapped_string}{$number}";
    }
}
