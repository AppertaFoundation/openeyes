<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "et_ophtroperationnote_glaucomatube".
 *
 * The followings are the available columns in table 'et_ophtroperationnote_glaucomatube':
 *
 * @property string $id
 * @property int $event_id
 * @property int $plate_position_id
 * @property int $plate_limbus
 * @property int $tube_position_id
 * @property bool $stent
 * @property bool $slit
 * @property bool $visco_in_ac
 * @property bool $flow_tested
 * @property json $eyedraw
 * @property string $description
 * @property int $last_modified_user_id
 * @property datetime $last_modified_date
 * @property int $created_user_id
 * @property datetime $created_date
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property OphTrOperationnote_GlaucomaTube_PlatePosition $plate_position
 * @property OphTrOperationnote_GlaucomaTube_TubePosition $tube_position
 */
class Element_OphTrOperationnote_GlaucomaTube extends Element_OnDemand
{
    public $service;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphTrOperationnote_GlaucomaTube the static model class
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
        return 'et_ophtroperationnote_glaucomatube';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('plate_position_id, plate_limbus, tube_position_id, stent, slit, visco_in_ac, flow_tested,
					eyedraw, description', 'required'),
                array('plate_limbus', 'numerical', 'integerOnly' => true, 'min' => 2, 'max' => 15,
                        'tooSmall' => '{attribute} cannot be smaller than 2mm', 'tooBig' => '{attribute} cannot be more than 15mm', ),
                array('event_id, plate_position_id, plate_limbus, tube_position_id, stent, slit, visco_in_ac,
				flow_tested, eyedraw, description', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
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
                'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'plate_position' => array(self::BELONGS_TO, 'OphTrOperationnote_GlaucomaTube_PlatePosition', 'plate_position_id'),
                'tube_position' => array(self::BELONGS_TO, 'OphTrOperationnote_GlaucomaTube_TubePosition', 'tube_position_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'plate_position_id' => 'Plate postiion',
            'tube_position_id' => 'Tube position',
            'visco_in_ac' => 'Visco in AC',
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

    public function getPrefillableAttributeSet()
    {
        return [
            'plate_position_id',
            'plate_limbus',
            'tube_position_id',
            'stent',
            'slit',
            'visco_in_ac',
            'flow_tested',
            'eyedraw',
            'description'
        ];
    }
}
