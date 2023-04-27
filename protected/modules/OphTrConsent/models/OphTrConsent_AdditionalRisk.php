<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "ophtrconsent_additional_risk".
 *
 * The followings are the available columns in table:
 *
 * @property integer $id
 * @property string $name
 * @property integer $institution_id
 * @property integer $active
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 */

class OphTrConsent_AdditionalRisk extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrConsent_AdditionalRisk|BaseActiveRecord the static model class
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
        return 'ophtrconsent_additional_risk';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false) . '.display_order');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, active, institution_id, display_order', 'safe'),
            array('name', 'length', 'max' => 255),
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
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'subspecialties' => array(self::HAS_MANY,'OphTrConsent_AdditionalRiskSubspecialtyAssignment','additional_risk_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array();
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('institution_id', $this->institution_id, true);
        $criteria->compare('active', $this->active, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    public function saveAdditionalRiskSubspecialtyAssignments($subspecialties)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            \OphTrConsent_AdditionalRiskSubspecialtyAssignment::model()->deleteAll('additional_risk_id=:id', [':id'=>$this->id]);
            foreach ($subspecialties as $subspecialty) {
                $subspecialty_obj = new \OphTrConsent_AdditionalRiskSubspecialtyAssignment;
                $exists = $subspecialty_obj->find(
                    'subspecialty_id=:subspecialty_id AND additional_risk_id=:additional_risk_id',
                    [':subspecialty_id'=>$subspecialty,':additional_risk_id'=>$this->id]
                );
                if (!$exists) {
                    $subspecialty_obj->subspecialty_id = $subspecialty;
                    $subspecialty_obj->additional_risk_id = $this->id;
                    if (!$subspecialty_obj->save()) {
                        throw new Exception(
                            'Unable to save subspecialty assignment. '.print_r($subspecialty_obj->getErrors(), true)
                        );
                    }
                }
            }
        } catch (Exception $e) {
            $transaction->rollback();
        }
        $transaction->commit();
    }
}
