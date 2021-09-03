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
 * This is the model class for table "institution".
 *
 * The followings are the available columns in table 'institution':
 *
 * @property int $id
 * @property string $name
 * @property int $remote_id
 * @property string $pas_key
 * @property string $short_name
 * @property int $contact_id
 * @property int $source_id
 *
 * The followings are the available model relations:
 * @property Contact $contact
 * @property Site[] $sites
 */
class Institution extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Institution the static model class
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
        return 'institution';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.name');
    }

    public function behaviors()
    {
        return array(
            'ContactBehavior' => array(
                'class' => 'application.behaviors.ContactBehavior',
            ),
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pas_key', 'length', 'max' => 10),
            array('name, remote_id, short_name', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name', 'safe', 'on' => 'search'),
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
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'sites' => array(self::HAS_MANY, 'Site', 'institution_id',
                'condition' => 'sites.active = 1',
                'order' => 'name asc',
            ),
            'logo' => array(self::BELONGS_TO, 'SiteLogo', 'logo_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'pas_key' => 'PAS Key',
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

    /**
     * @return Institution
     */
    public function getCurrent()
    {
        if (!isset(Yii::app()->params['institution_code'])) {
            throw new Exception('Institution code is not set');
        }

        $institution = $this->find('source_id=? and remote_id=?', array(1, Yii::app()->params['institution_code']));
        if (!$institution) {
            throw new Exception("Institution with code '".Yii::app()->params['institution_code']."' not found");
        }

        return $institution;
    }
}
