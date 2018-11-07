<?php

/**
 * This is the model class for table "ref_set".
 *
 * The followings are the available columns in table 'ref_set':
 * @property integer $id
 * @property string $name
 * @property integer $antecedent_ref_set_id
 * @property string $deleted_date
 * @property integer $display_order
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property RefMedicationSet[] $refMedicationSets
 * @property RefSet $antecedentRefSet
 * @property RefSet[] $refSets
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property RefSetRule[] $refSetRules
 * @property RefMedication[] $refMedications
 * @property RefMedicationSet[] $items
 * @method  RefMedication[] refMedications(array $opts)
 */
class RefSet extends BaseActiveRecordVersioned
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ref_set';
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
			array('antecedent_ref_set_id, display_order', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('deleted_date, last_modified_date, created_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, antecedent_ref_set_id, deleted_date, display_order, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
			'refMedicationSets' => array(self::HAS_MANY, 'RefMedicationSet', 'ref_set_id'),
			'antecedentRefSet' => array(self::BELONGS_TO, 'RefSet', 'antecedent_ref_set_id'),
			'refSets' => array(self::HAS_MANY, 'RefSet', 'antecedent_ref_set_id'),
			'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'refSetRules' => array(self::HAS_MANY, RefSetRule::class, 'ref_set_id'),
            'refMedications' => array(self::MANY_MANY, RefMedication::class, 'ref_medication_set(ref_set_id,ref_medication_id)'),
		);
	}

    /**
     * @return RefMedicationSet[]
     *
     * Compatibility function to map $this->items to $this->refMedicationSets
     */

	public function getItems()
    {
        return $this->refMedicationSets;
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'antecedent_ref_set_id' => 'Antecedent Ref Set',
			'deleted_date' => 'Deleted Date',
			'display_order' => 'Display Order',
			'last_modified_user_id' => 'Last Modified User',
			'last_modified_date' => 'Last Modified Date',
			'created_user_id' => 'Created User',
			'created_date' => 'Created Date',
            'adminListAction' => 'Action',
            'itemsCount' => 'Items count',
            'rulesString' => 'Rules',
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
		$criteria->compare('antecedent_ref_set_id',$this->antecedent_ref_set_id);
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
	 * @return RefSet the static model class
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
            'refSetRules' => array(
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

    public function itemsCount()
    {
        $result = Yii::app()->db->createCommand("SELECT COUNT(id) AS cnt FROM ref_medication_set WHERE ref_set_id = ".$this->id)->queryScalar();
        return $result;
    }

    public function rulesString()
    {
        $ret_val = [];
        foreach ($this->refSetRules as $rule) {
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
}
