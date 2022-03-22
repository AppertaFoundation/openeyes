<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "et_ophtroperationnote_revision_aqueous".
 *
 * The followings are the available columns in table 'et_ophtroperationnote_revision_aqueous':
 *
 * @property string $id
 * @property int $event_id
 * @property int $plate_pos_id
 * @property tinyint $is_shunt_explanted
 * @property int $final_tube_position_id
 * @property int $intraluminal_stent_id
 * @property tinyint $is_visco_in_ac
 * @property tinyint $is_flow_tested
 * @property text $comments
 * @property int $last_modified_user_id
 * @property datetime $last_modified_date
 * @property int $created_user_id
 * @property datetime $created_date
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class Element_OphTrOperationnote_RevisionAqueousShunt extends Element_OnDemand
{
    public $service;
// Plate position of revised tube
    const PLATE_POS_STQ = 1;
    const PLATE_POS_SNQ = 2;
    const PLATE_POS_INQ = 3;
    const PLATE_POS_ITQ = 4;
    const PLATE_POSITIONS = [
        self::PLATE_POS_STQ => 'STQ',
        self::PLATE_POS_SNQ => 'SNQ',
        self::PLATE_POS_INQ => 'INQ',
        self::PLATE_POS_ITQ => 'ITQ',
    ];
    //const TUBE_POS_NULL = 0;
    const TUBE_POS_AC = 1;
    const TUBE_POS_SULCUS = 2;
    const TUBE_POS_PARS_PLANA = 3;
    const TUBE_POSITIONS = [
        // self::TUBE_POS_NULL => '- SELECT -',
        self::TUBE_POS_AC => 'AC',
        self::TUBE_POS_SULCUS => 'Sulcus',
        self::TUBE_POS_PARS_PLANA => 'Pars Plana',
    ];
    const RIPCORD_SUTURE_NOT_MOD = 1;
    const RIPCORD_SUTURE_NEWLY_INS = 2;
    const RIPCORD_SUTURE_REMOVED = 3;
    const RIPCORD_SUTURE_ADJUSTED = 4;
    const RIPCORD_SUTURE_NO_RIPCORD = 5;
    const RIPCORD_SUTURES = [
        self::RIPCORD_SUTURE_NOT_MOD => 'Not modified',
        self::RIPCORD_SUTURE_NEWLY_INS => 'Newly inserted',
        self::RIPCORD_SUTURE_REMOVED => 'Removed',
        self::RIPCORD_SUTURE_ADJUSTED => 'Adjusted',
        self::RIPCORD_SUTURE_NO_RIPCORD => 'No intraluminal stent'
    ];

    /**
     * Return plate position of revised tube by id
     * @return string|null
     */
    public function getPlatePosById($id)
    {
        $plates = self::PLATE_POSITIONS;
        return $plates[$id] ?? null;
    }

    /**
     * Return final tube position by id
     * @return string|null
     */
    public function getTubePosById($id)
    {
        $tubes = self::TUBE_POSITIONS;
        return $tubes[$id] ?? null;
    }

    /**
     * Return intraluminal stent by id
     * @return string|null
     */
    public function getRipcordSutureById($id)
    {
        $intraluminal_stents = self::RIPCORD_SUTURES;
        return $intraluminal_stents[$id] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        // set model defaults
        if ($this->isNewRecord) {
            $this->plate_pos_id = self::PLATE_POS_STQ;
            $this->intraluminal_stent_id = self::RIPCORD_SUTURE_NOT_MOD;
        }
        parent::init();
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphTrOperationnote_RevisionAqueousShunt the static model class
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
        return 'et_ophtroperationnote_revision_aqueous';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id, plate_pos_id, is_shunt_explanted, final_tube_position_id, intraluminal_stent_id, is_visco_in_ac, is_flow_tested, comments,', 'safe'),
            array('comments', 'required'),
            array('id, event_id', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='" . get_class($this) . "'"),
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
            'plate_pos_id' => 'Plate position of revised tube',
            'is_shunt_explanted' => 'Shunt explanted',
            'final_tube_position_id' => 'Final tube position',
            'intraluminal_stent_id' => 'Intraluminal Stent',
            'is_visco_in_ac' => 'Visco in AC',
            'is_flow_tested' => 'Flow Tested',
            'comments' => 'Comments'
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
     * @inheritDoc
     */
    public function beforeSave()
    {
        if ($this->final_tube_position_id === '') {
            $this->final_tube_position_id = null;
        }

        return parent::beforeSave();
    }
}
