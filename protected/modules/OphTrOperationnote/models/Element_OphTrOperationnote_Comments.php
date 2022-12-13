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
 * This is the model class for table "element_procedurelist".
 *
 * The followings are the available columns in table 'element_operation':
 *
 * @property string $id
 * @property int $event_id
 * @property int $assistant_id
 * @property int $anaesthetic_type
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class Element_OphTrOperationnote_Comments extends Element_OpNote
{
    public $service;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return ElementOperation the static model class
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
        return 'et_ophtroperationnote_comments';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.

        return array(
            array('event_id, comments, postop_instructions', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('postop_instructions', 'checkMinimalLengthIfNeeded', 'min' => 1),
            array('id, event_id, comments, postop_instructions', 'safe', 'on' => 'search'),
        );
    }

    /**
     * Check minimal length if needed
     *
     * @param $attribute
     * @param $params
     */
    public function checkMinimalLengthIfNeeded($attribute, $params)
    {
        $mandatory_post_op_instructions = SettingMetadata::model()->findByAttributes(array('key' => 'mandatory_post_op_instructions'));
        if ( $mandatory_post_op_instructions->getSettingName() == 'On' ) {
            if (strlen($this->$attribute) < $params['min']) {
                $message = $this->getAttributeLabel($attribute).' is a required field';
                $this->addError($attribute, $message, $params);
            }
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'comments' => 'Operation comments',
            'postop_instructions' => 'Post-op instructions',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('comments', $this->comments);
        $criteria->compare('postop_instructions', $this->postop_instructions);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getPrefillableAttributeSet()
    {
        return [
            'comments',
            'postop_instructions'
        ];
    }

    public function getPostop_instructions_list()
    {
        $criteria = new CDbCriteria();

        $criteria->addCondition('subspecialty_id = :subspecialtyId and site_id = :siteId');
        $criteria->params[':subspecialtyId'] = Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id;
        $criteria->params[':siteId'] = Yii::app()->session['selected_site_id'];

        $criteria->order = 'display_order asc';

        return CHtml::listData(OphTrOperationnote_PostopInstruction::model()
            ->findAll($criteria), 'id', 'content');
    }
}
