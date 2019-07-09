<?php

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
			array('antecedent_medication_set_id, display_order, hidden, automatic', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('name, deleted_date, last_modified_date, created_date, automatic, hidden', 'safe'),
			array('medicationSetRules', 'safe'), //autosave relation in admin drugSet page
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, hidden, automatic, antecedent_medication_set_id, deleted_date, display_order, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'adminListAction' => 'Action',
            'itemsCount' => 'Items count',
            'rulesString' => 'Rules',
			'hidden' => 'Hidden/system',
			'automatic' => 'Automatic'
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

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('antecedent_medication_set_id',$this->antecedent_medication_set_id);
		$criteria->compare('deleted_date',$this->deleted_date,true);
		$criteria->compare('display_order',$this->display_order);
		$criteria->compare('last_modified_user_id',$this->last_modified_user_id,true);
		$criteria->compare('last_modified_date',$this->last_modified_date,true);
		$criteria->compare('created_user_id',$this->created_user_id,true);
		$criteria->compare('created_date',$this->created_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MedicationSet the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * Returns RefSets that belong to site and subspecialty
     *
     * @param null $site_id             defaults to currently selected
     * @param null $subspecialty_id     defaults to currently selected
     * @return CActiveRecord[]
     */

    public static function getAvailableSets($site_id = null, $subspecialty_id = null, $usage_code = null)
    {
        if(is_null($site_id)) {
            $site_id = Yii::app()->session['selected_site_id'];
        }

        if(is_null($subspecialty_id)) {
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

        if(!is_null($usage_code)) {
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
            $ret_val[]= "Site: " . ($rule->site ? $rule->site->name : '-') .
                ", SS: " . ($rule->subspecialty ? $rule->subspecialty->name : "-") .
                ", Usage code: " . $rule->usage_code;
        }

        return implode(" // ", $ret_val);
    }

    public function scopes()
    {
        return array(
            'byName' =>  array('order' => 'name ASC'),
        );
    }

	public function beforeValidate()
	{
		if($this->automatic) {
			foreach ($this->tmp_attrs as $attr) {
				if($attr['medication_attribute_option_id'] == '') {
					$this->addError('attribute', 'Attribute value must be set');
				}
			}
		}

		return parent::beforeValidate();
	}

	public function afterSave()
	{
		if($this->automatic) {
			$this->_saveAutoAttrs();
			$this->_saveAutoSets();
			$this->_saveAutoMeds();
		}

		return parent::afterSave();
	}

	/**
	 * Saves temporary auto attributes
	 * applies to automatic sets only
	 */

	private function _saveAutoAttrs()
	{
		$existing_ids = array_map(function($e){ return $e->id; }, $this->medicationAutoRuleAttributes);
		$updated_ids = array();
		foreach ($this->tmp_attrs as $attr) {
			if($attr['id'] == -1) {
				$attrib = new MedicationSetAutoRuleAttribute();
			}
			else {
				$attrib = MedicationSetAutoRuleAttribute::model()->findByPk($attr['id']);
			}

			$attrib->medication_attribute_option_id = $attr['medication_attribute_option_id'];
			$attrib->medication_set_id = $this->id;
			$attrib->save();
			$updated_ids[] = $attrib->id;
		}
		$ids_to_delete = array_diff($existing_ids, $updated_ids);
		if(!empty($ids_to_delete)) {

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
		$existing_ids = array_map(function($e){ return $e->id; }, $this->medicationSetAutoRuleSetMemberships);
		$updated_ids = array();
		foreach ($this->tmp_sets as $set) {
			if($set['id'] == -1) {
				$set_m = new MedicationSetAutoRuleSetMembership();
			}
			else {
				$set_m = MedicationSetAutoRuleSetMembership::model()->findByPk($set['id']);
			}

			$set_m->source_medication_set_id = $set['medication_set_id'];
			$set_m->target_medication_set_id = $this->id;
			$set_m->save();
			$updated_ids[] = $set_m->id;
		}
		$ids_to_delete = array_diff($existing_ids, $updated_ids);
		if(!empty($ids_to_delete)) {
            $criteria = new \CDbCriteria();
            $criteria->addInCondition('id', $ids_to_delete);
            MedicationSetAutoRuleSetMembership::model()->deleteAll($criteria);
		}
	}

	/**
	 * Saves temporary individual medications for auto sets
	 * Applies to automatic sets only
	 */

	private function _saveAutoMeds()
	{
		$existing_ids = array_map(function($e){ return $e->id; }, $this->medicationSetAutoRuleMedications);
		$updated_ids = array();
		foreach ($this->tmp_meds as $med) {
			if($med['id'] == -1) {
				$med_m = new MedicationSetAutoRuleMedication();
			}
			else {
				$med_m = MedicationSetAutoRuleMedication::model()->findByPk($med['id']);
			}

			$med_m->medication_id = $med['medication_id'];
			$med_m->include_children = $med['include_children'];
			$med_m->include_parent = $med['include_parent'];
			$med_m->medication_set_id = $this->id;
			$med_m->save();
			$updated_ids[] = $med_m->id;
		}
		$ids_to_delete = array_diff($existing_ids, $updated_ids);
		if(!empty($ids_to_delete)) {

            $criteria = new \CDbCriteria();
            $criteria->addInCondition('id', $ids_to_delete);
            MedicationSetAutoRuleMedication::model()->deleteAll($criteria);
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
		if(!$this->automatic || in_array($this->id, self::$_processed)) {
			Yii::log("Skipping ".$this->name." because it's already processed.");
			return false;
		}

		self::$_processed[] = $this->id;

		Yii::log("Started processing ".$this->name);

		$cmd = Yii::app()->db->createCommand();
		/** @var CDbCommand $cmd */
		$cmd->select('id', 'DISTINCT')->from('medication');
		$attribute_option_ids = array_map(function ($e){ return $e->id; }, $this->autoRuleAttributes);

		$auto_set_ids = array_map(function ($e) {
				return $e->id;
			},
			array_filter($this->autoRuleSets, function($e){
				return $e->automatic == 1;
			})
		);

		$nonauto_set_ids = array_map(function ($e) {
				return $e->id;
			},
			array_filter($this->autoRuleSets, function($e){
				return $e->automatic == 0;
			})
		);

		$no_condition = true;

		if(!empty($attribute_option_ids)) {
			$cmd->orWhere("id IN (SELECT medication_id FROM ".MedicationAttributeAssignment::model()->tableName()."
												WHERE medication_attribute_option_id IN (".implode(",", $attribute_option_ids).")
												)");
			$no_condition = false;
		}

		if(!empty($nonauto_set_ids)) {
			$cmd->orWhere("id IN (SELECT medication_id FROM ".MedicationSetItem::model()->tableName()."
												WHERE medication_set_id IN (".implode(",", $nonauto_set_ids).")
												)");
			$no_condition = false;
		}

		foreach ($this->medicationSetAutoRuleMedications as $medicationSetAutoRuleMedication) {
			if($medicationSetAutoRuleMedication->include_parent) {
				$medication = $medicationSetAutoRuleMedication->medication;
				if($medication->isAMP()) {
					$cmd->orWhere("preferred_code = '{$medication->vmp_code}'");
					if($vmp = Medication::model()->findAll("preferred_code = '{$medication->vmp_code}'")){
						$vmp = $vmp[0];
					};
					$cmd->orWhere("preferred_code = '{$vmp->vtm_code}'");
				}
				elseif ($medication->isVMP()) {
					$cmd->orWhere("preferred_code = '{$medication->vtm_code}'");
				}
			}
			if($medicationSetAutoRuleMedication->include_children) {
				$medication = $medicationSetAutoRuleMedication->medication;
				if($medication->isVTM()) {
					$cmd->orWhere("vtm_code = '{$medication->preferred_code}'");
				}
				elseif ($medication->isVMP()) {
					$cmd->orWhere("vmp_code = '{$medication->preferred_code}'");
				}
			}
			$cmd->orWhere("id = ".$medicationSetAutoRuleMedication->medication_id);
			$no_condition = false;
		}

		$ids = $cmd->queryColumn();
		// empty the set
		Yii::app()->db->createCommand("DELETE FROM ".MedicationSetItem::model()->tableName()." WHERE medication_set_id = ".$this->id)->execute();
		if(!$no_condition && !empty($ids)) {
			// repopulate
			$values = array();
			foreach ($ids as $id) {
				$values[] = "({$this->id},$id)";
			}
			Yii::app()->db->createCommand("INSERT INTO ".MedicationSetItem::model()->tableName()." (medication_set_id, medication_id)
									VALUES ".implode(",", $values))->execute();
		}

		Yii::log("Processed non-auto rules in ".$this->name);

		// process auto sub sets recursively
		if(!empty($auto_set_ids)) {
			foreach ($auto_set_ids as $auto_id) {
				if(!in_array($auto_id, self::$_processed)) {
					// Sub set is not already processed
					$included_set = self::model()->findByPk($auto_id);
					/** @var self $included_set */
					$included_set->populateAuto();
				}

				// Include items from sub set
				$table = MedicationSetItem::model()->tableName();
				Yii::log("Adding sub set items into ".$this->name);
				$items = Yii::app()->db->createCommand("SELECT medication_id FROM $table WHERE medication_set_id = $auto_id
														AND medication_id NOT IN (SELECT medication_id FROM $table WHERE medication_set_id = {$this->id})")->queryColumn();
				$values = array();
				foreach ($items as $id) {
					$values[] = "({$this->id},$id)";
				}
				Yii::app()->db->createCommand("INSERT INTO ".MedicationSetItem::model()->tableName()." (medication_set_id, medication_id)
									VALUES ".implode(",", $values))->execute();
			}
		}

		Yii::log("Done processing ".$this->name);

		return count($ids);
	}

	/**
	 * Runs processing routine for all auto sets
	 *
	 * @return array	The list of ids that were processed
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
		if($this->automatic) {
			foreach ($this->medicationSetAutoRuleSetMemberships as $set_m) {
				$set_m->delete();
			}
			foreach ($this->medicationSetAutoRuleMedications as $med) {
				$med->delete();
			}
			foreach ($this->medicationAutoRuleAttributes as $attribute) {
				$attribute->delete();
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

            if ($assignment->save()){
                return $assignment->id;
            }
        }
        return false;
    }
}
