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
 * This is the model class for table "ophtrconsent_leaflet".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 */
class OphTrConsent_Leaflet extends BaseActiveRecordVersioned
{
    use MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_SUBSPECIALTY | ReferenceData::LEVEL_FIRM;
    }

    protected function mappingColumn(int $level): string
    {
        return 'leaflet_id';
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrConsent_Leaflet|BaseActiveRecord the static model class
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
        return 'ophtrconsent_leaflet';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, display_order', 'safe'),
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
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'subspecialties' => array(self::HAS_MANY, 'OphTrConsent_Leaflet_Subspecialty', 'leaflet_id'),
            'firms' => array(self::HAS_MANY, 'OphTrConsent_Leaflet_Firm', 'leaflet_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
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
        $criteria->compare('name', $this->name, true);

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

    public function findAllByCurrentFirm($leaflet_values)
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

        $criteria1 = new CDbCriteria();

        if ($firm->serviceSubspecialtyAssignment) {
            $criteria1->addCondition('subspecialty_id=:subspecialty_id');
            $criteria1->params[':subspecialty_id'] = $subspecialty_id;
            $criteria1->order = 'name asc';
            $return1 = self::model()->with('subspecialties')->activeOrPk($leaflet_values)->findAll($criteria1);
        }
        $criteria2 = new CDbCriteria();
        $criteria2->addCondition('firm_id=:firm_id');
        $criteria2->params[':firm_id'] = $firm->id;
        $criteria2->order = 'name asc';
        $return2 = self::model()->with('firms')->activeOrPk($leaflet_values)->findAll($criteria2);
        if (is_array($return1)) {
            return array_merge($return1, $return2);
        }

        return $return2;
    }
}
