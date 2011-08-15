<?php

/**
 * This is the model class for table "disorder".
 *
 * The followings are the available columns in table 'disorder':
 * @property string $id
 * @property string $fully_specified_name
 * @property string $term
 * @property integer $systemic
 *
 * The followings are the available model relations:
 * @property CommonOphthalmicDisorder[] $commonOphthalmicDisorders
 * @property CommonSystemicDisorder[] $commonSystemicDisorders
 * @property Diagnosis[] $diagnosises
 */
class Disorder extends BaseActiveRecord
{
	const SITE_LEFT = 0;
	const SITE_RIGHT = 1;
	const SITE_BILATERAL = 2;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Disorder the static model class
	 */
	public static function model($className=__CLASS__)
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
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, fully_specified_name, term', 'required'),
			array('systemic', 'numerical', 'integerOnly'=>true),
			array('id', 'length', 'max'=>10),
			array('fully_specified_name, term', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, fully_specified_name, term, systemic', 'safe', 'on'=>'search'),
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
			'commonOphthalmicDisorders' => array(self::HAS_MANY, 'CommonOphthalmicDisorder', 'disorder_id'),
			'commonSystemicDisorders' => array(self::HAS_MANY, 'CommonSystemicDisorder', 'disorder_id'),
			'diagnoses' => array(self::HAS_MANY, 'Diagnosis', 'disorder_id'),
		);
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
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('fully_specified_name',$this->fully_specified_name,true);
		$criteria->compare('term',$this->term,true);
		$criteria->compare('systemic',$this->systemic);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Fetch a list of disorders whose term matches a provided value (with wildcards)
	 * 
	 * @param string $term
	 * 
	 * @return array
	 */
	public static function getDisorderOptions($term)
	{
		$disorders = Yii::app()->db->createCommand()
			->select('term')
			->from('disorder')
			->where('term LIKE :term', 
				array(':term'=>"%{$term}%"))
			->queryAll();

		$data = array();

		foreach ($disorders as $disorder) {
			$data[] = $disorder['term'];
		}

		return $data;
	}

        /**
         * Get a list of disorders
         * Store extra data for the session
         * 
         * @param string  $term          term to search by
         * 
         * @return array
         */
        public static function getList($term)
        {
                $search = "{$term}%";

                $select = 'fully_specified_name, id';

                $disorders = Yii::app()->db->createCommand()
                        ->select($select)
                        ->from('disorder')
                        ->where('(term LIKE :term OR fully_specified_name LIKE :format) AND systemic = 0',
                                array(':term'=>$search, ':format'=>$search))
                        ->queryAll();

                $data = array();
                $session = Yii::app()->session['Disorders'];

                foreach ($disorders as $disorder) {
                        $data[] = $disorder['fully_specified_name'];
                        $id = $disorder['id'];
                        $session[$id] = array(
                                'fully_specified_name' => $disorder['fully_specified_name'],
                        );
                }

                Yii::app()->session['Disorders'] = $session;

                return $data;
        }
}
