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
 * This is the model class for table "et_ophtroperationnote_genericprocedure".
 *
 * The followings are the available columns in table 'element_operation':
 *
 * @property string $id
 * @property int $event_id
 * @property int $proc_id
 * @property string $comments
 * @property int $element_index
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property Procedure $procedure
 */
class Element_OphTrOperationnote_GenericProcedure extends Element_OnDemand
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
        return 'et_ophtroperationnote_genericprocedure';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, proc_id, comments, element_index', 'safe'),
            array('proc_id, element_index', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, element_index, proc_id, comments', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'procedure' => array(self::BELONGS_TO, 'Procedure', 'proc_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'proc_id' => 'Procedure',
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

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    public function getPrefillableAttributeSet()
    {
        return [
            'proc_id',
            'comments',
            'element_index'
        ];
    }

    /**
     * Name is defined by the procedure assigned to this element.
     *
     * @return string
     */
    public function getElementTypeName()
    {
        if ($this->procedure) {
            return $this->procedure->term;
        } else {
            return 'No Procedure';
        }
    }

    public function getContainer_form_view()
    {
        return false;
    }

    public function setDefaultOptions(Patient $patient = null)
    {
        $genericProcedureData = OphTrOperationNote_Generic_Procedure_Data::model()->find(
            'proc_id =:proc_id',
            ['proc_id' => $this->proc_id]
        );
        $this->comments = isset($genericProcedureData) ? $genericProcedureData->default_text : null;
        parent::setDefaultOptions($patient);
    }

    public function getDefaultFormOptions(array $context): array
    {
        $fields = array();
        $genericProcedureData = OphTrOperationNote_Generic_Procedure_Data::model()->find(
            'proc_id =:proc_id',
            ['proc_id' => $this->proc_id]
        );
        $fields['comments'] = isset($genericProcedureData) ? $genericProcedureData->default_text : null;
        return array_merge($fields, parent::getDefaultFormOptions($context));
    }

    public static function canHaveMultipleOf()
    {
        return true;
    }

    public function getTemplateIndex()
    {
        return $this->proc_id ?? null;
    }
}
