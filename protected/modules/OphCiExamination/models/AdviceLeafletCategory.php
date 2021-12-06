<?php

namespace OEModule\OphCiExamination\models;
/**
 * This is the model class for table "ophciexamination_advice_leaflet_category".
 *
 * The followings are the available columns in table 'ophciexamination_advice_leaflet_category':
 * @property int $id
 * @property string $name
 * @property string $display_order
 * @property int $institution_id
 * @property int $active
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 * @property AdviceLeafletCategoryAssignment[] $category_assignments
 * @property AdviceLeaflet[] $leaflets
 * @property \Institution $institution
 */
class AdviceLeafletCategory extends \BaseActiveRecordVersioned
{
    use \MappedReferenceData;

    public function getSupportedLevels(): int
    {
        return \ReferenceData::LEVEL_INSTITUTION;
    }

    public function mappingColumn(int $level): string
    {
        return $this->tableName() . '_id';
    }

    protected function mappingModelName(int $level): string
    {
        return __CLASS__ . ucfirst($this->getModelSuffixForLevel($level));
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_advice_leaflet_category';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('active', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>255),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, active, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'leaflets' => array(self::MANY_MANY, AdviceLeaflet::class, 'ophciexamination_advice_leaflet_category_assignment(category_id, leaflet_id)', 'order' => 'leaflets_leaflets.display_order'),
            'leaflet_assignments' => array(self::HAS_MANY, AdviceLeafletCategoryAssignment::class, 'category_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'subspecialties' => array(self::MANY_MANY, 'Subspecialty', 'ophciexamination_advice_leaflet_category_subspecialty(category_id, subspecialty_id)')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'active' => 'Active',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
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
     * @return \CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('active', $this->active);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AdviceLeafletCategory the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeSave()
    {
        if ($this->id !== null) {
            foreach (AdviceLeafletCategoryAssignment::model()->findAll('category_id=?', array($this->id)) as $leaflet_entry) {
                $leaflet_entry->delete();
            }
        }
        return parent::beforeSave();
    }

    protected function afterSave()
    {
        foreach ($this->leaflets as $i => $entry) {
            $leaflet_assignment = new AdviceLeafletCategoryAssignment();
            $leaflet_assignment->category_id = $this->id;
            $leaflet_assignment->leaflet_id = $entry;
            $leaflet_assignment->display_order = $i;
            $leaflet_assignment->save(true);
        }
        parent::afterSave();
    }
}
