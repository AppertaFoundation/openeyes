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
 * This is the model class for table "unique_codes".
 *
 * The followings are the available columns in table 'unique_codes':
 *
 * @property int $id
 * @property string $name
 */
class UniqueCodes extends BaseActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return UniqueCodes the static model class
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
        return 'unique_codes';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.code');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('code, active', 'safe'),
            array('code', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, code', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(

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
        $criteria->compare('code', $this->code, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @param $code
     * @return Event|null
     * @throws CException
     */
    public function eventFromUniqueCode($code)
    {
        $event_id = $this->dbConnection->createCommand()
            ->select('event.id')
            ->from('unique_codes')
            ->join('unique_codes_mapping', 'unique_codes.id = unique_codes_mapping.unique_code_id')
            ->join('event', 'unique_codes_mapping.event_id = event.id')
            ->where('unique_codes.code = ? ', array($code))
            ->queryRow();

        if ($event_id) {
            return Event::model()->findByPk($event_id['id']);
        }

        return null;
    }

    /**
     * @param $code
     * @return mixed
     */
    public function examinationEventCheckFromUniqueCode($code)
    {
        $logs = $this->dbConnection->createCommand()
                                ->select('automatic_examination_event_log.*')
                                ->from('automatic_examination_event_log')
                                ->join('event', 'event.id = automatic_examination_event_log.event_id and event.deleted <> 1')
                                ->join('import_status', 'import_status.id = automatic_examination_event_log.import_success')
                                ->where('unique_code = ? ', array($code))
                                ->andWhere('import_status.status_value <> "Import Failure"')
                                ->order('created_date ASC')
                                ->queryAll();

        $count = count($logs);
        $log = array_pop($logs);
        $log['count'] = $count;

        return $log;
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getEpisodeIdFromCode($code)
    {
        $episodeId = $this->dbConnection->createCommand()
                        ->select('episode_id')
                        ->from('event')
                        ->join('unique_codes_mapping ucm', 'ucm.event_id = event.id')
                        ->join('unique_codes uc', "ucm.unique_code_id = uc.id and uc.code ='$code'")
                        ->queryRow();

        return $episodeId;
    }

    /**
     * @param $episode_id
     * @param $event_type_id
     * @return mixed
     */
    public function getEventFromEpisode($episode_id, $event_type_id)
    {
        $event_id = $this->dbConnection->createCommand()
                                ->select('id')
                                ->from('event')
                                ->where('episode_id = ? ', array($episode_id))
                                ->andWhere('event_type_id = '.$event_type_id)
                                ->andWhere('deleted != 1')
                                ->order('id desc')
                                ->limit(1)
                                ->queryRow();

        return $event_id;
    }

    /**
     * @param $id
     * @return string
     */
    public static function codeForEventId($id)
    {
        if (!empty($id)) {
            return Yii::app()->db->createCommand()
                         ->select('uc.code')
                         ->from('unique_codes uc')
                         ->join('unique_codes_mapping ucm', 'uc.id = ucm.unique_code_id')
                         ->where("ucm.event_id = $id")->queryScalar();
        }

        return '';
    }
}
