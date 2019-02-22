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
 */
class MedicationSet extends BaseActiveRecordVersioned
{
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
			array('antecedent_medication_set_id, display_order, hidden', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('deleted_date, last_modified_date, created_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, hidden, antecedent_medication_set_id, deleted_date, display_order, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
			'hiddenString' => 'Hidden/system'
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

    public function adminListAction()
    {
        return '<a href="/OphDrPrescription/refMedicationSetAdmin/list?ref_set_id='.$this->id.'">List medications</a>';
    }

	public function getAdminListAction()
	{
		return $this->adminListAction();
    }

    public function itemsCount()
    {
        $result = Yii::app()->db->createCommand("SELECT COUNT(id) AS cnt FROM medication_set_item WHERE medication_set_id = ".$this->id)->queryScalar();
        return $result;
    }

    public function getItemsCount()
	{
		return $this->itemsCount();
	}

    public function rulesString()
    {
        $ret_val = [];
        foreach ($this->medicationSetRules as $rule) {
            $ret_val[]= "Site: ".(is_null($rule->site_id) ? "-" : $rule->site->name).
                ", SS: ".(is_null($rule->subspecialty_id) ? "-" : $rule->subspecialty->name).
                ", Usage code: ".$rule->usage_code;
        }

        return implode(" // ", $ret_val);
    }

    public function scopes()
    {
        return array(
            'byName' =>  array('order' => 'name ASC'),
        );
    }

	public function hiddenString()
	{
		return $this->hidden ? "Yes" : "No";
    }
}
