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
            ['any_number_search_allowed, first_used_site_id, logo_id', 'safe'],
            ['id, name, any_number_search_allowed', 'safe', 'on' => 'search'],
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
            'authenticationMethods' =>  [
                self::HAS_MANY,
                InstitutionAuthentication::class,
                'institution_id',
                'condition' => 'authenticationMethods.active = 1',
            ],
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
        if (Yii::app()->session->get('selected_institution_id', null) === null) {
            throw new Exception('Institution id is not set');
        }

        $institution = $this->findByPk(Yii::app()->session->get('selected_institution_id'));
        if (!$institution) {
            throw new Exception("Institution with id '".Yii::app()->session->get('selected_institution_id')."' not found");
        }

        return $institution;
    }

    public function getList($current_institution_only = true)
    {
        $result = array();
        if ($current_institution_only) {
            $current_institution = $this->getCurrent();
            $result[$current_institution->id] = $current_institution->name;
        } else {
            $cmd = Yii::app()->db->createCommand()
                ->select('i.id, i.name')
                ->from('institution i');

            foreach ($cmd->queryAll() as $institution) {
                $result[$institution['id']] = $institution['name'];
            }

            natcasesort($result);
        }
        return $result;
    }

    public function getTenanted($condition = '', $params = array())
    {
        return $this->with('authenticationMethods')->findAll($condition, $params);
    }

    public function getTenantedOr($default_to_id, $condition = '', $params = array())
    {
        return array_merge([$this->findByPk($default_to_id)], $this->getTenanted($condition, $params));
    }

    public function getTenantedList($current_institution_only = true)
    {
        $result = array();

        if ($current_institution_only) {
            $current_institution = $this->getCurrent();
            $result[$current_institution->id] = $current_institution->name;
        } else {
            $cmd = Yii::app()->db->createCommand()
                ->selectDistinct('i.id, i.name')
                ->from('institution i')
                ->join('institution_authentication ia', 'ia.institution_id = i.id');

            foreach ($cmd->queryAll() as $institution) {
                $result[$institution['id']] = $institution['name'];
            }

            natcasesort($result);
        }

        return $result;
    }

    public function getCorrespondenceName()
    {
        return $this->name;
    }

    /**
     * Returns the name of the institution
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->short_name;
    }
}
