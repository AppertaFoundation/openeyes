<?php
use OE\factories\models\traits\HasFactory;
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
 * The followings are the available columns in table 'ophcocorrespondence_internal_referral_site_firm_mapping':.
 *
 * @property string $id
 * @property int $firm_id
 * @property int $site_id
 *
 * The followings are the available model relations:
 */
class InternalReferralSiteFirmMapping extends BaseActiveRecord
{
    use HasFactory;
    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     * @return FirmSiteSecretary the static model class
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
        return 'ophcocorrespondence_internal_referral_site_firm_mapping';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('firm_id, site_id', 'safe'),
            array('firm_id, site_id', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('firm_id, site_id', 'safe', 'on' => 'search'),
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
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @param $subspecialty_id
     * @param $site_id
     *
     * @return array
     */
    public static function findInternalReferralFirms($site_id, $subspecialty_id = null, $only_service_firms = false)
    {
        $command = Yii::app()->db->createCommand()
                                 ->selectDistinct('f.id, f.name, s.name AS subspecialty')
                                 ->from('firm f')
                                 ->join('ophcocorrespondence_internal_referral_site_firm_mapping sfm', 'sfm.firm_id = f.id')
                                 ->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
                                 ->join('subspecialty s', 'ssa.subspecialty_id = s.id')
                                 ->where('f.active = 1')
                                 ->andWhere('sfm.site_id = :site_id', [':site_id' => $site_id]);

        if ($subspecialty_id) {
            $command->andWhere('s.id = :id', array(':id' => $subspecialty_id));
        }

        if ($only_service_firms) {
            $command->andWhere('f.can_own_an_episode = 1');
        }

        $firms = $command->order('f.name, s.name')->queryAll();

        $data = array();

        foreach ($firms as $firm) {
            $display = $firm['name'];

            if ($firm['subspecialty']) {
                $display .= ' (' . $firm['subspecialty'] . ')';
            }

            $data[$firm['id']] = $display;
        }

        natcasesort($data);

        return $data;
    }
}
