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

/**
 * This is the model class for table "medication_set".
 *
 * The followings are the available columns in table 'medication_set':
 * @property integer $id
 * @property string $name
 * @property integer $antecedent_medication_set_id
 * @property string $deleted_date
 * @property integer $display_order
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property int $hidden
 * @property int $automatic
 *
 * The followings are the available model relations:
 * @property MedicationSetItem[] $medicationSetItems
 * @property MedicationSet $antecedentMedicationSet
 * @property MedicationSet[] $medicationSets
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property MedicationSetRule[] $medicationSetRules
 * @property Medication[] $medications
 * @property MedicationSetItem[] $items
 * @method  Medication[] medications(array $opts)
 * @property MedicationAttributeOption[] $autoRuleAttributes
 * @property MedicationSet[] $autoRuleSets
 * @property Medication[] $autoRuleMedications
 * @property MedicationSetAutoRuleAttribute[] $medicationAutoRuleAttributes
 * @property MedicationSetAutoRuleSetMembership[] $medicationSetAutoRuleSetMemberships
 * @property MedicationSetAutoRuleMedication[] $medicationSetAutoRuleMedications
 */
class MedicationSet extends BaseActiveRecordVersioned
{
    /*
     * These variables stand for temporary storage only
     */
    public $tmp_attrs = [];
    public $tmp_sets = [];
    public $tmp_meds = [];
    public $tmp_rules = [];

    private static $_processed = [];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_set';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),

            // 'on'=>'insert, update' because of the protected/commands/MedicationSetImportCommand.php
            // it needs to handle duplicate names during the import
            array('name', 'isUnique', 'on' => 'insert, update'),
            array('antecedent_medication_set_id, display_order, hidden, automatic', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('name, deleted_date, last_modified_date, created_date, automatic, hidden, id', 'safe'),

            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, hidden, automatic, antecedent_medication_set_id, deleted_date, display_order, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on' => 'search'),
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
            'medicationSetItems' => array(self::HAS_MANY, MedicationSetItem::class, 'medication_set_id'),
            'antecedentMedicationSet' => array(self::BELONGS_TO, MedicationSet::class, 'antecedent_medication_set_id'),
            'medicationSets' => array(self::HAS_MANY, MedicationSet::class, 'antecedent_medication_set_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'medicationSetRules' => array(self::HAS_MANY, MedicationSetRule::class, 'medication_set_id'),
            'medications' => array(self::MANY_MANY, Medication::class, 'medication_set_item(medication_set_id,medication_id)'),
            'medicationAutoRuleAttributes' => array(self::HAS_MANY, MedicationSetAutoRuleAttribute::class, 'medication_set_id'),
            'autoRuleAttributes' => array(self::MANY_MANY, MedicationAttributeOption::class, 'medication_set_auto_rule_attribute(medication_set_id, medication_attribute_option_id)'),
            'medicationSetAutoRuleSetMemberships' => array(self::HAS_MANY, MedicationSetAutoRuleSetMembership::class, 'target_medication_set_id'),
            'autoRuleSets' => array(self::MANY_MANY, MedicationSet::class, 'medication_set_auto_rule_set_membership(target_medication_set_id,source_medication_set_id)'),
            'medicationSetAutoRuleMedications' => array(self::HAS_MANY, MedicationSetAutoRuleMedication::class, 'medication_set_id'),
            'autoRuleMedications' => array(self::MANY_MANY, Medication::class, 'medication_set_auto_rule_medication(medication_set_id, medication_id)'),
        );
    }

    /**
     * @return MedicationSetItem[]
     *
     * Compatibility function to map $this->items to $this->medicationSetItems
     */

