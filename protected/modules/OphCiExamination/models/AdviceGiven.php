<?php

namespace OEModule\OphCiExamination\models;

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 * Creation date: 30 September 2021
 * @package Clinical
 *
 * This is the model class for table "et_ophciexamination_advicegiven".
 *
 * The followings are the available columns in table 'et_ophciexamination_advicegiven':
 * @property string $id
 * @property string $event_id
 * @property string $created_user_id
 * @property string $created_date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $comments
 *
 * @property AdviceLeaflet[] $leaflets
 * @property AdviceLeafletEntry[] $leaflet_entries
 *
 */
class AdviceGiven extends \BaseEventTypeElement
{
    protected $widgetClass = \OEModule\OphCiExamination\widgets\AdviceGiven::class;

    /**
     * Returns the static model of the specified AR class.
     * @return AdviceGiven the static model class
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
        return 'et_ophciexamination_advicegiven';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // Only define rules for those attributes with user inputs.
        return array(
            array('comments', 'safe'),

            // Remove attributes that should not be searched.
            array('comments', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'leaflets' => array(
                self::MANY_MANY,
                AdviceLeaflet::class,
                'ophciexamination_advice_leaflet_entry(element_id, leaflet_id)',
                'order' => 'leaflets_leaflets.display_order'
            ),
            'leaflet_entries' => array(self::HAS_MANY, AdviceLeafletEntry::class, 'element_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'comment' => 'Comment',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('comment', $this->comments, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return string
     */
    public function getLetter_string(): string
    {
        $string = '';
        if ($this->comments && $this->comments !== '') {
            $string .= "$this->comments<br/>";
        }

        if (count($this->leaflets) > 0) {
            $leaflet_rows = '';
            foreach ($this->leaflets as $leaflet) {
                $leaflet_rows .= "<tr><td>$leaflet->name</td></tr>";
            }
            $string .= "I have provided copies of the following information resources: 
            <table>
            <tbody>
            $leaflet_rows
            </tbody>
            </table>";
        }
        return $string;
    }
}
