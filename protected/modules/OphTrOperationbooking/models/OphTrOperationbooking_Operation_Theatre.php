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
 * This is the model class for table "ophtroperationbooking_operation_theatre".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property string $name
 * @property int $site_id
 * @property int $ward_id
 * @property string $code
 *
 * The followings are the available model relations:
 * @property Site $site
 */
class OphTrOperationbooking_Operation_Theatre extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrOperationbooking_Operation_Theatre|BaseActiveRecord the static model class
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
        return 'ophtroperationbooking_operation_theatre';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.name');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, site_id, code, ward_id', 'safe'),
            array('name, site_id, code', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, site_id, code', 'safe', 'on' => 'search'),
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
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'sessions' => array(self::HAS_MANY, 'OphTrOperationbooking_Operation_Session', 'theatre_id'),
            'ward' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Ward', 'ward_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'site_id' => 'Site',
            'ward_id' => 'Ward',
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
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

    public function getNameWithSite()
    {
        return $this->name.' ('.$this->site->name.')';
    }

    /**
     * Get all Sites that have theatres attached
     *
     * @param null $current_site_id
     * @return Site[]|null
     */
    public static function getSiteList($current_site_id = null)
    {
        $model = static::model();
        $cmd = Yii::app()->db->createCommand()
            ->selectDistinct('site_id')
            ->where('active = 1')
            ->from($model->tableName());

        $ids = array_map(function ($r) {
            return $r['site_id'];
        }, $cmd->queryAll());

        $criteria = new CDbCriteria();
        $criteria->addCondition("active = 1 and short_name != ''");
        $criteria->addCondition("institution_id = :institution_id");
        $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
        $criteria->addInCondition('id', $ids);
        if ($current_site_id) {
            $criteria->addCondition('id = :id', 'OR');
            $criteria->params = array_merge($criteria->params, array(':id' => $current_site_id));
        }
        $criteria->order = 'short_name';

        return Site::model()->findAll($criteria);
    }

    public static function getTheatresForCurrentInstitution()
    {
        $site_id_list = array_map(
            static function ($site) {
                return $site->id;
            },
            Institution::model()->getCurrent()->sites
        );
        return self::model()->active()->findAll('site_id IN (' . implode(', ', $site_id_list) . ')');
    }
}
