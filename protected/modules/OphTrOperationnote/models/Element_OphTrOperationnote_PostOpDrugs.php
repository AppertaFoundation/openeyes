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
 * This is the model class for table "et_ophtroperationnote_postop_drugs".
 *
 * The followings are the available columns in table 'et_ophtroperationnote_postop_drugs':
 *
 * @property int $id
 * @property int $event_id
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property OphTrOperationnote_OperationDrug[] $drugs
 */
class Element_OphTrOperationnote_PostOpDrugs extends Element_OpNote
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphTrOperationnote_PostOpDrugs the static model class
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
        return 'et_ophtroperationnote_postop_drugs';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'drug_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_OperationDrug', 'ophtroperationnote_postop_drugs_id'),
            'drugs' => array(self::HAS_MANY, 'OphTrOperationnote_PostopDrug', 'drug_id',
                'through' => 'drug_assignments', ),
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

    /**
     * Need to delete associated records.
     *
     * @see CActiveRecord::beforeDelete()
     */
    protected function beforeDelete()
    {
        OphTrOperationnote_OperationDrug::model()->deleteAllByAttributes(array('ophtroperationnote_postop_drugs_id' => $this->id));

        return parent::beforeDelete();
    }

    public function updateDrugs($drug_ids)
    {
        $curr_by_id = array();
        foreach (OphTrOperationnote_OperationDrug::model()->findAll(
            'ophtroperationnote_postop_drugs_id = :drugsId',
            array(':drugsId' => $this->id)
        ) as $od) {
            $curr_by_id[$od->drug_id] = $od;
        }

        if (!empty($drug_ids)) {
            foreach ($drug_ids as $d_id) {
                if (!isset($curr_by_id[$d_id])) {
                    $da = new OphTrOperationnote_OperationDrug();
                    $da->ophtroperationnote_postop_drugs_id = $this->id;
                    $da->drug_id = $d_id;
                    if (!$da->save()) {
                        throw new Exception('Unable to save drug assignment: '.print_r($da->getErrors(), true));
                    }
                } else {
                    unset($curr_by_id[$d_id]);
                }
            }
        }

        foreach ($curr_by_id as $curr) {
            if (!$curr->delete()) {
                throw new Exception('Unable to delete drug assignment: '.print_r($curr->getErrors(), true));
            }
        }
    }

    public function getTileSize($action)
    {
        $action_list = array('view', 'createImage', 'removed');
        return in_array($action, $action_list) ? 1 : null;
    }
}
