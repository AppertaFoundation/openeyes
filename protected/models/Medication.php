<?php

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
 * @property string $deleted_date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property EventMedicationUse[] $eventMedicationUses
 * @property User $lastModifiedUser
 * @property User $createdUser
 * @property MedicationSet[] $medicationSets
 * @property MedicationSearchIndex[] $medicationSearchIndexes
 * @property MedicationAttribute[] $medicationAttributes
 * @property MedicationAttributeAssignment[] $medicationAttributeAssignments
 *
 * @method  MedicationAttribute[] medicationAttributes($opts)
 */
class Medication extends BaseActiveRecordVersioned
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'medication';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('source_type, preferred_term, preferred_code', 'required'),
			array('source_type, last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('source_subtype', 'length', 'max' => 45),
			array('preferred_term, short_term, preferred_code, vtm_term, vtm_code, vmp_term, vmp_code, amp_term, amp_code', 'length', 'max'=>255),
			array('deleted_date, last_modified_date, created_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, source_type, source_subtype, preferred_term, preferred_code, vtm_term, vtm_code, vmp_term, vmp_code, amp_term, amp_code, deleted_date, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
			'eventMedicationUses' => array(self::HAS_MANY, EventMedicationUse::class, 'medication_id'),
			'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'medicationSets' => array(self::MANY_MANY, MedicationSet::class, 'medication_set_item(medication_id, medication_set_id)'),
			'medicationSearchIndexes' => array(self::HAS_MANY, MedicationSearchIndex::class, 'medication_id'),
            'medicationAttributeAssignments' => array(self::HAS_MANY, MedicationAttributeAssignment::class, 'medication_id'),
            'medicationAttributes' => array(self::HAS_MANY, MedicationAttribute::class, 'medication_attribute_assignment(medication_id,medication_attribute_id)')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
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
            'will_copy' => 'Will copy'
		);
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

		$criteria->compare('id',$this->id);
		$criteria->compare('source_type',$this->source_type,true);
		$criteria->compare('source_subtype',$this->source_subtype,true);
		$criteria->compare('preferred_term',$this->preferred_term,true);
		$criteria->compare('preferred_code',$this->preferred_code,true);
		$criteria->compare('vtm_term',$this->vtm_term,true);
		$criteria->compare('vtm_code',$this->vtm_code,true);
		$criteria->compare('vmp_term',$this->vmp_term,true);
		$criteria->compare('vmp_code',$this->vmp_code,true);
		$criteria->compare('amp_term',$this->amp_term,true);
		$criteria->compare('amp_code',$this->amp_code,true);
		$criteria->compare('deleted_date',$this->deleted_date,true);
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
	 * @return Medication the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @return bool
     */

    public function getToBeCopiedIntoMedicationManagement()
    {
        $med_sets = array_map(function($e){ return $e->id; }, MedicationSet::model()->with('medicationSetRules')->findAll("medicationSetRules.usage_code = 'Management'"));

        foreach ($this->medicationSets as $medSet) {
            if(in_array($medSet->id, $med_sets)) {
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
        $criteria = new CDbCriteria();
        $criteria->condition = "id IN (SELECT medication_id FROM medication_set_item WHERE medication_set_id IN 
                                        (SELECT medication_set_id FROM medication_set_rule WHERE usage_code = 'Common subspecialty medications' 
                                            AND site_id=:site_id AND subspecialty_id=:subspecialty_id))";
        $criteria->params = array(":site_id" => $site_id, "subspecialty_id" => $subspecialty_id);
        $criteria->order = 'preferred_term';
        return $this->findAll($criteria);
    }

    /**
     * @return MedicationSet[]
     */

    public function getTypes()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "id IN (SELECT medication_set_id FROM medication_set_item WHERE medication_id = :medication_id 
                                            AND medication_set_id IN (SELECT medication_set_id FROM medication_set_rule WHERE usage_code = 'DrugTag'))";
        $criteria->params = array(":medication_id" => $this->id);
        $criteria->order = 'name';
        return MedicationSet::model()->findAll($criteria);
    }

    /**
     * @return bool
     */

    public function isPreservativeFree()
    {
        foreach ($this->getTypes() as $type) {
            if($type->name == 'Preservative free') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */

    public function getLabel($short = false)
    {
        $name =  $short ? ($this->short_term != "" ? $this->short_term : $this->preferred_term): $this->preferred_term;

        if($this->isAMP()) {
            $name.=" (".$this->vtm_term.")";
        }

        if ($this->isPreservativeFree()) {
            $name.= ' (No Preservative)';
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
     * @return string
     */

    public function alternativeTerms()
    {
        $terms = [];
        foreach ($this->medicationSearchIndexes as $idx) {
            $terms[] = $idx->alternative_term;
        }

        return implode(", ", $terms);
    }

    private function listByUsageCode($usage_code, $subspecialty_id = null, $raw = false)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('medicationSetRules.usage_code',$usage_code);
        if(!is_null($subspecialty_id)) {
            $criteria->compare('medicationSetRules.subspecialty_id', $subspecialty_id);
        }
        $sets = MedicationSet::model()->with('medicationSetRules')->findAll($criteria);

        $return = [];
        $ids = [];

        /** @var MedicationSet[] $sets */
        foreach ($sets as $set) {
            foreach ($set->items as $item) {
                if(in_array($item->medication->id, $ids)) {
                    continue;
                }
                $return[] = array(
                    'label' => $item->medication->preferred_term,
                    'value' => $item->medication->preferred_term,
                    'name' => $item->medication->preferred_term,
                    'id' => $item->medication->id,
                    'dose_unit_term' => $item->default_dose_unit_term,
                    'dose' => $item->default_dose,
                    'default_form' => $item->default_form_id,
                    'frequency_id' => $item->default_frequency_id,
                    'route' => $item->default_route_id,
                    'will_copy' => $item->medication->getToBeCopiedIntoMedicationManagement(),
                    'set_ids' =>  array_map(function ($e){
                        return $e->id;
                    } , $item->medication->getMedicationSetsForCurrentSubspecialty())
                );
                $ids[] = $item->medication->id;
            }
        }

        usort($return, function($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $raw ? $return : CHtml::listData($return, 'id', 'label');
    }

    public function listBySubspecialtyWithCommonMedications($subspecialty_id, $raw = false)
    {
        return $this->listByUsageCode("Common subspecialty medications", $subspecialty_id, $raw);
    }

    public function listCommonSystemicMedications($raw = false)
    {
        return $this->listByUsageCode("COMMON_SYSTEMIC", null, $raw);
    }

    public function listCommonOphThalmicMedications($raw = false)
    {
        return $this->listByUsageCode("COMMON_OPH", null, $raw);
    }

    public function getMedicationSetsForCurrentSubspecialty()
    {
        $firm_id = $this->getApp()->session->get('selected_firm_id');
        $site_id = $this->getApp()->session->get('selected_site_id');
        /** @var Firm $firm */
        $firm = $firm_id ? Firm::model()->findByPk($firm_id) : null;
        if($firm) {
            $sets = array();
            foreach($this->medicationSets as $set) {
                $relevant = false;
                foreach ($set->medicationSetRules as $rule) {
                    if($rule->subspecialty_id === null && $rule->site_id === null) {
                        $relevant = true;
                    }

                    if($rule->subspecialty_id == $firm->subspecialty_id && $rule->site_id == $site_id) {
                        $relevant = true;
                    }
                }

                if($relevant) {
                    $sets[] = $set;
                }
            }

            return $sets;
        }
        else {
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
                if($rule->usage_code == $usage_code) {
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
     * Returns all attributes as name=>value pairs
     *
     * @return array
     */

    public function getAttrs()
    {
        $ret = array();
        foreach ($this->medicationAttributeAssignments as $attr_assignment) {
            $ret[$attr_assignment->medicationAttribute->name] = $attr_assignment->value;
        }

        return $ret;
    }

    /**
     * Returns drug attribute(s) by name
     *
     * @param $attr_name
     * @return MedicationAttribute[]
     */

    public function getAttr($attr_name)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "name = :name";
        $criteria->params['name'] = $attr_name;
        $criteria->with = 'medication_attribute_assignment';
        return $this->medicationAttributes($criteria);
    }
}