    public function getItems()
    {
        return $this->medicationSetItems;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'antecedent_medication_set_id' => 'Antecedent Medication Set',
            'deleted_date' => 'Deleted Date',
            'display_order' => 'Display Order',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'itemsCount' => 'Items count',
            'rulesString' => 'Rules',
            'hidden' => 'Hidden/system',
            'automatic' => 'Automatic'
        );
    }

    /**
     * Returns true if the set has a usage_code provided as parameter
     *
     * @param $usage_code
     * @return bool
     */
    public function hasUsageCode($usage_code)
    {

        $criteria = new \CDbCriteria();
        $criteria->join = "JOIN medication_set_rule r ON t.id = r.medication_set_id ";
        $criteria->join .= "JOIN medication_usage_code c ON r.usage_code_id = c.id";
        $criteria->addCondition("t.id = :id");
        $criteria->addCondition("c.usage_code = :usage_code");
        $criteria->params = [
            ':id' => $this->id,
            ':usage_code' => $usage_code
        ];
        return (bool)$this->count($criteria);
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('antecedent_medication_set_id', $this->antecedent_medication_set_id);
        $criteria->compare('deleted_date', $this->deleted_date, true);
        $criteria->compare('display_order', $this->display_order);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MedicationSet the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Returns RefSets that belong to site and subspecialty
     *
     * @param null $site_id defaults to currently selected
     * @param null $subspecialty_id defaults to currently selected
     * @return CActiveRecord[]
     */

    public static function getAvailableSets($site_id = null, $subspecialty_id = null, $usage_code = null)
    {
        if (is_null($site_id)) {
            $site_id = Yii::app()->session['selected_site_id'];
        }

        if (is_null($subspecialty_id)) {
            $firm_id = \Yii::app()->session['selected_firm_id'];
            $firm = \Firm::model()->findByPk($firm_id);
            $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition("(t.deleted_date IS NULL)");
        $criteria->with = array(
            'medicationSetRule' => array(
                'condition' =>
                    ' (subspecialty_id = :subspecialty_id OR subspecialty_id IS NULL) AND' .
                    ' (site_id = :site_id OR site_id IS NULL)' .
                    (!is_null($usage_code) ? 'AND FIND_IN_SET(:usage_code, usage_code) > 0' : '')
            ),
        );
        $criteria->params['subspecialty_id'] = $subspecialty_id;
        $criteria->params['site_id'] = $site_id;

        if (!is_null($usage_code)) {
            $criteria->params['usage_code'] = $usage_code;
        }

        $criteria->order = 't.display_order ASC';

        return self::model()->findAll($criteria);
    }

    public function itemsCount()
    {
        return \MedicationSetItem::model()->countByAttributes(['medication_set_id' => $this->id]);
    }

    public function rulesString()
    {
        $ret_val = [];
        foreach ($this->medicationSetRules as $rule) {
            $ret_val[] = "Site: " . ($rule->site ? $rule->site->name : '-') .
                ", SS: " . ($rule->subspecialty ? $rule->subspecialty->name : "-") .
                ", Usage code: " . ($rule->usageCode ? $rule->usageCode->name : '-');
        }

        return implode(" // ", $ret_val);
    }

    public function scopes()
    {
        return array(
            'byName' => array('order' => 'name ASC'),
        );
    }

    public function beforeValidate()
    {
        if ($this->automatic) {
            foreach ($this->tmp_attrs as $attr) {
                if ($attr['medication_attribute_option_id'] == '') {
                    $this->addError('attribute', 'Attribute value must be set');
                }
            }
        }

        return parent::beforeValidate();
    }

    public function afterSave()
    {
        if ($this->automatic) {
            $this->_saveAutoAttrs();
            $this->_saveAutoSets();
            $this->_saveSetRules();
        }

        return parent::afterSave();
    }

    /**
     * Saves temporary auto attributes
     * applies to automatic sets only
     */

    private function _saveAutoAttrs()
    {
        $existing_ids = array_map(function ($e) {
            return $e->id;

        }, $this->medicationAutoRuleAttributes);
        $updated_ids = array();
        foreach ($this->tmp_attrs as $attr) {
            if ($attr['id'] == -1) {
                $attrib = new MedicationSetAutoRuleAttribute();
            } else {
                $attrib = MedicationSetAutoRuleAttribute::model()->findByPk($attr['id']);
            }

            $attrib->medication_attribute_option_id = $attr['medication_attribute_option_id'];
            $attrib->medication_set_id = $this->id;
            $attrib->save();
            $updated_ids[] = $attrib->id;
        }
        $ids_to_delete = array_diff($existing_ids, $updated_ids);
        if (!empty($ids_to_delete)) {
            $criteria = new \CDbCriteria();
            $criteria->addInCondition('id', $ids_to_delete);
            MedicationSetAutoRuleAttribute::model()->deleteAll($criteria);
        }
    }

    /**
     * Saves temporary set memberships
     * Applies to automatic sets only
     */

    private function _saveAutoSets()
    {
        $existing_ids = array_map(function ($e) {
            return $e->id;

        }, $this->medicationSetAutoRuleSetMemberships);
        $updated_ids = [];
        $models = [];
        foreach ($this->tmp_sets as $set) {
            if ($set['id'] == -1) {
                $set_m = new MedicationSetAutoRuleSetMembership();
            } else {
                $set_m = MedicationSetAutoRuleSetMembership::model()->findByPk($set['id']);
            }

            $set_m->source_medication_set_id = $set['medication_set_id'];
            $set_m->target_medication_set_id = $this->id;
            $set_m->save();
            $updated_ids[] = $set_m->id;

            $models[] = $set_m;
        }

        // in case if there is an error on the page we can display what user set previously
        $this->medicationSetAutoRuleSetMemberships = $models;

        $ids_to_delete = array_diff($existing_ids, $updated_ids);
        if (!empty($ids_to_delete)) {
            $criteria = new \CDbCriteria();
            $criteria->addInCondition('id', $ids_to_delete);
            MedicationSetAutoRuleSetMembership::model()->deleteAll($criteria);
        }
    }

    /**
     * Saves temporary individual medications for auto sets
     * Applies to automatic sets only
     */

    public function saveAutoMeds()
    {
        $existing_ids = array_map(function ($e) {
            return $e->id;

        }, $this->medicationSetAutoRuleMedications);
        $updated_ids = array();

        foreach ($this->tmp_meds as $med) {
            if (!isset($med['id'])) {
                $med_m = new MedicationSetAutoRuleMedication();
            } else {
                $med_m = MedicationSetAutoRuleMedication::model()->findByPk($med['id']);
            }

            if (!$med_m) {
                throw new Exception("MedicationSetAutoRuleMedication {$med['id']} did not found");
            }

            $med_m->medication_id = $med['medication_id'];
            $med_m->include_children = $med['include_children'];
            $med_m->include_parent = $med['include_parent'];
            $med_m->medication_set_id = $this->id;
            $med_m->save();
            $updated_ids[] = $med_m->id;
        }
        $ids_to_delete = array_diff($existing_ids, $updated_ids);
        if (!empty($ids_to_delete)) {
            $criteria = new \CDbCriteria();
            $criteria->addInCondition('id', $ids_to_delete);
            MedicationSetAutoRuleMedication::model()->deleteAll($criteria);
        }
    }

    private function _saveSetRules()
    {
        $existing_ids = array_map(function ($rule) {
            return $rule->id;

        }, $this->medicationSetRules);
        $updated_ids = [];

        foreach ($this->tmp_rules as $rule) {
            if (isset($rule['id']) && !$rule['id']) {
                $rule_model = new MedicationSetRule();
            } else {
                $rule_model = MedicationSetRule::model()->findByPk($rule['id']);
            }
            $rule_model->attributes = $rule;
            $rule_model->medication_set_id = $this->id;
            $rule_model->save();
            $updated_ids[] = $rule_model->id;
        }

        $ids_to_delete = array_diff($existing_ids, $updated_ids);
        if (!empty($ids_to_delete)) {
            $criteria = new \CDbCriteria();
            $criteria->addInCondition('id', $ids_to_delete);
            MedicationSetRule::model()->deleteAll($criteria);
        }
    }

    /**
     * Populate the automatic set with
     * all the relevant medications
     *
     * @return int the number of items found
     * @return false on error
     */

    public function populateAuto()
    {
        if (!$this->automatic || in_array($this->id, self::$_processed)) {
            $msg = "Skipping " . $this->name . " because it's already processed.\n";
            Yii::log($msg);
            return false;
        }

        self::$_processed[] = $this->id;
        $msg = "Started processing " . $this->name . "\n";
        Yii::log($msg);

        $cmd = $this->getDbConnection()->createCommand();
        $cmd->select('id', 'DISTINCT')->from('medication');
        $attribute_option_ids = array_map(function ($e) {
            return $e->id;

        }, $this->autoRuleAttributes);

        $auto_set_ids = array_map(function ($e) {
            return $e->id;
        },
            array_filter($this->autoRuleSets, function ($e) {
                return $e->automatic == 1;
            })
        );

        $nonauto_set_ids = array_map(function ($e) {
            return $e->id;
        },
            array_filter($this->autoRuleSets, function ($e) {
                return $e->automatic == 0;
            })
        );

        $no_condition = true;

        if (!empty($attribute_option_ids)) {
            $cmd->orWhere("id IN (SELECT medication_id FROM " . MedicationAttributeAssignment::model()->tableName() . "
												WHERE medication_attribute_option_id IN (" . implode(",", $attribute_option_ids) . ")
												)");
            $no_condition = false;
        }

        if (!empty($nonauto_set_ids)) {
            $cmd->orWhere("id IN (SELECT medication_id FROM " . MedicationSetItem::model()->tableName() . "
												WHERE medication_set_id IN (" . implode(",", $nonauto_set_ids) . ")
												)");
            $no_condition = false;
        }

        foreach ($this->medicationSetAutoRuleMedications as $medicationSetAutoRuleMedication) {
            if ($medicationSetAutoRuleMedication->include_parent) {
                $medication = $medicationSetAutoRuleMedication->medication;
                if ($medication->isAMP()) {
                    $cmd->orWhere("preferred_code = '{$medication->vmp_code}'");
                    if ($vmp = Medication::model()->findAll("preferred_code = '{$medication->vmp_code}'")) {
                        $vmp = $vmp[0];
                    };
                    $cmd->orWhere("preferred_code = '{$vmp->vtm_code}'");
                } elseif ($medication->isVMP()) {
                    $cmd->orWhere("preferred_code = '{$medication->vtm_code}'");
                }
            }
            if ($medicationSetAutoRuleMedication->include_children) {
                $medication = $medicationSetAutoRuleMedication->medication;
                if ($medication->isVTM()) {
                    $cmd->orWhere("vtm_code = '{$medication->preferred_code}'");
                } elseif ($medication->isVMP()) {
                    $cmd->orWhere("vmp_code = '{$medication->preferred_code}'");
                }
            }
            $cmd->orWhere("id = " . $medicationSetAutoRuleMedication->medication_id);
            $no_condition = false;
        }

        $medication_ids = $cmd->queryColumn();

        // empty the set
        $cnt = MedicationSetItem::model()->countByAttributes(['medication_set_id' => $this->id]);

        $batch = 500000;
        $iteration = -1;
        do {
            $iteration++;
            $item_ids_array = $this->dbConnection->createCommand()
                ->select('id')
                ->from('medication_set_item')
                ->where('medication_set_id = :id', [':id' => $this->id])
                ->offset($iteration * $batch)
                ->limit($batch)
                ->queryAll();

            $item_ids = [];
            foreach ($item_ids_array as $i) {
                $item_ids[] = $i['id'];
            }

            if ($item_ids) {
                //deleting tapers
                $delete_taper_query = "DELETE FROM medication_set_item_taper WHERE medication_set_item_id IN (" . implode(", ", $item_ids) . ")";
                $this->dbConnection->getCommandBuilder()->createSqlCommand($delete_taper_query)->execute();

                //deleting item
                $delete_set_item_query = "DELETE FROM medication_set_item WHERE id IN (" . implode(", ", $item_ids) . ")";
                $this->dbConnection->getCommandBuilder()->createSqlCommand($delete_set_item_query)->execute();
            }
        } while (($iteration * $batch) <= $cnt);

        if (!$no_condition && !empty($medication_ids)) {
            // repopulate

            $medication_queries = [];
            foreach ($medication_ids as $mk => $id) {
                $medication_queries[] = [
                    'medication_set_id' => $this->id,
                    'medication_id' => $id
                ];
            }
            if ($medication_queries) {
                $this->dbConnection->getCommandBuilder()->createMultipleInsertCommand('medication_set_item', $medication_queries)->execute();
            }

            foreach ($medication_ids as $mk => $id) {
                $auto_set = MedicationSetAutoRuleMedication::model()->findByAttributes(['medication_set_id' => $this->id, 'medication_id' => $id]);

                if ($auto_set) {
                    $q = [];
                    foreach ($auto_set->attributes as $attribute) {
                        if (strpos($attribute, 'default') === 0) {
                            //$new_set_item->$attribute = $auto_set->$attribute;
                            $q[$attribute] = $auto_set->$attribute;
                        }
                    }

                    if ($q) {
                        $criteria = new \CDbCriteria();
                        $criteria->addCondition('medication_set_id', $this->id);
                        $criteria->addCondition('medication_id', $id);
                        $this->dbConnection->getCommandBuilder()->createUpdateCommand('medication_set_item', $q, $criteria);
                    }
                }

                if ($auto_set && $auto_set->tapers) {
                    // save tapers
                    $medication_tapers_values = [];
                    foreach ($medicationSetAutoRuleMedication->tapers as $taper) {
                        $medication_tapers_values[] =
                        "((SELECT id FROM medication_set_item WHERE medication_set_id = {$this->id} AND medication_id = {$id} LIMIT 1), 
                         {$taper->dose}, {$taper->frequency_id}, {$taper->duration_id})
                        ";
                    }

                    if ($medication_tapers_values) {
                        $insert_taper_query = "
                        INSERT INTO medication_set_item_taper(medication_set_item_id, dose, frequency_id, duration_id)
                        VALUES " . (implode(", ", $medication_tapers_values)) . ";";
                        $this->dbConnection->getCommandBuilder()->createSqlCommand($insert_taper_query)->bindValues([
                            ':medication_set_id' => $this->id,
                            ':medication_id' => $id
                        ])->execute();
                    }
                }
            }
        }

        $msg = "Processed non-auto rules in " . $this->name . "\n";
        Yii::log($msg);

        // process auto sub sets recursively
        if (!empty($auto_set_ids)) {
            foreach ($auto_set_ids as $auto_id) {
                if (!in_array($auto_id, self::$_processed)) {
                    // Sub set is not already processed
                    $included_set = self::model()->findByPk($auto_id);
                    /** @var self $included_set */
                    $included_set->populateAuto();
                }

                // Include items from sub set
                $msg = "Adding sub set items into " . $this->name . " ";
                Yii::log($msg);

                $criteria = new \CDbCriteria();
                $criteria->addCondition('medication_set_id = :medication_auto_set_id');
                $criteria->addCondition('medication_id NOT IN (SELECT medication_id FROM medication_set_item WHERE medication_set_id = :medication_set_id)');
                $criteria->params[':medication_auto_set_id'] = $auto_id;
                $criteria->params[':medication_set_id'] = $this->id;

                $item_count = MedicationSetItem::model()->count($criteria);

                if ($item_count) {
                    $items = MedicationSetItem::model()->findAll($criteria);
                    $insert_queries = [];
                    $index = 0;

                    foreach ($items as $ik => $item) {
                        $data = [];
                        // $new_item = new MedicationSetItem();
                        foreach ($item->attributes as $attribute) {
                            if (strpos($attribute, 'default') === 0) {
                                $data[$attribute] = $item->$attribute;
                            }
                            $data['medication_set_id'] = $this->id;
                            $data['medication_id'] = $item->medication_id;

                            $insert_queries[$this->id . $item->medication_id] = $data;
                            $index++;
                            if (($index >= 500 || $ik === $item_count - 1) && $insert_queries) {
                                $this->dbConnection->getCommandBuilder()->createMultipleInsertCommand('medication_set_item', $insert_queries)->execute();
                                $index = 0;
                                $insert_queries = [];
                            }
                        }
                    }
                }
            }
        }

        $msg = "Done processing " . $this->name . "\n";
        Yii::log($msg);

        return count($medication_ids);
    }

    /**
     * Runs processing routine for all auto sets
     *
     * @return array    The list of ids that were processed
     */

    public static function populateAutoSets()
    {
        self::$_processed = [];
        foreach (self::model()->findAll("automatic = 1") as $set) {
            /** @var self $set */
            $set->populateAuto();
        }

        return self::$_processed;
    }

    public function beforeDelete()
    {
        if ($this->automatic) {
            foreach ($this->medicationSetAutoRuleSetMemberships as $set_m) {
                $set_m->delete();
            }
            foreach ($this->medicationSetAutoRuleMedications as $med) {
                $med->delete();
            }
            foreach ($this->medicationAutoRuleAttributes as $attribute) {
                $attribute->delete();
            }
        } else {
            foreach ($this->medicationSetItems as $item) {
                $item->delete();
            }
        }

        return true;
    }

    public function addMedication($medication_id)
    {
        if (!$this->isNewRecord) {
            $assignment = new \MedicationSetItem();
            $assignment->medication_id = $medication_id;
            $assignment->medication_set_id = $this->id;

            if ($assignment->save()) {
                return $assignment->id;
            }
        }
        return false;
    }

    public function removeUsageCode($usage_code)
    {
        if (!$this->id) {
            return false;
        }

        return \MedicationSetRule::model()->deleteAllByAttributes(['medication_set_id' => $this->id, $usage_code]);
    }

    public function addUsageCode($usage_code, $subspecialty)
    {
        $medication_set_rule = new MedicationSetRule();
        $medication_set_rule->medication_set_id = $this->id;
        $medication_set_rule->subspecialty_id = \Subspecialty::model()->find('name=?', array($subspecialty))->id;
        $medication_set_rule->usage_code_id = $usage_code->id;

        if ($medication_set_rule->save()) {
            return true;
        }
        return false;
    }

    public function addMedicationAttribute($medication_attribute, $value)
    {
        $medication_set_auto_rule_attribute = new MedicationSetAutoRuleAttribute();
        $medication_attribute_option = MedicationAttributeOption::model()->findByAttributes(
            array(
                'medication_attribute_id' => $medication_attribute->id,
                'value' => $value
            ))->id;
        $this->tmp_attrs[] = array(
            'id' => $this->id,
            'medication_attribute_option_id' => $medication_set_auto_rule_attribute->medication_attribute_option_id
        );
        if ($medication_attribute_option) {
            $medication_set_auto_rule_attribute->medication_set_id = $this->id;
            $medication_set_auto_rule_attribute->medication_attribute_option_id = $medication_attribute_option;
            if ($medication_set_auto_rule_attribute->save()) {
                return true;
            }
        }
        return false;
    }

    public function isUnique($attribute, $params)
    {
        if (!$this->medicationSetRules) {
            ## if name exists in medication_set table and id is not in medication_set_rule table then error
            $set = self::model()->findAll('name = :name', [':name' => $this->{$attribute}]);

            if ($set) {
                $set_ids = array_map(function ($medication_set) {
                    return $medication_set['id'];
                }, $set);

                if (!in_array($this->id, $set_ids) && !MedicationSetRule::model()->count('medication_set_id = :medication_set_id', [':medication_set_id' => implode(",", array_column($set, 'id'))])) {
                    $this->addError($attribute, 'A medication set with the name ' . $this->{$attribute} . ' already exists with no rules');
                }
            }
        }
    }
}
