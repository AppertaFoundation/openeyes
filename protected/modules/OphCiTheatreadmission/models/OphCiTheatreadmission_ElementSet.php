<?php

/**
 * This is the model class for table "ophcitheatreadmission_element_set".
 *
 * The followings are the available columns in table 'ophcitheatreadmission_element_set':
 * @property integer $id
 * @property string $name
 * @property string $position
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class OphCiTheatreadmission_ElementSet extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcitheatreadmission_element_set';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'length', 'max'=>40),
            array('position, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, position, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'items' => array(self::HAS_MANY, 'OphCiTheatreadmission_ElementSetItem', 'set_id',
                'with' => 'element_type',
                'order' => 'items.display_order, element_type.display_order',
            ),
            'visibleItems' => array(self::HAS_MANY, 'OphCiTheatreadmission_ElementSetItem', 'set_id',
                'with' => 'element_type',
                'condition' => 'is_hidden = 0',
                'order' => 'element_type.name',
            ),
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
            'position' => 'Position',
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
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('position', $this->position, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphCiTheatreadmission_ElementSet the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getWorkFlowMaximumDisplayOrder()
    {
        $maximum_display_order = 0;
        foreach ($this->visibleItems as $item) {
            if ($item->display_order && $item->display_order > $maximum_display_order) {
                $maximum_display_order = $item->display_order;
            }
        }
        return $maximum_display_order;
    }

    /**
     * Returns the given elements set specific display order if it exists.
     *
     * @return int
     */
    public function getSetElementOrder($element)
    {
        foreach ($this->visibleItems as $item) {
            if ($element->getElementType()->class_name == $item->element_type->class_name && $item->display_order) {
                return $item->display_order;
            }
        }
        return null;
    }

    /**
     * Get an array of ElementTypes corresponding with the items in this set.
     *
     * @return ElementType[]
     */
    public function getDefaultElementTypes($action = 'edit')
    {
        $element_types = [];
        $maximum_worklist_display_order = $this->getWorkFlowMaximumDisplayOrder();

        foreach ($this->visibleItems as $item) {
            if ($item->display_order) {
                $element_types[$item->display_order] = $item->element_type;
            } else {
                $element_types[$maximum_worklist_display_order + $item->element_type->display_order] = $item->element_type;
            }
        }

        ksort($element_types);
        return $element_types;
    }

    public function getHiddenElementTypes()
    {
        $hiddenElementTypes = \ElementType::model()->findAll(array(
            'condition' => "event_type.class_name = 'OphCiTheatreadmission' AND
					 ophcitheatreadmission_element_set_item.is_hidden = 1",
            'join' => 'JOIN event_type ON event_type.id = t.event_type_id
					LEFT JOIN ophcitheatreadmission_element_set_item ON (ophcitheatreadmission_element_set_item.element_type_id = t.id
					AND ophcitheatreadmission_element_set_item.set_id = :set_id)',
            'params' => array(':set_id' => $this->id),
        ));

        return $hiddenElementTypes;
    }

    public function getMandatoryElementTypes()
    {
        $mandatoryElementTypes = \ElementType::model()->findAll(array(
            'condition' => "event_type.class_name = 'OphCiExamination' AND
					 ophcitheatreadmission_element_set_item.is_mandatory = 1",
            'join' => 'JOIN event_type ON event_type.id = t.event_type_id
					LEFT JOIN ophcitheatreadmission_element_set_item ON (ophcitheatreadmission_element_set_item.element_type_id = t.id
					AND ophcitheatreadmission_element_set_item.set_id = :set_id)',
            'params' => array(':set_id' => $this->id),
        ));

        return $mandatoryElementTypes;
    }

    public function getNextStep()
    {
        $criteria = new \CDbCriteria(array(
            'condition' => 'position >= :position AND id <> :id',
            'order' => 'position, id',
            'params' => array(':position' => $this->position, ':id' => $this->id),
        ));

        return $this->find($criteria);
    }
}
